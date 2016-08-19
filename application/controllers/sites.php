<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 2/16/16
 * Time: 5:58 PM
 */
class Sites extends CI_Controller {

    protected $admin_id;

    function __construct() {
        parent::__construct();
        $this->users_model->security();
        $this->load->model('aps_model', 'aps');
        $this->admin_id = $this->users_model->user()->user_id;
    }

    function register() {
        $this->validation();
        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $data = array(
                'site_name' => $this->input->post('site_name'),
                'site_zone' => $this->input->post('site_zone'),
                'site_email' => $this->input->post('site_email'),
                'site_geo_location' => $this->input->post('site_geo_location'),
                'site_added_by' => $this->admin_id,
                'site_about' => $this->input->post('site_about'),
                //  'site_status' => $this->input->post('site_status'),
                'site_category' => $this->input->post('category')
            );


            $site_id = $this->crud_model->add_record('sites', $data);
            if ($site_id) {
                $this->sites_model->log($site_id, "Registered into the system");
                $this->session->set_flashdata('success', 'Registered site successfully');
                //todo: notify managers
                redirect('sites/profile/' . urlencode(base64_encode($site_id)), 'refresh');
            }
        }
    }

    function validation() {
        $this->form_validation->set_rules('site_name', 'Site Name', 'trim|required');
        $this->form_validation->set_rules('site_zone', 'Zone', 'trim|required');
        $this->form_validation->set_rules('site_status', 'Site status', 'trim');
        $this->form_validation->set_rules('site_email', 'Site email address', 'valid_email|required');
//        $this->form_validation->set_rules('category', 'Site location', 'required');
        $this->form_validation->set_rules('site_geo_location', 'Geo location coordinates', 'required');
        $this->form_validation->set_rules('site_about', 'Site description', 'required');
    }

    function create() {
        $var['page'] = 'Register a Site';
        $this->load->template('new_site', $var);
    }

    function map() {
        $var['page'] = 'Sites on a map';
        $var['scripts'] = array(
            'plugins/google-map/google-map-script'
        );
        $this->load->template('map_view', $var);
    }

    function update() {
        $this->validation();
        $site_id = $this->input->post('site_id');
        if ($this->form_validation->run() == FALSE) {
            $this->profile(urlencode(base64_encode($site_id)));
        } else {
            $data = array(
                'site_name' => $this->input->post('site_name'),
                'site_zone' => $this->input->post('site_zone'),
                'site_email' => $this->input->post('site_email'),
                'site_geo_location' => $this->input->post('site_geo_location'),
                'site_added_by' => $this->admin_id,
                'site_about' => $this->input->post('site_about'),
                'site_status' => $this->input->post('site_status')
//                'category' => $this->input->post('site_location_name')
            );
            if ($this->crud_model->update_record('sites', 'site_id', $site_id, $data)) {
                $this->sites_model->log($site_id, 'updated details');
                $this->session->set_flashdata('success', 'Site updated successfully');
                redirect('sites/profile/' . urlencode(base64_encode($site_id)), 'refresh');
            }
        }
    }

    function install($site_id = false) {
        if (!$site_id) {
            $var['projects'] = $this->crud_model->get_records('projects', 'project_stage', 'INSTALLATION');
            $var['page'] = 'Projects pending installation';
            $this->load->template('installation_projects_view', $var);
            return;
        }
        if (!$site = $this->crud_model->get_record('projects', 'project_id', base64_decode(urldecode($site_id)))) {
            redirect('unknown', 'refresh');
            return;
        }
        $var['page'] = 'Project Installation';
        $var['details'] = $site;
        $this->load->template('install_project', $var);
    }

    function trash($site_id, $restore = false) {
        $this->db->trans_start();
        $this->crud_model->update_record("sites", "site_id", base64_decode(urldecode($site_id)), array("deleted" => $restore));
        $this->sites_model->log(base64_decode(urldecode($site_id)), !$restore ? "Recovered from trash" : "Trashed");
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            if (!$restore) {
                $this->log_model->log('Recovered site No. ' . base64_decode(urldecode($site_id)));
                $this->session->set_flashdata('success', "Site recovered Successfully");
            } else {
                $this->log_model->log('Trashed site No. ' . base64_decode(urldecode($site_id)));
                $this->session->set_flashdata('success', "Site trashed Successfully");
            }
        } else {
            echo 'Failed';
        }
    }

    function manager() {
        $site_id = $this->input->post("site_id");
        $this->db->trans_start();
        $this->crud_model->update_record("site_manager", "site_id", $site_id, array("check_out" => date("Y-m-d H:m")));
        $this->crud_model->add_record("site_manager", array("staff_id" => $this->input->post('manager'),
            "site_id" => $site_id,
            "check_in" => $this->input->post('check_in'),
            "assigned_by" => $this->admin_id,
            "instructions" => $this->input->post('instructions')));
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
//todo: notify manger
        }
        redirect("sites/profile/" . urlencode(base64_encode($site_id)));
    }

    /*     * ************************************SITES******************************************************* */

    function index() {
        $var['page'] = 'Sites Library';
        $var['sites'] = $this->crud_model->get_records('sites');

        $var['trashed'] = $this->crud_model->get_trash('sites');
        $var['coming_soon'] = $this->crud_model->get_records('sites', 'site_status', 3);
        $this->load->template('sites_view', $var);
    }

    function profile($site_id) {
        $site_id = base64_decode(urldecode($site_id));
        if (!$site = $this->crud_model->get_record('sites', 'site_id', $site_id)) {
            redirect('unkown', 'refresh');
            return;
        }
        $var['page'] = 'Site Profile';
        $var['details'] = $site;
        $var['aps'] = $this->aps->get_aps($site_id);
        $var['tickets'] = $this->crud_model->get_records('tickets', 'site_id', $site_id);
        $var['equipments'] = $this->sites_model->site_equipment($site_id);
        $var['supervisors'] = $this->sites_model->site_supervisors($site_id);
        $this->load->template('site_profile', $var);
    }

    function add_aps() {
        //      
        $this->form_validation->set_rules('site_id', 'Site Id', 'trim|required');
        $this->form_validation->set_rules('aps_no', 'Aps No', 'trim|required');
        $this->form_validation->set_rules('code_name', 'Code Name', 'trim|required');
        $this->form_validation->set_rules('ip_address', 'IP Address', 'trim|required');
        $this->form_validation->set_rules('aps_os', 'OS', 'trim|required');
        $this->form_validation->set_rules('aps_status', 'Status', 'trim|required');
        $this->form_validation->set_rules('aps_comments', 'Comments', 'trim');

        if ($this->form_validation->run() === FALSE) {
            $this->index();
        } else {
            $site_id = $this->input->post('site_id');
            $d = array(
                'site_id' => $this->input->post('site_id'),
                'aps_no' => $this->input->post('aps_no'),
                'code_name' => $this->input->post('code_name'),
                'ip_address' => $this->input->post('ip_address'),
                'status' => $this->input->post('aps_status'),
                'comments' => $this->input->post('aps_comments'),
                'os' => $this->input->post('aps_os'),
                'created_by' => $this->admin_id
            );

            $aps_id = $this->aps->insert_aps($d);
            if ($aps_id) {
                $this->sites_model->log($site_id, 'add aps to site');
                $this->session->set_flashdata('success', 'Aps added successfully');
                redirect('sites/profile/' . urlencode(base64_encode($site_id)), 'refresh');
            } else {
                $this->profile(urlencode(base64_encode($site_id)));
            }
        }
    }

}

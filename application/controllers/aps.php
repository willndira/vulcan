<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class aps extends CI_Controller {

    protected $admin_id;

    public function __construct() {
        parent::__construct();
        $this->users_model->security();
        $this->load->model('aps_model', 'aps');
        $this->admin_id = $this->users_model->user()->user_id;
    }

    function index() {
        $var['page'] = 'Aps Library';
        $var['aps'] = $this->aps->get_all_aps();
        $var['sites'] = $this->crud_model->get_records('sites');
        $var['trashed'] = $this->crud_model->get_trash('aps');
        $var['coming_soon'] = $this->crud_model->get_records('sites', 'site_status', 3);
        $this->load->template('aps_view', $var);
    }

    function profile($ap_id = FALSE) {
        $aps_id = base64_decode(urldecode($ap_id));
        if (!$aps = $this->crud_model->get_record('aps', 'aps_id', $aps_id)) {
            redirect('unkown', 'refresh');
            return;
        }
        $var['page'] = 'APS Profile';
        $var['details'] = $aps;
        $var['sites'] = $this->crud_model->get_records('sites');
        $var['tickets'] = $this->crud_model->get_records('tickets', 'site_id', $aps->site_id);
        $var['equipments'] = $this->sites_model->site_equipment($aps->site_id);
        $var['supervisors'] = $this->sites_model->site_supervisors($aps->site_id);
        $this->load->template('aps_profile', $var);
    }

    function register() {

        //-----------------------------------------------------------------------------------------
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
            $aps_is = $this->aps->insert_aps($d);
            if ($aps_is) {
                $this->sites_model->log($site_id, 'add aps to site');
                $this->session->set_flashdata('success', 'Aps added successfully');
                redirect('aps/profile/' . urlencode(base64_encode($aps_is)), 'refresh');
            } else {
                $this->index();
            }
        }
    }

    function trash($aps_id, $restore = false) {
        $this->db->trans_start();
        $this->crud_model->update_record("aps", "aps_id", base64_decode(urldecode($aps_id)), array("deleted" => $restore));
        $this->sites_model->log(base64_decode(urldecode($aps_id)), !$restore ? "Recovered from trash" : "Trashed");
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            if (!$restore) {
                $this->log_model->log('Recovered aps No. ' . base64_decode(urldecode($aps_id)));
                $this->session->set_flashdata('success', "Site recovered Successfully");
            } else {
                $this->log_model->log('Trashed aps No. ' . base64_decode(urldecode($aps_id)));
                $this->session->set_flashdata('success', "Site trashed Successfully");
            }
        } else {
            echo 'Failed';
        }
    }

    function update() {

        $this->form_validation->set_rules('aps_id', 'Aps Id', 'trim|required');
        $this->form_validation->set_rules('site_id', 'Site Id', 'trim|required');
        $this->form_validation->set_rules('aps_no', 'Aps No', 'trim|required');
        $this->form_validation->set_rules('code_name', 'Code Name', 'trim|required');
        $this->form_validation->set_rules('ip_address', 'Ip Address', 'trim|required');
        $this->form_validation->set_rules('aps_os', 'OS', 'trim|required');
        $this->form_validation->set_rules('aps_status', 'Status', 'trim|required');
        $this->form_validation->set_rules('aps_comments', 'Comments', 'trim');

        if ($this->form_validation->run() === FALSE) {
            $this->index();
        } else {
            //aps_id aps_no code_name ip_address aps_os aps_status site_id aps_comments
            $aps_id = $this->input->post('aps_id');
            $data = array(
                'site_id' => $this->input->post('site_id'),
                'aps_no' => $this->input->post('aps_no'),
                'code_name' => $this->input->post('code_name'),
                'ip_address' => $this->input->post('ip_address'),
                'status' => $this->input->post('aps_status'),
                'comments' => $this->input->post('aps_comments'),
                'os' => $this->input->post('aps_os'),
                'created_by' => $this->admin_id
            );

            if ($this->crud_model->update_record('aps', 'aps_id', $aps_id, $data)) {
                $this->sites_model->log($this->input->post('site_id'), 'updated details');
                $this->session->set_flashdata('success', 'Aps updated successfully');
                redirect('aps/profile/' . urlencode(base64_encode($aps_id)), 'refresh');
            }
        }
    }

}

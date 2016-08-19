<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 2/22/16
 * Time: 3:32 PM
 */
class Technicians extends CI_Controller
{
    private $admin_id;

    function __construct()
    {
        parent::__construct();
        $this->users_model->security("man_technicians");
        $this->admin_id = $this->users_model->user()->user_id;
    }

    function register()
    {
        $this->validate();
        if ($this->form_validation->run() == FALSE) {
            $this->index();
        } else {
            $data = array(
                'user_id' => $this->input->post('user_id'),
                'device_id' => $this->input->post('device_imei'),
                'reg_by' => $this->admin_id
            );

            $this->db->trans_start();
            $technician_id = $this->crud_model->add_record('technicians', $data);
            foreach ($this->input->post('specialisation') as $special) {
                $this->crud_model->add_record('tech_specialisation', array("technician_id" => $technician_id, "specialisation_id" => $special));
            }
            $this->db->trans_complete();
            if ($this->db->trans_status()) {
                $this->log_model->log('Registered technician ' . $this->users_model->user($this->input->post('user_id'))->user_name . ' with phone IMEI No. ' . $this->input->post('device_imei'));
                $this->session->set_flashdata('success', 'Technician registered successfully.');
            }
            $data = array(
                'header' => 'Technician registration',
                'message' => 'You were registered as a technician and your device IMEI No. ' . $this->input->post('device_imei')
                    . ' has been registered by ' . $this->users_model->user()->user_name
            );
            $this->users_model->notify(array($this->input->post('user_id')), $data);

            redirect('technicians', 'refresh');
        }
    }

    function validate()
    {
        $this->form_validation->set_rules('specialisation', 'Technician specialisation', 'required');
        $this->form_validation->set_rules('device_imei', 'IMEI Number', 'trim|required');
        $this->form_validation->set_rules('user_id', 'Staff ', 'numeric|required');
    }

    function index()
    {
        $var['page'] = 'Technicians';
//        $var['scripts'] = array(
//            'plugins/google-map/tech-map-script'
//        );
        $this->load->template('technicians_view', $var);
    }

    function profile($technician_id)
    {
        $details = $this->crud_model->get_record("technicians", 'technician_id', base64_decode(urldecode($technician_id)));
        if (count($details) == 0) {
            $this->index();
            return;
        }
        $var['page'] = 'Technicians profile';
        $var['details'] = $details;
        $var['staff_details'] = $this->crud_model->get_record("users", "user_id", $details->user_id);
        $this->load->template('technicians_profile', $var);
    }

    function update()
    {
        $this->crud_model->update_record("technicians", "technician_id", $_POST['technician_id'], array("device_id" => $_POST['device_imei']));

        redirect('technicians/profile/' . urlencode(base64_encode($_POST['technician_id'])), 'refresh');
    }

    function trash($technician_id, $deleted)
    {
        $this->crud_model->update_record("technicians", "technician_id", base64_decode(urldecode($technician_id)), array("deleted" => $deleted));
    }
}
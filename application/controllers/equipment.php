<?php

/**
 * Created by PhpStorm.
 * User: mayne
 * Date: 3/17/16
 * Time: 12:04 PM
 */
class Equipment extends CI_Controller
{
    protected $admin_id;

    function __construct()
    {
        parent::__construct();
        $this->users_model->security();
        $this->admin_id = $this->users_model->user()->user_id;
    }

    function profile($equipment_id)
    {
        $equipment = $this->crud_model->get_record('equipment', 'equipment_id', base64_decode(urldecode($equipment_id)));
        if (!$equipment || !$equipment_id) {
            redirect('equipment', 'refresh');
            return;
        }
        $var['page'] = 'Equipment No ' . $equipment->equipment_no . ' Profile';
        $var['details'] = $equipment;
        $this->load->template('equipment_profile', $var);
    }

    function register()
    {
        $this->form_validation->set_rules('component_id', 'Equipment category', 'trim|required');
        $this->form_validation->set_rules('eq_no', 'Equipment no.', 'trim|required|callback_verify_eq_no');
        $this->form_validation->set_rules('equipment_availability', 'Equipment availability', 'trim|required');
        $this->form_validation->set_rules('equipment_condition', 'Equipment condition', 'trim|required');
        $this->form_validation->set_rules('equipment_comment', 'Equipment comment', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->index();
            return;
        }
        $data = array(
            "component_id" => $this->input->post("component_id"),
            "equipment_no" => $this->input->post("eq_no"),
            "equipment_comment" => $this->input->post("equipment_comment"),
            "registering_user" => $this->admin_id,
            "equipment_availability" => $this->input->post("equipment_availability"),
            "equipment_condition" => $this->input->post("equipment_condition"),
            "equipment_stage" => 0,
        );
        $this->equipment_model->register_equipment($data);
        $this->log_model->log('Registered equipment No. ' . $this->input->post("eq_no"));
        $this->session->set_flashdata('success', 'Equipment registered successfully');
        redirect("equipment", "refresh");
    }

    function index()
    {
        $var['page'] = 'Equipment';
        $this->load->template('equipments_view', $var);
    }

    function verify_eq_no($eq_no)
    {
        if (count($this->crud_model->get_record("equipment", "equipment_id", $eq_no)) > 0) {
            $this->form_validation->set_message('verify_eq_no', 'Equipment No. given is seems to exists');
            return false;
        }
        return true;
    }

    function complete($assembly_id)
    {
        $schedule = $this->crud_model->get_record("assembly_schedule", "assembly_schedule_id", base64_decode(urldecode($assembly_id)));
        if (is_null($this->input->post("report")) || is_null($schedule)) {
            $this->session->set_flashdata('error', 'Kindly include report for the task completion');
            redirect("assembly/process/" . urlencode(base64_encode($schedule->equipment_assembly_id)));
            return;
        }
        $this->crud_model->update_record("assembly_schedule", "assembly_schedule_id",
            base64_decode(urldecode($assembly_id)), array("report" => $this->input->post("report"), "complete_date" => date("Y-m-d H:m:s"), "schedule_stage" => 2));
        $this->session->set_flashdata('success', 'Task marked as complete');
        redirect($this->input->post("redirect") . urlencode(base64_encode($schedule->equipment_assembly_id)));
    }

    function test_report($equipment_assembly_id)
    {
        $var['page'] = "Tests report";
        $var['details'] = $this->crud_model->get_record('equipment_assembly', "equipment_assembly_id", base64_decode(urldecode($equipment_assembly_id)));
        $this->load->template('test_report', $var);
    }

    function install($equipment_assembly_id)
    {
        $var['details'] = $this->crud_model->get_record('equipment_assembly', "equipment_assembly_id", base64_decode(urldecode($equipment_assembly_id)));
        $var['page'] = 'Equipment installation';
        $this->load->template('equipment_installation', $var);

    }

}
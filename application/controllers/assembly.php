<?php

/**
 * Created by PhpStorm.
 * User: mayne
 * Date: 3/12/16
 * Time: 8:31 AM
 */
class Assembly extends CI_Controller
{
    private $admin_id;

    function __construct()
    {
        parent::__construct();
        $this->users_model->security();
        $this->admin_id = $this->users_model->user()->user_id;
    }

    function new_task($eq_assembly)
    {
        $assembly_details = $this->crud_model->get_record("equipment_assembly", "equipment_assembly_id", base64_decode(urldecode($eq_assembly)));
        $this->form_validation->set_rules('title', 'Task title', 'trim|required');
        $this->form_validation->set_rules('sdate', 'Start date', 'trim|required');
        $this->form_validation->set_rules('edate', 'expected due date', 'trim|required');
        $this->form_validation->set_rules('assigned_staff', 'Assigned staff', 'required');
        $this->form_validation->set_rules('instructions', 'Task instructions', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->input->post("redirect") == "/assembly/process/" ? $this->process($eq_assembly) : $this->test("one", $eq_assembly);
        } else {
            $this->db->trans_start();
            $schedule_id = $this->crud_model->add_record("assembly_schedule", array(
                "equipment_assembly_id" => base64_decode(urldecode($eq_assembly)),
                "create_staff" => $this->admin_id,
                "expected_start_date" => $this->input->post("sdate"),
                "schedule_title" => $this->input->post("title"),
                "expected_end_date" => $this->input->post("edate"),
                "schedule_comment" => $this->input->post("instructions")
            ));
            if ($this->input->post("assigned_staff") && !is_null($staffs = $this->input->post("assigned_staff"))) {
                foreach ($staffs as $staff) {
                    $this->crud_model->add_record("assembly_team", array(
                        "user_id" => $staff,
                        "added_by" => $this->admin_id,
                        "assembly_schedule_id" => $schedule_id
                    ));
                }
            }
            if ($this->input->post("predecessor") && !is_null($predecessors = $this->input->post("predecessor"))) {
                foreach ($predecessors as $predecessor) {
                    $this->crud_model->add_record("schedule_predicessor", array(
                        "schedule_id" => $schedule_id,
                        "predicessor_id" => $predecessor
                    ));
                }
            }
            if (!is_null($steps = $this->input->post("required_steps"))) {
                foreach ($steps as $step) {
                    $this->crud_model->add_record("assembly_schedule_steps", array(
                        "setup_guide_id" => $step,
                        "schedule_id" => $schedule_id
                    ));
                }
            }
            $this->db->trans_complete();
            if ($this->db->trans_status()) {
                $this->session->set_flashdata('success', 'Task created successfully');
                $this->equipment_model->log($assembly_details->equipment_id, 'Assembly task ' . $this->input->post("title") . " created");
                $data = array(
                    'header' => 'Assembly task assignment',
                    'message' => "You have an equipment assembly management task assigned to you by " . $this->users_model->user()->user_name . ' for Equipment '
                        . $eq_no = $this->equipment_model->details($assembly_details->equipment_id)->equipment_no . ". <br/>You will be expected to perform and report on progress of task within the stipulated duration");
                $this->users_model->notify($this->input->post("assigned_staff"), $data);
            }
            redirect($this->input->post("redirect") . $eq_assembly, "refresh");
        }
    }

    function process($equipment_assembly_id)
    {
        $var['page'] = 'Equipment Assembly';
        $var['details'] = $this->crud_model->get_record('equipment_assembly', "equipment_assembly_id", base64_decode(urldecode($equipment_assembly_id)));
        $this->load_page('equipment_assembly_profile', $var);
    }

    function load_page($page, $var)
    {
        /* $var['styles'] = array(
             'js/plugins/data-tables/css/jquery.dataTables.min'
         );
         $var['scripts'] = array(
             'plugins/data-tables/js/jquery.dataTables.min',
             'plugins/data-tables/data-tables-script'
         );*/
        $this->load->template($page, $var);
    }

    function test($level, $equipment_assembly_id)
    {
        $var['page'] = 'Equipment ' . ($level == "one" ? "assembly" : "installation") . ' testing';
        $var['details'] = $this->crud_model->get_record('equipment_assembly', "equipment_assembly_id", base64_decode(urldecode($equipment_assembly_id)));
        $var['test_stage'] = $level == "one" ? 1 : 2;
        $this->load->template('equipment_assembly_testing', $var);
    }

    function start()
    {
        $this->form_validation->set_rules('equipment_id', 'Equipment', 'trim|required');
        $this->form_validation->set_rules('assembly_manager', 'Assembly manager', 'trim|required');
        $this->form_validation->set_rules('assembly_priority', 'Assembly priority', 'trim|required');
        $this->form_validation->set_rules('equipment_comment', 'assembly comment', 'trim|required');
        $this->form_validation->set_rules('stage', 'assembly stage', 'trim|required');
        $stage = $this->input->post("stage");
        if ($this->form_validation->run() == FALSE) {
            $this->processing(urlencode(base64_encode($stage)));
        } else {
            $this->db->trans_start();
            $data = array(
                "equipment_id" => ($eq_id = $this->input->post("equipment_id")),
                "priority" => $this->input->post("assembly_priority"),
                "stage" => $this->input->post("stage"),
                "assembly_manager" => $this->input->post("assembly_manager")
            );
            $stage_name = $stage == 1 ? "Assembly" : ($stage == 2 ? "assembly testing" : ($stage == 3 ? "installation" : "installation testing"));
            $this->crud_model->add_record("equipment_assembly", $data);
            $equipment = $this->crud_model->get_record("equipment", "equipment_id", $eq_id);
            $this->crud_model->update_record("equipment", "equipment_id", $eq_id, array("equipment_stage" => ($equipment->equipment_stage + 1)));
            $this->equipment_model->log($eq_id, $stage_name . " initialized");
            $this->db->trans_complete();
            if ($this->db->trans_status()) {
                $this->session->set_flashdata('success', 'Equipment ' . $stage_name . ' started successfully');
                $data = array(
                    'header' => $stage_name . ' manager assignment',
                    'message' => "You have a new equipment " . $stage_name . " management task assigned to you by " . $this->users_model->user()->user_name . ' for Equipment '
                        . $this->input->post("eq_no") . ". <br/>You will be expected to manage and report on progress of " . $stage_name . " and assign roles to
                        other members of the team");
                $this->users_model->notify(array($this->input->post("assembly_manager")), $data);
            }
            redirect("assembly/processing/" . urlencode(base64_encode($stage)), "refresh");
        }
    }

    function processing($stage)
    {
        $stage = base64_decode(urldecode($stage));
        $var['page'] = ($stage == 1) ? "assembly" : (($stage == 2) ? "assembly testing" : (($stage == 3) ? "installation" : "installation testing"));
        $var['role'] = ($stage == 1) ? "man_assembly" : (($stage == 3) ? "install_equipment" : "test_equipment");
        $var['required_role'] = ($stage <= 2) ? "start_assembly" : "insProj";
        $var['url'] = ($stage == 1) ? 'assembly/process/' : ($stage == 2 ? "assembly/test/one/" : ($stage == 3 ? "equipment/install/" : "assembly/test/two/"));
        $var['stage'] = $stage;
        $var['equipments'] = $this->assembly_model->processing($stage);
        $this->load->template('start_assembly', $var);
    }

    function index()
    {
        $this->processing(urlencode(base64_encode(1)));
    }
//
//    function check_assembly($equipment_id)
//    {
//        if (count($this->crud_model->get_record("equipment_assembly", "equipment_id", $equipment_id)) > 0) {
//            $this->form_validation->set_message("check_assembly" . "This equipment assembly process was already initiated");
//            return false;
//        }
//        return true;
//    }

    function report($equipment_assembly_id, $installation = false)
    {
        $var['page'] = (!$installation ? "Assembly" : "Installation");
        $var['details'] = $this->crud_model->get_record('equipment_assembly', "equipment_assembly_id", base64_decode(urldecode($equipment_assembly_id)));
        $this->load->template('assembly_report', $var);
    }

    function team()
    {
        $this->form_validation->set_rules('equipment_assembly_id', 'Equipment', 'trim|required');
        $this->form_validation->set_rules('members', 'Members  ', 'required');
        $this->form_validation->set_rules('member_role', 'Roles ', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->process($this->input->post("equipment_assembly_id"));
        } else {
            foreach ($this->input->post("members") as $user) {
                $team = array(
                    "user_id" => $user,
                    "equipment_assembly_id" => base64_decode(urldecode($this->input->post("equipment_assembly_id"))),
                    "assembly_role" => $this->input->post("member_role")
                );
                $this->crud_model->add_record("equipment_assembly_team", $team);
            }
            $data = array(
                'header' => 'Equipment assembly assignment',
                'message' => $this->users_model->user()->user_name . ' has assigned you an assembly task (' . $this->input->post("member_role")
                    . ") due on " . $this->input->post("ddate"));
            $this->users_model->notify($this->input->post("members"), $data);
            $this->log_model->log('Added team members for equipment assembly process ' . base64_decode(urldecode($this->input->post("equipment_assembly_id"))));
            $this->session->set_flashdata('success', 'Team registered and notified successfully');
            redirect("assembly/process/" . $this->input->post("equipment_assembly_id"), "refresh");
        }
    }

    function change_manager($assembly_id)
    {
        if (null == $this->input->post('assembly_manager')) {
            $this->session->set_flashdata('error', 'Assembly manager needs to be specified');
            redirect('assembly/process/' . $assembly_id, 'refresh');
        }
        $assembly = $this->crud_model->get_record("equipment_assembly", "equipment_assembly_id", base64_decode(urldecode($assembly_id)));
        $this->crud_model->update_record("equipment_assembly", "equipment_assembly_id", $assembly->equipment_assembly_id,
            array("assembly_manager" => $this->input->post('assembly_manager')));
        if ($assembly->assembly_manager > 0)
            $this->equipment_model->log($assembly->equipment_id, "Assembly manager changed from <b>" . $this->users_model->user($assembly->assembly_manager)->user_name . " to " .
                $this->users_model->user($this->input->post('assembly_manager'))->user_name . "</b>");
        else
            $this->equipment_model->log($assembly->assembly_manager, "New assembly manager assigned: <b>" . $this->users_model->user($this->input->post('assembly_manager'))->user_name . "</b>");
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $this->log_model->log('Changed assembly manager for equipment: ' . $this->equipment_model->details($assembly->equipment_id)->equipment_no);
            $this->session->set_flashdata('success', 'assembly manager updated successfully');
        }
        redirect('assembly/process/' . $assembly_id, 'refresh');

    }
}
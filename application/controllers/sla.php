<?php

/**
 * Created by PhpStorm.
 * User: mayne
 * Date: 5/20/16
 * Time: 5:06 AM
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sla extends CI_Controller
{
    private $admin_id;

    function __construct()
    {
        parent::__construct();
        $this->users_model->security();
        $this->admin_id = $this->users_model->user()->user_id;
    }

    function index()
    {
        $var['page'] = 'SLA Settings';
        $this->load->template('sla_view', $var);
    }

    function profile($sla_id)
    {
        $real_sla_id = base64_decode(urldecode($sla_id));
        $sla = $this->sites_model->sla($real_sla_id);
        if (count($sla) == 0) {
            $this->session->set_flashdata('error', 'SLA level selected does not exist');
            redirect("sla", "refresh");
        }
        $var['details'] = $sla;
        $var['escalations'] = $this->sites_model->sla_escalation_levels($real_sla_id);
        $var['del_escalations'] = $this->sites_model->sla_escalation_levels($real_sla_id, 1);
        $var['tickets'] = $this->sites_model->sla_tickets($real_sla_id);
        $var['page'] = $sla->sla_name;
        $this->load->template('sla_profile', $var);
    }

    function add()
    {
        $this->form_validation->set_rules('sla_name', 'SLA name', 'trim|required');
        $this->form_validation->set_rules('sla_description', 'SLA description', 'trim|required');
        $this->form_validation->set_rules('preventative', 'Preventative wait', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->index();
        } else {
            $this->db->trans_start();
            $sla_id = $this->crud_model->add_record('sla', array(
                'sla_name' => $this->input->post('sla_name'),
                'preventative_after' => $this->input->post('preventative'),
                'reg_staff' => $this->admin_id,
                'sla_description' => $this->input->post("sla_description")
            ));
            $this->db->trans_complete();
            if ($this->db->trans_status()) {
                $this->log_model->log('Registered SLA level ' . $this->input->post('sla_name'));
                $this->session->set_flashdata('success', 'SLA level registered successfully');
            }
            redirect("sla", "refresh");
        }
    }

    function update()
    {
        $this->form_validation->set_rules('sla_name', 'SLA name', 'trim|required');
        $this->form_validation->set_rules('sla_description', 'SLA description', 'trim|required');
        $this->form_validation->set_rules('preventative', 'Preventative wait', 'trim|required');
        $sla_id = $_POST['sla_id'];
        if ($this->form_validation->run() == FALSE) {
            $this->index();
        } else {
            $this->db->trans_start();
            $this->crud_model->update_record('sla', "sla_id", $sla_id, array(
                'sla_name' => $this->input->post('sla_name'),
                'preventative_after' => $this->input->post('preventative'),
                'sla_description' => $this->input->post("sla_description")
            ));
            $this->db->trans_complete();
            if ($this->db->trans_status()) {
                $this->log_model->log('Edited SLA level ' . $this->input->post('sla_name'));
                $this->session->set_flashdata('success', 'SLA level edited successfully');
            }
            redirect("sla/profile/" . urlencode(base64_encode($sla_id)), "refresh");
        }
    }

    function trash($sla_id)
    {
        $real_sla_id = base64_decode(urldecode($sla_id));
        $sla = $this->sites_model->sla($real_sla_id);
        if (count($sla) == 0) {
            $this->session->set_flashdata('error', 'SLA category level selected does not exist');
            redirect("sla", "refresh");
            return;
        }
        $key_word = !$sla->deleted ? "Trashed" : "Recovered";
        $this->crud_model->update_record("sla", "sla_id", $sla->sla_id, array("deleted" => !$sla->deleted));
        $this->log_model->log($key_word . ' SLA category ' . $sla->sla_name);
        $this->session->set_flashdata('success', 'SLA category ' . $key_word . ' successfully');
//        redirect("sla/profile/" . urlencode(base64_encode($sla->sla_id)), "refresh");
    }


    //SLA escalation management
    function new_escalation()
    {
        $this->form_validation->set_rules('sla_id', 'SLA name', 'trim|required');
        $this->form_validation->set_rules('user_category_id', 'Escation to', 'trim|required');
        $this->form_validation->set_rules('ticket_delay_duration', 'Delay duration', 'trim|numeric|required');
        $this->form_validation->set_rules('escalation_message', 'Escalation message', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->sla();
        } else {
            $this->db->trans_start();
            $sla_id = $this->input->post('sla_id');
            $this->crud_model->add_record('escalation_levels', array(
                'sla_id' => $sla_id,
                'reg_staff' => $this->admin_id,
                'user_category_id' => $this->input->post("user_category_id"),
                'ticket_delay_duration' => $this->input->post("ticket_delay_duration"),
                'escalation_message' => $this->input->post("escalation_message")
            ));
            $this->db->trans_complete();
            if ($this->db->trans_status()) {
                $this->log_model->log('Registered escalation level ' . $this->input->post('sla_name'));
                $this->session->set_flashdata('success', 'Escalation level registered successfully');
            }
            redirect("sla/profile/" . urlencode(base64_encode($sla_id)), "refresh");
        }
    }


    function remove_level($level_id, $delete = false)
    {
        $key_word = $delete ? "Trashed" : "Recovered";
        $real_level_id = base64_decode(urldecode($level_id));
        $sla = $this->sites_model->escalation_level($real_level_id);
        if (count($sla) == 0) {
            $this->session->set_flashdata('error', 'Escalation level level selected does not exist');
            redirect("sla", "refresh");
            return;
        }
        $this->crud_model->update_record("escalation_levels", "sla_notification_level_id", $real_level_id, array("deleted" => $delete));
        $this->log_model->log($key_word . ' escalation level #' .$sla->sla_notification_level_id);
        $this->session->set_flashdata('success', 'Escalation level ' . $key_word . ' successfully');
        redirect("sla/profile/" . urlencode(base64_encode($sla->sla_id)), "refresh");
    }

}
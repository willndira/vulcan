<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 2/22/16
 * Time: 10:12 AM
 */
class Tickets extends CI_Controller
{
    private $admin_id;

    function __construct()
    {
        parent::__construct();
        $this->users_model->security();
        $this->admin_id = $this->users_model->user()->user_id;
    }

    function raise()
    {
        $this->val();
        if ($this->form_validation->run() == FALSE) {
            $this->index();
        } else {
            $data = array(
                'ticket_title' => $this->input->post('ticket_title'),
                'site_id' => $this->input->post('site_id'),
                'ticket_issue' => $this->input->post('about'),
                'ticket_due_date' => $this->input->post('ticket_due_date'),
                'ticket_priority' => $this->input->post('ticket_priority'),
                'ticket_etime' => $this->input->post('expected_duration'),
                'problem_type' => $this->input->post('problem_type'),
                'is_remote' => $this->input->post('is_remote'),
                'ticket_affected_component' => $this->input->post('affected_component'),
                'raised_by' => $this->session->userdata('logged_admin')
            );
            $this->db->trans_start();
            $ticket_id = $this->crud_model->add_record('tickets', $data);

            $this->tickets_model->log($ticket_id, "Raised by " . $this->users_model->user()->user_name);
            $this->db->trans_complete();
            if ($this->db->trans_status()) {
                $this->session->set_flashdata('success', 'Ticket created successfully.');
                $this->log_model->log('Created ticket #' . $this->input->post('ticket_title'));
            }
            redirect('tickets/profile/' . urlencode(base64_encode($ticket_id)), 'refresh');
        }
    }

    function val()
    {
        $this->form_validation->set_rules('expected_duration', 'Expected duration at the site', 'trim|required');
        $this->form_validation->set_rules('problem_type', 'Type of the issue', 'trim|required');
        $this->form_validation->set_rules('affected_component', 'Affected component at the site', 'trim|required');
        $this->form_validation->set_rules('ticket_title', 'Ticket title', 'trim|required');
        $this->form_validation->set_rules('site_id', 'Site', 'trim|numeric|required');
        $this->form_validation->set_rules('ticket_priority', 'Priority', 'trim|required');
        $this->form_validation->set_rules('ticket_due_date', 'Due date', 'required');
        $this->form_validation->set_rules('about', 'Problem', 'required');
    }

    function index()
    {
        $var['page'] = 'Maintenance Tickets';
        $this->load->template('tickets_view', $var);
    }

    function assign()
    {
        $this->form_validation->set_rules('technicians', 'Assigned technicians', 'required');
        $this->form_validation->set_rules('staff_role', 'Technician role', 'required');
        $ticket_id = $this->input->post('ticket_id');
        if ($this->form_validation->run() == FALSE) {
            $this->profile(urlencode(base64_encode($ticket_id)));
        } else {
            foreach ($this->input->post('technicians') as $assigned) {
                $data1 = array(
                    'technician_id' => $assigned,
                    'ticket_id' => $ticket_id,
                    'staff_role' => $this->input->post('staff_role'),
                    'assigned_by' => $this->admin_id
                );
                $tech = $this->crud_model->get_record("technicians", "technician_id", $assigned);
                $this->crud_model->add_record('ticket_staff', $data1);
                $this->log_model->log('Assigned ' . $this->users_model->user($tech->user_id)->user_name . ' ticket (#' . $ticket_id . ') ' . $this->input->post('ticket_title'));
                $this->tickets_model->log($ticket_id, 'Assigned ' . $this->users_model->user($tech->user_id)->user_name . ' with a role ' . $this->input->post('staff_role'));
                $this->notify($tech->user_id, $ticket_id);
            }
            redirect('tickets/profile/' . urlencode(base64_encode($ticket_id)), 'refresh');
        }
    }

    function profile($ticket_id)
    {
        $ticket_id = base64_decode(urldecode($ticket_id));
        if (!$ticket = $this->crud_model->get_record('tickets', 'ticket_id', $ticket_id)) {
            redirect('unknown', 'refresh');
            return;
        }
        $var['page'] = 'Ticket Profile';
        $var['details'] = $ticket;
        $this->load->template('ticket_profile', $var);
    }

    function notify($assigned, $ticket_id)
    {
        $ticket = $this->crud_model->get_record("tickets", "ticket_id", $ticket_id);
        $data = array(
            'header' => 'Ticket Assignment',
            'message' => 'You have been assigned a new ticket by ' . $this->users_model->user()->user_name
                . '. <br/><b>TICKET DETAILS</b>'
                . '<br/> TITLE:' . $ticket->ticket_title
                . '<br/> ASSIGNMENT TIME:' . date('Y-m-d H:m:s')
                . '<br/> SITE NAME:' . $this->sites_model->site($ticket->site_id)->site_name
                . '<br/> DUE TIME: ' . $ticket->ticket_due_date
                . '<br/> PRIORITY: ' . $ticket->ticket_priority
                . '<br/> PROBLEM: ' . $ticket->ticket_issue
                . '<br/> Role: ' . $this->input->post('staff_role') . '<br/>'
        );
        $this->users_model->notify(array($assigned), $data);
    }

    function update()
    {
        $this->val();
        $ticket_id = $this->input->post('ticket_id');
        if ($this->form_validation->run() == FALSE) {
            $this->profile(urldecode(base64_encode($ticket_id)));
        } else {
            $data = array(
                'ticket_title' => $this->input->post('ticket_title'),
                'site_id' => $this->input->post('site_id'),
                'ticket_issue' => $this->input->post('about'),
                'ticket_due_date' => $this->input->post('ticket_due_date'),
                'ticket_priority' => $this->input->post('ticket_priority'),
                'is_remote' => $this->input->post('is_remote'),
                'ticket_etime' => $this->input->post('expected_duration'),
                'problem_type' => $this->input->post('problem_type'),
                'ticket_affected_component' => $this->input->post('affected_component')
            );
            if ($this->tickets_model->update($data)) {
                $this->session->set_flashdata('success', 'Ticket details updated successfully.');
                $this->log_model->log('Edited details for ticket #' . $ticket_id . ", Title: " . $this->input->post('ticket_title'));
            } else
                $this->session->set_flashdata('error', 'Ticket updated failed.');


            redirect('tickets/profile/' . urlencode(base64_encode($ticket_id)), 'refresh');
        }
    }

    function trash($ticket_id, $restore = false)
    {
        $this->db->trans_start();
        $this->crud_model->update_record("tickets", "ticket_id", base64_decode(urldecode($ticket_id)), array("deleted" => $restore));
        $this->tickets_model->log(base64_decode(urldecode($ticket_id)), !$restore ? "Recovered from trash" : "Trashed");
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            if (!$restore) {
                $this->log_model->log('Recovered ticket No. ' . base64_decode(urldecode($ticket_id)));
                echo "Recovered Successfully";
            } else {
                $this->log_model->log('Trashed ticket No. ' . base64_decode(urldecode($ticket_id)));
                echo "Trashed Successfully";
            }
        } else {
            echo 'Failed';
        }
    }

    function cancel_assign($ticket_staff_id, $restore = false)
    {
        $this->db->trans_start();
        $this->crud_model->update_record("ticket_staff", "ts_id", base64_decode(urldecode($ticket_staff_id)), array("deleted" => $restore));
        // $this->tickets_model->log(base64_decode(urldecode($ticket_staff_id)), !$restore ? "Recovered from trash" : "Trashed");
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            if (!$restore) {
                //  $this->log_model->log('Recovered ticket No. ' . base64_decode(urldecode($ticket_staff_id)));
                echo "Staff reassigned to ticket";
            } else {
                //   $this->log_model->log('Trashed ticket No. ' . base64_decode(urldecode($ticket_staff_id)));
                echo "Technician assignment canceled";
            }
        } else {
            echo 'Failed';
        }
    }
}
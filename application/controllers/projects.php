<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 2/15/16
 * Time: 7:11 PM
 */
class Projects extends CI_Controller
{
    protected $admin_id;

    function __construct()
    {
        parent::__construct();
        $this->users_model->security();
        $this->admin_id = $this->users_model->user()->user_id;
    }

    function index()
    {
        $var['page'] = 'Projects Library';
        $var['projects'] = $this->crud_model->get_records('projects');
        $this->load->template('projects_view', $var);
    }

    function register()
    {
        $this->val();
        if ($this->form_validation->run() == FALSE) {
            $this->start();
        } else {
            $this->db->trans_start();
            $project_id = $this->projects_model->register(
                $this->input->post('project_name'),
                $this->input->post('client_name'),
                $this->input->post('description'),
                $this->input->post('start_date'),
                $this->input->post('end_date'),
                $this->input->post('project_manager'),
                $this->input->post('project_site_id')
            );
            $this->db->trans_complete();
            if ($this->db->trans_status()) {
                $this->session->set_flashdata('success', 'Project request sent successfully');


            }
            redirect('projects/profile/' . urlencode(base64_encode($project_id)), 'refresh');
        }
    }

    function val()
    {
        $this->form_validation->set_rules('project_name', 'Project Name', 'trim|required');
        $this->form_validation->set_rules('client_name', 'Client Name', 'trim|required');
        $this->form_validation->set_rules('start_date', 'Start date', 'required');
        $this->form_validation->set_rules('end_date', 'End date', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required');

    }

    function start()
    {
        $var['page'] = 'Project Proposal';
        $this->load->template('new_project', $var);
    }

    function request_approval($project_id)
    {
//notify top officials of a new project
        $this->db->trans_start();
        $project = $this->crud_model->get_record("projects", "project_id", $project_id);
        $this->crud_model->update_record("projects", "project_id", $project_id, array("project_stage" => 1));
        foreach ($this->users_model->has_powers("revProj") as $manager) {
            $data = array(
                'header' => 'New site Proposal',
                'message' => 'There is a new site proposal by ' . $this->users_model->user($project->project_proposer)->user_name . '.<br/>
                        Kindly check the system and have your say about it.'
            );
            $this->users_model->notify(array($manager->user_id), $data);
        }
        //send notification to pm
        $data = array(
            'project_id' => $project_id,
            'header' => 'Project management role',
            'message' => 'You have been assigned a project management role by ' . $this->users_model->user($project->project_proposer)->user_name . ' for the project
                    ' . $project->project_name . '.<br/> Log into your profile to review and plan for the project .'
        );
        $this->notify_pm($data);
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            echo "Request submitted successfully";
        } else {
            echo "OOPS: An error occurred while submitting your request";
        }
    }

    function notify_pm($details)
    {
        $details = json_decode(json_encode($details));
        $data = array(
            'header' => $details->header,
            'message' => $details->message
        );
        $this->users_model->notify(array($this->projects_model->project($details->project_id)->project_manager), $data);
    }

    function add_machine()
    {
        $project_id = $this->input->post('project_id');
        $component_id = $this->input->post('component_id');
        $component_qty = $this->input->post('component_qty');
        $desc = $this->input->post('description');
        if (!$this->db->get_where('tbl_project_components', array('component_id' => $component_id, 'project_id' => $project_id))->row()) {
            $this->crud_model->add_record('project_components',
                array(
                    'component_id' => $component_id,
                    'pc_description' => $desc,
                    'component_qty' => $component_qty,
                    'project_id' => $project_id,
                    'pc_added_by' => $this->admin_id
                ));
            $this->projects_model->log($project_id, 'Machine  added');
            $this->log_model->log('Added machine to project ' . $this->crud_model->get_record('projects', 'project_id', $project_id)->project_name);
            $this->session->set_flashdata('success', 'Machine added to project successfully');
        } else {
            $this->session->set_flashdata('error', 'It seems the machine is already in added in the project. Consider editing quantity');
        }
        redirect('projects/profile/' . urlencode(base64_encode($project_id)));
    }

    function update()
    {
        $this->val();
        $project_id = $this->input->post('project_id');
        if ($this->form_validation->run() == FALSE) {
            $this->profile($project_id);
        } else {
            $data = array(
                'project_name' => $this->input->post('project_name'),
                'project_client' => $this->input->post('client_name'),
                'project_description' => $this->input->post('description'),
                'project_manager' => $this->input->post('project_manager'),
                'site_id' => $this->input->post('project_site_id'),
                'project_start_date' => $this->input->post('start_date'),
                'project_due_date' => $this->input->post('end_date')
            );
            if ($this->crud_model->update_record('projects', 'project_id', $project_id, $data)) {
                $this->projects_model->log($project_id, 'Details updated');
                $this->log_model->log('Updated details for project ' . $this->input->post('project_name'));
                $this->session->set_flashdata('success', 'Project updated successfully');
                foreach ($this->users_model->has_powers("revProj") as $manager) {
                    $data = array(
                        'header' => 'Site Proposal Details update',
                        'message' => $this->users_model->user()->user_name . '. has made changes to the project proposal'
                            . $this->input->post('project_name') . '<br/>Kindly check the system to view changes from project timeline tab.'
                    );
                    $this->users_model->notify(array($manager->user_id), $data);
                }
                redirect('projects/profile/' . urlencode(base64_encode($project_id)), 'refresh');
            }
        }
    }

    function profile($project_id)
    {
        $var['page'] = 'Projects Profile';
        $var['details'] = $this->load_project($project_id);
        $this->load->template('project_profile', $var);
    }

    function load_project($project_id)
    {
        $project = $this->crud_model->get_record('projects', 'project_id', base64_decode(urldecode($project_id)));
        if (count($project) < 1) {
            $this->session->set_flashdata('error', 'Project not found. Check the url and try again');
            redirect("projects", "refresh");
        } else {
            return $project;
        }
    }

    function pending()
    {
        $var['projects'] = $this->crud_model->get_records("projects", "project_stage", 1);
        $var['page'] = 'Project Pending Review';
        $this->load->template('projects_view', $var);
    }

    function incomplete($state)
    {
        $state = urldecode(base64_decode($state));
        $var['projects'] = $this->projects_model->incomplete($state);
        $var['page'] = $state <= 3 ? "Assembly projects" : "Installation projects";
        $this->load->template('projects_view', $var);
    }

    function pending_handover()
    {
        $var['projects'] = $this->projects_model->pending_handover();
        $var['page'] = "Projects pending handover";
        $this->load->template('projects_view', $var);
    }

    function complete()
    {
        $var['projects'] = $this->projects_model->complete();
        $var['page'] = "Complete projects";
        $this->load->template('projects_view', $var);
    }

    function assembly($project_id)
    {
        $var['details'] = $this->load_project($project_id);
        $var['page'] = 'Project Assembly';
        $this->load->template('project_setup', $var);
    }

    function change_manager($project_id)
    {
        if (null == $this->input->post('project_manager')) {
            $this->session->set_flashdata('error', 'Project manager needs to be specified');
            redirect('projects/profile/' . $project_id, 'refresh');
        }
        $project = $this->crud_model->get_record("projects", "project_id", base64_decode(urldecode($project_id)));
        $this->crud_model->update_record("projects", "project_id", $project->project_id,
            array("project_manager" => $this->input->post('project_manager')));
        if ($project->project_manager > 0)
            $this->projects_model->log($project->project_id, "Project manager changed from <b>" . $this->users_model->user($project->project_manager)->user_name . " to " .
                $this->users_model->user($this->input->post('project_manager'))->user_name . "</b>");
        else
            $this->projects_model->log($project->project_id, "New project manager assigned: <b>" . $this->users_model->user($this->input->post('project_manager'))->user_name . "</b>");
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $this->log_model->log('Change project manager for project: ' . $this->projects_model->project(base64_decode(urldecode($project_id)))->project_name);
            $this->session->set_flashdata('success', 'Project manager updated successfully');
        }
        redirect('projects/profile/' . $project_id, 'refresh');

    }

    function submit_review()
    {
        $this->form_validation->set_rules('project_id', 'Project', 'trim|required');
        $this->form_validation->set_rules('pr_verdict', 'Project verdict', 'trim|required');
        $this->form_validation->set_rules('pr_comment', 'Review comment', 'trim|required');
        $project_id = $this->input->post('project_id');
        if ($this->form_validation->run() == FALSE) {
            $this->review(urlencode(base64_encode($project_id)));
        } else {
            $data = array(
                'project_id' => $project_id,
                'pr_verdict' => $this->input->post('pr_verdict'),
                'pr_comment' => $this->input->post('pr_comment'),
                'user_id' => $this->admin_id
            );
            if ($this->crud_model->add_record('project_review', $data)) {
                $this->projects_model->log($project_id, 'Review submitted');
                $this->log_model->log('Did a review for project ' . $this->crud_model->get_record('projects', 'project_id', $project_id)->project_name);
                $this->session->set_flashdata('success', 'Project review submitted successfully');
                $data = array(
                    'header' => 'Site Proposal Review',
                    'message' => $this->users_model->user()->user_name . ' has made made a review to your project proposal'
                        . $this->input->post('project_name') . '<br/><b>REVIEW : </b> ' . $this->input->post('pr_comment')
                );
                $this->users_model->notify(array($this->crud_model->get_record("projects", "project_id", $project_id)->project_proposer), $data);
            }
            redirect('projects/review/' . urlencode(base64_encode($project_id)), 'refresh');
        }
    }

    function review($project_id)
    {
        $var['details'] = $this->load_project($project_id);
        $var['page'] = 'Project Review';
        $this->load->template('project_review', $var);
    }

    function new_teamer()
    {
        $project_id = $this->input->post('project_id');
        $user_id = $this->input->post('user_id');
        $team_type = $this->input->post('team_type');
        $role = $this->input->post('member_role');
        if (!$this->db->get_where('tbl_project_team', array('user_id' => $user_id, 'project_id' => $project_id))->row()) {
            $this->crud_model->add_record('project_team',
                array(
                    'user_id' => $user_id,
                    'member_role' => $role,
                    'team_type' => $team_type,
                    'project_id' => $project_id,
                    'added_by' => $this->admin_id
                ));
            $this->projects_model->log($project_id, $this->users_model->user($user_id)->user_name . ' assigned as a team member');
            $this->log_model->log('Assigned ' . $this->users_model->user($user_id)->user_name . ' as a team member of project ' . $this->crud_model->get_record('projects', 'project_id', $project_id)->project_name);
            $this->session->set_flashdata('success', 'Member assigned to project successfully');
            $notify = array(
                'header' => "Project assignment",
                'message' => $this->users_model->user()->user_name . ' has assigned you to project '
                    . $this->crud_model->get_record('projects', 'project_id', $project_id)->project_name . '<br/><b>Team : </b> ' . ($team_type == 1 ? 'Assembly' : "Installation")
                    . '<br/><b>Role: </b>' . $role
            );
            $this->users_model->notify(array($user_id), $notify);
        } else {
            $this->session->set_flashdata('error', 'It seems that staff is already a member here.');
        }
        redirect('projects/profile/' . $project_id);
    }

    function authorize($request, $status = false)
    {
        $project_id = base64_decode(urldecode($request));
        $data = array(
            'project_id' => $project_id,
            'pr_verdict' => !$status,
            'pr_comment' => !$status ? '{AUTHORIZED} project request approved' : "{Declined} Project request declined",
            'user_id' => $this->admin_id
        );
        $this->db->trans_start();
        $this->projects_model->log($project_id, !$status ? 'Authorised to go ahead' : "Request declined");
        $this->crud_model->add_record('project_review', $data);
        $this->projects_model->move_next($project_id, (!$status ? 3 : 2));
        $this->db->trans_complete();
        $notify = array(
            'header' => $data["pr_comment"],
            'message' => $this->users_model->user()->user_name . ' has made made a final verdict on your proposed project '
            . $this->input->post('project_name') . '<br/><b>Verdict : </b> ' . !$status ? 'Authorised to go ahead' : "Request declined"
        );
        $this->users_model->notify(array($this->crud_model->get_record("projects", "project_id", $project_id)->project_proposer), $notify);
        redirect('projects/profile/' . urlencode(base64_encode($project_id)), 'refresh');
    }

    function perform_procedure($procedure_details)
    {
        $details = json_decode(urldecode($procedure_details));
        if (!$details->is_pm) {
            echo "Not team member";
            return;
        }
        $this->db->trans_start();
        $guide = $this->equipment_model->guide_details($details->guide);
        if (!is_null($guide)) {
            $this->crud_model->update_record("assembly_schedule_steps", "as_id", $details->guide,
                array("perform_result" => $details->setup_result, "performed_by" => $this->admin_id, "perform_time" => date("Y-m-d H:m:s")));
        } else {
            echo "Failed!!! ... Try refreshing the page, if it persists kindly contact support";
            return;
        }
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            echo "SUCCESS :-) ... Procedure performed successfully";
        }
    }

    function post_text($as_id, $equipment_assembly_id)
    {
        $this->db->trans_start();
        $guide = $this->equipment_model->guide_details($as_id);
        if (!is_null($guide)) {
            $this->crud_model->update_record("assembly_schedule_steps", "as_id", $as_id,
                array("perform_result" => $this->input->post("result"), "performed_by" => $this->admin_id, "perform_time" => date("Y-m-d H:m:s")));
        } else {
            echo "Failed!!! ... Try refreshing the page, if it persists kindly contact support";
            return;
        }
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $this->session->set_flashdata('success', 'SUCCESS :-) ... Procedure performed successfully');
        } else
            $this->session->set_flashdata('error', 'FAILED :-( ... Results failed to be posted. Kindly check the information provided and try again');

        redirect('assembly/process/' . urlencode(base64_encode($equipment_assembly_id)), 'refresh');
    }

    function start_task($details)
    {
        $details = json_decode(urldecode($details));
        $this->db->trans_start();
        $this->crud_model->update_record("assembly_schedule", "assembly_schedule_id",
            $details->assembly_schedule_id, array("start_date" => date("Y-m-d H:m:s"), "schedule_stage" => 1));
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            echo "SUCCESS :-) ... Task started";
        }
    }

    function assembly_complete($id, $is_project = false)
    {
        $this->db->trans_start();
        if (!$is_project) {
            $equipment = $this->equipment_model->details($id);
            $this->crud_model->update_record("equipment_assembly", "equipment_id", $id, array("completion_date" => date("Y-m-d H:m:s")));
            $this->crud_model->update_record("equipment", "equipment_id", $id, array("equipment_stage" => ($equipment->equipment_stage + 1)));
            $this->equipment_model->log($id, $equipment->equipment_stage == 1 ? "Assembly process marked complete" : "Testing process marked
            complete");
        } else {
            $project = $this->projects_model->project($id);
            $this->crud_model->update_record("projects", "project_id", $id, array("project_stage" => $project->project_stage + 1, "assembly_completion_date" => date("Y-m-d H:m:s")));
            $this->projects_model->log($id, "Assembly process marked complete");
        }
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            echo "Assembly process marked complete";
            return;
        }
        echo "Assembly completion failed";
    }

    function handover($project_id)
    {
        $var['page'] = 'Project handover';
        $var['details'] = $this->load_project($project_id);
        $this->load->template('handover_form', $var);
    }

    function confirm_handover($project_id)
    {
        $this->db->trans_start();
        $project = $this->projects_model->project($project_id);
        $this->assembly_complete($project_id, true);
        $this->crud_model->update_record("sites", "site_id", $project->site_id, array("site_status" => 1));
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $this->session->set_flashdata('success', 'Project handed over to client successfully and site status changed to live ');
        } else {
            $this->session->set_flashdata('error', 'Project handover failed');
        }
    }

    function trash($project_id, $restore = false)
    {
        $this->db->trans_start();
        $this->crud_model->update_record("projects", "project_id", base64_decode(urldecode($project_id)), array("deleted" => $restore));
        $this->projects_model->log(base64_decode(urldecode($project_id)), !$restore ? "Recovered from trash" : "Trashed");
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            if (!$restore) {
                $this->log_model->log('Recovered project No. ' . base64_decode(urldecode($project_id)));
                echo "project recovery Successfully";
            } else {
                $this->log_model->log('Trashed project No. ' . base64_decode(urldecode($project_id)));
                echo "project trashed Successfully";
            }
        } else {
            echo 'Failed';
        }
    }
}
<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 2/15/16
 * Time: 7:12 PM
 */
class Projects_model extends CI_Model
{
    protected $logged;

    function __construct()
    {
        parent::__construct();
        $this->logged = $this->users_model->user();
    }

    function project($project_id)
    {
        return $this->crud_model->get_record('projects', 'project_id', $project_id);
    }

    function register($project_name, $client_name, $description, $start_date, $end_date, $user_id, $site_id)
    {
        $data = array(
            'project_name' => $project_name,
            'project_client' => $client_name,
            'project_proposer' => $this->logged->user_id,
            'project_description' => $description,
            'project_start_date' => $start_date,
            'project_due_date' => $end_date,
            'project_manager' => $user_id,
            'site_id' => $site_id
        );
        $project_id = $this->crud_model->add_record('projects', $data);
        $this->log($project_id, 'Added into the system');
        $this->log_model->log('Creating a project proposal ' . $project_name);
        return $project_id;
    }

    function log($project_id, $action)
    {
        $data = array(
            'project_id' => $project_id,
            'pt_action' => $action,
            'user_id' => $this->users_model->user()->user_id
        );
        $this->crud_model->add_record('project_timeline', $data);
    }

    function incomplete($state)
    {
        $this->db->select('*')
            ->from('tbl_projects')
            ->where('project_stage >', 2)
            ->where('deleted ', false);
        if ($state <= 3)
            $this->db->where('project_stage <', 4);
        else
            $this->db->where('project_stage <', 6)
                ->where('project_stage > ', 3);
        return $this->db->get()->result();
    }

    function pending_handover()
    {
        return $this->db->select('*')
            ->from('tbl_projects')
            ->where('project_stage', 5)
            ->where('deleted ', false)
            ->get()
            ->result();
    }
    function complete()
    {
        return $this->db->select('*')
            ->from('tbl_projects')
            ->where('project_stage', 6)
            ->where('deleted ', false)
            ->get()
            ->result();
    }

    function move_next($project_id, $status)
    {
        $this->crud_model->update_record('projects', 'project_id', $project_id, array('project_stage' => $status));
        $this->log($project_id, $status != 2 ? 'Moved to ' . $this->stage($status) . " stage" : "Rejected");
    }

    function stage($stage)
    {
        switch ($stage) {
            case 0;
                return "<span class='cyan-text'>Draft</span>";
            case 1;
                return "<span class='cyan-text'>Under Review</span>";
            case 2;
                return "<span class='red-text'>Rejected</span>";
            case 3;
                return "<span class='orange-text'>Assembly</span>";
            case 4;
                return "<span class='blue-text'>Installation</span>";
            case 5;
                return "<span class='green-text'>Pending handover</span>";
            case 6;
                return "<span class='green-text'>Installed</span>";
            default:
                return "<span class='red-text'>Unknown</span>";
        }
    }

    function team($project_id, $team_type = false)
    {
        if ($team_type)
            return $this->db->get_where("tbl_project_team", array("project_id" => $project_id, "team_type" => $team_type, "deleted" => false))->result();
        else
            return $this->db->get_where("tbl_project_team", array("project_id" => $project_id, "deleted" => false))->result();
    }

    function components($project_id, $stage)
    {
        $this->db->select('*');
        $this->db->from('tbl_project_components a');
        $this->db->join('tbl_components b', 'a.component_id = b.component_id');
        //   $this->db->join('tbl_component_items c', 'c.component_id = b.component_id');
        $this->db->where('a.project_id', $project_id);
        $this->db->where('a.component_stage', $stage);

        return $this->db->get()->result();
    }

    function equipment_steps($component_id, $level, $test = false)
    {
        return $this->db->get_where("tbl_setup_guide",
            array("component_id" => $component_id,
                "is_test" => $test, //TESTING OR NOT
                "step_category" => $level //ASSEMBLY VS INSTALLATION
            ))->result();
    }

    function step_requirements($equipment_id, $step_id)
    {
        $is_available = true;
        $i = 0;
        $items = $this->crud_model->get_records("setup_items", "step_id", $step_id);
        while ($is_available && $i < count($items)) {
            $this->db->select("a.asset_id");
            $this->db->from("tbl_asset_location_trail a");
            $this->db->join("tbl_items b", "a.asset_id = b.item_id");
            $this->db->where("b.model_id", $items[$i]->model_id);
            $this->db->where("a.location_type", 2);
            $this->db->where("a.location_id", $equipment_id);
            $this->db->where("a.status", true);
            $this->db->where("a.confirmation", true);

            if (count($this->db->get()->result()) < 1)
                $is_available = false;
            $i++;
        }
        return $is_available;
    }


}
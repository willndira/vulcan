<?php

/**
 * Created by PhpStorm.
 * User: mayne
 * Date: 3/17/16
 * Time: 2:53 PM
 */
class Equipment_model extends CI_Model
{
    protected $logged;

    function __construct()
    {
        parent::__construct();
        $this->logged = $this->users_model->user();
    }

    function all()
    {
        return $this->crud_model->get_records("equipment");
    }

    function register_equipment($details)
    {
        $this->db->trans_start();
        $eq = $this->crud_model->add_record("equipment", $details);
        $asset = array(
            "asset_id" => $eq,
            "asset_type" => 2,
            "location_id" => 2,
            "location_type" => 1,
            "comment" => "New equipment registration "
        );
        $this->asset_model->stock($asset);
        $this->db->trans_complete();
        $this->log($eq, "Registered into system");
        return $eq;
    }

    function log($equipment_id, $action)
    {
        $this->crud_model->add_record("equipment_timeline", array(
            "equipment_id" => $equipment_id,
            "activity" => $action,
            "user_id" => $this->logged->user_id
        ));
        $this->log_model->log($action . " :: Equipment no. " . $this->details($equipment_id)->equipment_no);
    }

    function details($eq_id)
    {
        $item = $this->crud_model->get_record('equipment', 'equipment_id', $eq_id);
        return $item;
    }

    function add_asset($equipment_id, $item_id)
    {
        $data = array(
            'equipment_id' => $equipment_id,
            'item_id' => $item_id,
            'added_by' => $this->logged->user_id
        );
        $this->crud_model->add_record('equipment_items', $data);
        $this->items_model->log($item_id, "Added to equipment No " . $this->details($equipment_id)->equipment_no);
    }

    function stage($stage_id)
    {
        switch ($stage_id) {
            case 0:
                return "<span class='red-text'>Pending assembly</span>";
            case 1:
                return "<span class='orange-text'>Assembly</span>";
            case 2:
                return "<span class='yellow-text'>Pending Assembly testing</span>";
            case 3:
                return "<span class='yellow-text'>Assembly testing</span>";
            case 4:
                return "<span class='green-text'>Pending installation</span>";
            case 5:
                return "<span class='green-text'>Installation</span>";
            case 6:
                return "<span class='green-text'>Pending installation testing</span>";
            case 7:
                return "<span class='green-text'>Installation testing</span>";
            case 8:
                return "<span class='green-text'>Installed</span>";
            default:
                return "<span class='red-text'>Unknown</span>";
        }
    }

    function task_stage($stage_id)
    {
        switch ($stage_id) {
            case 0:
                return "<span class='orange-text text-darken-4'>Pending</span>";
            case 1:
                return "<span class='yellow-text text-darken-4'>In progress</span>";
            case 2:
                return "<span class='green-text text-darken-4'>Complete</span>";
            default:
                return "<span class='red-text'>Unknown</span>";
        }
    }

    function priority($priority)
    {
        switch ($priority) {
            case 1:
                return "Very low";
            case 2:
                return "Low";
            case 3:
                return "Normal";
            case 4:
                return "High";
            default:
                return "Very high";
        }
    }

    function is_team($user_id, $schedule_id)
    {
        $this->db->select("assembly_team_id")
            ->from("tbl_assembly_team")
            ->where("user_id", $user_id)
            ->where("assembly_schedule_id", $schedule_id);
        return count($this->db->get()->result()) > 0;
    }

    function guide_details($ag_id)
    {
        return $this->crud_model->get_record("setup_guide", "ag_id", $ag_id);
    }


    function tests($equipment_id)
    {
        return $this->db->get_where("tbl_equipment_tests", array(
            "deleted" => false,
            "equipment_id" => $equipment_id))
            ->result();
    }

    /*
    * Monitor movements of assets.
    * asset type : 1 for item, 2 for equipment;
    * location_type: 1 for store, 2 for equipment, 3 for project
    */

    function location($asset_id, $asset_type)
    {
        return $this->db
            ->order_by("asset_location_id", "desc")
            ->get_where("tbl_asset_location_trail",
                array(
                    "asset_id" => $asset_id,
                    "asset_type" => $asset_type))
            ->row();
    }

    function current_location($asset_id, $asset_type)
    {
        $current = $this->db
            ->order_by("asset_location_id", "desc")
            ->get_where("tbl_asset_location_trail",
                array(
                    "asset_id" => $asset_id,
                    "asset_type" => $asset_type))
            ->row();
        if (count($current) == 0) {
            return "<span class='red-text'>Unknown</span>";
        }
        if ($current->location_type == 1) {
            return "<b>" . $this->stores_model->specific($current->location_id)->store_name . "</b>";
        } elseif ($current->location_type == 2) {
            $eq = $this->details($current->location_id);
            return "installed in equipment: <b>" . $eq->equipment_no . "</b> " . $this->current_location($current->location_id, 2);
        } else {
            $project = $this->projects_model->project($current->location_id);
            if ($project->project_stage == 3)
                return " in <b>Assembly Store</b>";
            elseif ($project->project_stage == 4)
                return " in <b>Installation Store</b>";
            else {
                return " at site:<b>" . $this->sites_model->site($project->site_id)->site_name . " </b>";
            }
        }
    }

    function installation_ready($equipment_id)
    {
        $current_location = $this->equipment_model->location($equipment_id, 2);
        if ($current_location->location_type == 3) {
            $project = $this->projects_model->project($current_location->location_id);
            if ($project->project_stage == 4) {
                if ($project->site_id != 0) {
                    $site = $this->sites_model->site($project->site_id);
                    if ($site->site_status != 3) {
                        return true;
                    }
                    return "Installation site not ready";
                }
                return "Project site not specified";
            }
            return "project not ready for installation";
        }
        return "equipment not requested";
    }

    /*
     * Assembly functions
     */

    function assembly_date($type, $equipment_assembly_id)
    {
        return $this->db->select_max(($type ? "expected_start_date" : "expected_end_date"), "sdate")
            ->from("tbl_assembly_schedule")
            // ->join("tbl_equipment_assembly b", "a.equipment_assembly_id = b.equipment_assembly_id")
            ->where("equipment_assembly_id", $equipment_assembly_id)
            ->get()
            ->row()
            ->sdate;
    }

    function start_date($equipment_assembly_id)
    {
        return $this->crud_model->get_record("assembly_schedule", "expected_start_date", $this->assembly_date(1, $equipment_assembly_id));
    }

    function completion_date($equipment_id)
    {
        return $this->db->select_max("a.complete_date", "complete_date")
            ->from("tbl_assembly_schedule a")
            ->join("tbl_equipment_assembly b", "a.equipment_assembly_id = b.equipment_assembly_id")
            ->where("b.equipment_id", $equipment_id)
            ->get()
            ->row()
            ->complete_date;
    }

    function task($task_id)
    {
        return $this->crud_model->get_record("assembly_schedule", "assembly_schedule_id", $task_id);
    }
}
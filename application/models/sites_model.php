<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 2/16/16
 * Time: 7:42 PM
 */
class Sites_model extends CI_Model
{

    function status($status)
    {
        switch ($status) {
            case 1:
                return '<span class="green-text">Online</span>';
            case 2:
                return '<span class="orange-text">Offline</span>';
            case 3:
                return '<span class="blue-text">Under Construction</span>';
            case 4:
                return '<span class="orange-text">Ready for installation</span>';
            default;
                return '<span class="red-text">Unknown</span>';
        }
    }

    function log($site_id, $action)
    {
        $this->crud_model->add_record('site_timeline', array(
            'site_id' => $site_id,
            'user_id' => $this->users_model->user()->user_id,
            'st_action' => $action
        ));
        $this->log_model->log($action . " site " . $this->site($site_id)->site_name);
    }

    function site($site_id)
    {
        return $this->crud_model->get_record('sites', 'site_id', $site_id);
    }

    function manager($site_id)
    {
        return $this->db->get_where('tbl_site_manager', array('site_id' => $site_id, 'check_in  < ' => now(), "check_out" => now()))->row();
    }

    function preventative($site_id)
    {
        echo "fetching";
    }

    //Site profile
    function site_equipment($site_id)
    {
        return array();
    }

    function site_supervisors($site_id)
    {
        return $this->db->get_where("tbl_site_manager", array("site_id" => $site_id, "deleted" =>false))->result();
    }

    //SLA management
    //todo: relocate all SLA to sites model

    function sla($sla_id)
    {
        return $this->crud_model->get_record("sla", "sla_id", $sla_id);
    }

    function get_sla($deleted = false)
    {
        return $this->db->get_where("tbl_sla", array("deleted" => $deleted))->result();
    }

    function sla_escalation_levels($sla_id, $deleted = false)
    {
        return $this->db->select("a.*")->from("tbl_escalation_levels a")->join("tbl_sla b", "a.sla_id = b.sla_id")
            ->where(array("a.deleted" => $deleted, "a.sla_id" => $sla_id))->get()->result();
    }

    function escalation_levels()
    {
        return $this->db->select("a.*")->from("tbl_escalation_levels a")->join("tbl_sla b", "a.sla_id = b.sla_id")
            ->where(array("a.deleted" => false, "b.deleted" => false))->get()->result();
    }

    function escalation_level($level_id)
    {
        return $this->db->select("*")->from("tbl_escalation_levels")
            ->where(array("sla_notification_level_id" => $level_id))->get()->row();
    }

    //SLA tickets

    function sla_tickets($sla_id)
    {
        return array(); //todo:
    }
}
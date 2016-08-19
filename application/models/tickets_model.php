<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 2/21/16
 * Time: 8:39 PM
 */
class Tickets_model extends CI_Model
{
    protected $logged;

    function __construct()
    {
        parent::__construct();
        $this->logged = $this->users_model->user();
    }

    function my_tickets($technician_id = false, $offset = false)
    {
        return $this->db->select("a.*")
            ->from("tbl_ticket_staff b")
            ->join("tbl_tickets a", "a.ticket_id = b.ticket_id", 'left outer')
            ->join("tbl_technicians c", "c.technician_id = b.technician_id")
            ->where("c.user_id", $technician_id ? $technician_id : $this->logged->user_id)
            ->where("b.deleted", false)
            ->where("a.deleted", false)
            ->where("b.ticket_id > ", $offset)
            ->get()
            ->result();
    }

    function details($ticket_id)
    {
        return $this->crud_model->get_record("tickets", "ticket_id", $ticket_id);
    }


    function update($data, $ticket_id)
    {
        $this->db->trans_start();
        $this->crud_model->update_record("tickets", "ticket_id", $ticket_id, $data);
        $this->db->where(array("ticket_id" => $ticket_id, "deleted" => false))
            ->update("tbl_ticket_staff", array("altered" => true));
        $ticket = $this->crud_model->get_record("tickets", "ticket_id", $ticket_id);
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $this->api_model->create_notification("Ticket " . $ticket->ticket_title . " details have been updated by" . $this->users_model->user($this->logged->user_name) . ". Kindly open the ticket details for more details.",
                "Ticket update",
                $this->api_model->get_tokens($ticket_id));
        }
        return $this->db->trans_status();
    }

    function assignments($ticket_id)
    {
        return $this->db->get_where("tbl_ticket_staff", array("ticket_id" => $ticket_id, "deleted" => false))->result();
    }

    function log($ticket_id, $action)
    {
        $this->crud_model->add_record("ticket_log",
            array(
                "ticket_id" => $ticket_id,
                "user_id" => $this->logged->user_id,
                "tl_action" => $action
            ));
    }

    function state($state_id)
    {
        switch ($state_id) {
            case 1:
                return "<span class='red-text'> Pending</span>";
            case 2:
                return "<span class='orange-text'> In progress </span>";
            case 3:
                return "<span class='green-text'> Resolved </span>";
            default:
                return "<span class='red-text'> Unknown </span>";

        }
    }

    function priority($priority)
    {
        switch ($priority) {
            case 3:
                return "<span class='green-text'> Normal</span>";
            case 2:
                return "<span class='yellow-text'> Urgent </span>";
            case 1:
                return "<span class='green-text'> Critical </span>";
            default:
                return "<span class='red-text'> Unknown </span>";
        }
    }


    function component($component)
    {
        switch ($component) {
            case 4:
                return " Others";
            case 3:
                return " APS";
            case 2:
                return " Exit";
            case 1:
                return " Entry";
            default:
                return " Unknown";
        }
    }

    function type($type)
    {
        switch ($type) {
            case 1:
                return "<span class='orange-text'> Power</span>";
            case 2:
                return "<span class='orange-text'> Network </span>";
            case 3:
                return "<span class='orange-text'> Hardware </span>";
            case 4:
                return "<span class='orange-text'> Software </span>";
            case 5:
                return "<span class='orange-text'> Other </span>";
            default:
                return "<span class='red-text'> Unknown </span>";
        }
    }

    function monthly_tickets($month, $type)
    {
        $this->db->select("ticket_id")
            ->from("tbl_tickets")
            ->where("deleted", false)
            ->where("ticket_time >= ", date("Y-") . $month . "-01 00:00:00")
            ->where("ticket_time <= ", date("Y-") . $month . "-31 23:59:59");
        if ($type > 0)
            $this->db->where("ticket_status", $type);
        return $this->db->get()->result();
    }

    function daily_tickets($date, $type)
    {
        $this->db->select("ticket_id")
            ->from("tbl_tickets")
            ->where("deleted", false)
            ->where("ticket_time >= ", $date . " 00:00:00")
            ->where("ticket_time <= ", $date . " 23:59:59");
        if ($type > 0)
            $this->db->where("ticket_status", $type);
        return $this->db->get()->result();
    }

    function daily_closed_tickets($date)
    {
        $this->db->select("ticket_id")
            ->from("tbl_tickets")
            ->where("deleted", false)
            ->where("ticket_close_time >= ", $date . " 00:00:00")
            ->where("ticket_close_time <= ", $date . " 23:59:59");
        $this->db->where("ticket_status", 3);
        return $this->db->get()->result();
    }

    /*
     * check if a technician is already assigned a ticket
     * returns true if assigned else false
     */

    function isAssigned($technician_id, $ticket_id)
    {
        return count($this->db->get_where("tbl_ticket_staff", array("deleted" => 0, "ticket_id" => $ticket_id, "technician_id" => $technician_id))->row()) > 0;
    }

}
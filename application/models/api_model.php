<?php

/**
 * Created by PhpStorm.
 * User: mayne
 * Date: 4/23/16
 * Time: 6:30 AM
 */
class Api_model extends CI_Model
{
    protected $logged;

    function __construct()
    {
        parent::__construct();
        $this->logged = $this->users_model->user();
    }

    function user($device_id, $token)
    {
        return $this->db->select("*")
            ->from("tbl_users a")
            ->join("tbl_technicians b", "a.user_id = b.user_id")
            ->where("b.device_id", $device_id)
            ->where("b.device_token", $token)
            ->get()
            ->row();
    }

    function login()
    {
        $email = $this->input->post("username");
        $password = $this->input->post("password");

        return $this->db->select("*")
            ->from("tbl_users a")
            ->join("tbl_technicians b", "a.user_id = b.user_id")
            ->where("a.user_email", $email)
            ->where("a.user_password", md5($password))
            ->get()
            ->row();
    }

    function create_group($user_array)
    {
        $tokens = array();
        $data = implode("|", $user_array);
        $result = $this->db->select("device_token")
            ->from('tbl_technicians')
            ->where("user_id REGEXP BINARY ", "'[[:<:]]" . $data . "[[:>:]]'", FALSE)
            ->get()
            ->result();
        foreach ($result as $token) {
            array_push($tokens, $token->device_token);
        }
        return $tokens;
    }

    function update_token($token, $device_id)
    {
        if (count($this->crud_model->get_record("technicians", "device_id", $device_id)) == 0)
            return 0;
        return $this->db->where("device_id", $device_id)
            ->update("tbl_technicians", array("device_token" => $token, "last_update" => date("Y-m-d H:m")));

    }

    function notifications($device_id, $offset)
    {
        return $this->db->select("a.*,b.status")
            ->from("tbl_notifications a")
            ->join("tbl_notification_user b", "a.notification_id = b.notification_id")
            ->join("tbl_technicians c", "c.user_id = b.user_id")
            ->where("c.device_id", $device_id)
            ->where("a.notification_id > ", $offset)
            ->get()->result();
    }

    function one_notification($nu_id)
    {
        return $this->db->select("a.*,b.status")
            ->from("tbl_notifications a")
            ->join("tbl_notification_user b", "a.notification_id = b.notification_id")
            ->where("b.nu_id", $nu_id)
            ->get()->result();
    }

    function push($to, $message, $exc = false)
    {
        var_dump($message);
        // Set POST variables
        $url = 'https://gcm-http.googleapis.com/gcm/send';
        $headers = array(
            'Authorization: key=AIzaSyCRpiXXcwV-7zsVt5mmCrakRWgT4CNoNfs',
            'Content-Type: application/json'
        );
        $fields = array(
            "data" => array(
                "type" => $message->header,
                'message' => $message->message
            ),
            "registration_ids" => (!$exc ? $this->create_group($to) : $to));
        // Open connection
        $ch = curl_init();
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
        return $result;
    }

    function get_team($offset)
    {
        return $this->db->select("a.user_name, a.user_email, a.user_phone, b.last_update,b.technician_id")
            ->from("tbl_users a")
            ->join("tbl_technicians b", "a.user_id =  b.user_id")
            ->where("a.deleted", false)
            ->where("b.deleted", false)
            ->where("b.technician_id > ", $offset)
            ->get()->result();
    }

    function ticket_logs($user_id, $offset)
    {
        return $this->db->select("a.ticket_log_id,a.ticket_id,a.location,a.tl_action,a.tl_time as log_time,a.deleted,d.user_name as log_staff")
            ->from("tbl_ticket_log a")
            ->join("tbl_ticket_staff b", "a.ticket_id =  b.ticket_id")
            ->join("tbl_technicians c", "c.technician_id =  b.technician_id")
            ->join("tbl_users d", "d.user_id =  c.user_id")
            ->where(array("d.user_id" => $user_id, "a.ticket_log_id > " => $offset))
            ->get()
            ->result();
    }

    function ticket_items($technician_id, $offset)
    {
        //get ticket items
        $items = $this->db->select("a.*,b.item_code,b.item_serial_no,c.model_name ,d.make_name,e.it_name")
            ->from("tbl_ticket_items a")
            ->join("tbl_items b", "a.item_id = b.item_id")
            ->join("tbl_item_models c", "b.model_id = c.item_model_id")
            ->join("tbl_item_make d", "d.make_id = c.make_id")
            ->join("tbl_item_types e", "e.it_id = d.it_id")
            ->join("tbl_ticket_staff f", "f.ticket_id = a.ticket_id")
            ->where("f.technician_id", $technician_id)
            ->where("a.ticket_item_id > ", $offset)
            ->get()
            ->result();

        return $items;
    }

    function get_item($item_id)
    {
        $this->db->select("d.*,c.model_name ,b.make_name,a.it_name");
        $this->db->from('tbl_item_types a, tbl_item_make b, tbl_item_models c,tbl_items d');
        $this->db->where('c.item_model_id = d.model_id');
        $this->db->where('c.make_id =  b.make_id');
        $this->db->where('b.it_id = a.it_id');
        $this->db->where('d.item_id', $item_id);
        return $this->db->get()->row();
    }

    function get_tokens($ticket_id)
    {
        return $this->db->select("a.device_token")
            ->from("tbl_technicians a")
            ->join("tbl_ticket_staff b", " a.technician_id =  b.technician_id")
            ->where(array("b.ticket_id" => $ticket_id, "a.deleted" => false, "b.deleted" => false));
    }

    function pushTicket($ticket_id)
    {
        $ticket = $this->crud_model->get_record("tickets", "ticket_id", $ticket_id);
        //$message = $this->create_notification("Ticket " . $ticket->ticket_title . " details have been updated by" . $this->users_model->user($this->logged->user_name) . ". Kindly open the ticket details for more details.", "Ticket update");
        $message = (object)array(
            "type" => "dbContent",
            "message" => array(
                "table" => "tickets",
                "type" => "update",
                "values" => json_encode($ticket)
            )
        );
        $this->push($this->get_tokens($ticket_id), $message, true);
    }

    function create_notification($message, $title, $to)
    {
        $this->push($to, (object)array(
            "type" => "notification",
            "title" => $title,
            "message" => $message
        ), true);
    }

    //todo implement these functions

    function deassign_ticket($ticket_id)
    {

    }
}
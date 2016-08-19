<?php

/**
 * Created by PhpStorm.
 * User: mayne
 * Date: 4/18/16
 * Time: 4:35 AM
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller
{
    protected $token;
    protected $device_id;
    protected $technician;

    function __construct()
    {
        parent::__construct();
        if ($token = $this->input->post("token") == "")
            exit("No direct url access is allowed here. Got it? Cool...");
        $this->token = $this->input->post("token");
        $this->device_id = $this->input->post("device_id");
        $this->technician = $this->api_model->user($this->device_id, $this->token);
    }

    //done successfully
    function login()
    {
        $technician = $this->api_model->login();
        if (count($technician) == 1) {
            echo json_encode(array('error' => false, 'user' => $technician));
            $this->log_model->log("Logged into the system via maintenance app", $technician->user_id);
        } else {
            echo json_encode(array('error' => true, 'error_message' => "Invalid credentials!!! Kindly check and try again"));
        }
    }

//done successfully
    function read_notification()
    {
        $this->update_token();
        $notification_id = $this->input->post("notification_id");
        $this->db->trans_start();
        $notification = $this->crud_model->get_record("notifications", "notification_id", $notification_id);
        $notification_user = $this->db->get_where("tbl_notification_user", array("notification_id" => $notification_id, "user_id" => $this->technician->user_id))->row();
        $this->crud_model->update_record("notification_user", "nu_id", $notification_user->nu_id, array("status" => true));
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $this->log_model->log("Viewed notification (" . $notification->notification_header . ") via the app", $this->technician->user_id);
            $message = (object)array(
                "header" => "dbContent",
                "message" => array(
                    "table" => "notifications",
                    "type" => "update",
                    "values" => $this->api_model->one_notification($notification_user->nu_id)
                )
            );
            $this->api_model->push(array($this->token), $message, true);
            echo "ok";
            return;
        }
        echo "bad";
    }

//done successfully
    function get_data()
    {
        $this->update_token();
        $tickets = json_decode($this->input->post("tickets"));
        $logs = json_decode($this->input->post("logs"));
        $items = json_decode($this->input->post("items"));

//        $this->db->trans_start();
        $this->update_ticket();
        $this->process_items();
        $this->process_logs();

        $sites = '{"table":"sites","type":"add","values":' . json_encode($this->db->get_where("tbl_sites", array("site_id > " => $this->input->post("sites")))->result()) . '}';
        $technicians = '{"table":"technicians","type":"add","values":' . json_encode($this->api_model->get_team($this->input->post("technicians"))) . '}';
        $tickets = '{"table":"tickets","type":"add","values":' . json_encode($this->tickets_model->my_tickets($this->technician->user_id, $tickets->offset)) . '}';
        $notifications = '{"table":"notifications","type":"add","values":' . json_encode($this->api_model->notifications($this->device_id, $this->input->post("notifications"))) . '}';
        $logs = '{"table":"ticket_logs","type":"add","values":' . json_encode($this->api_model->ticket_logs($this->technician->user_id, $logs->offset)) . '}';
        $items = '{"table":"ticket_items","type":"add","values":' . json_encode($this->api_model->ticket_items($this->technician->technician_id, $items->offset)) . '}';

//        $this->db->trans_complete();
//        if ($this->db->trans_status())
        echo json_encode(array($sites, $technicians, $tickets, $notifications, $logs, $items));
//        else
//            echo "fail";
    }

//done successfully
    function update_token()
    {
        if (!$this->api_model->update_token($this->token, $this->device_id)) {
            $message = (object)array(
                "header" => "command",
                "message" => "logout"
            );
            $this->api_model->push(array($this->token), $message, true);
        }
    }

    function ticket_data()
    {
        $this->update_token();
        $ticket_id = $this->input->post("ticket_id");
        $logs_offset = $this->input->post("logs");
        $items_offset = $this->input->post("items");
        $logs = '{"table":"ticket_logs","type":"add","values":' . json_encode($this->api_model->ticket_logs($ticket_id, $logs_offset)) . '}';
        $items = '{"table":"ticket_items","type":"add","values":' . json_encode($this->api_model->ticket_items($ticket_id, $items_offset)) . '}';
        echo json_encode(array($logs, $items));
    }

    function verify_item()
    {
        $asset_type = $this->input->post("type");
        $asset = $this->input->post("asset_id");
        $mode = $this->input->post("mode");
        $ticket_id = $this->input->post("ticket_id");
        $ticket = $this->tickets_model->details($ticket_id);
        if ($asset_type == 2)
            $asset_id = $this->crud_model->get_record("equipment", "equipment_no", $asset);
        else
            $asset_id = $this->crud_model->get_record("items", ($mode == 1 ? "item_code" : "item_serial_no"), $asset);
        $asset_result = $this->equipment_model->location(($asset_type == 1 ? $asset_id->item_id : $asset_id->equipment_id), $asset_type);
        if (count($asset_result) == 0) {
            echo "Asset code provided does not exist in our records.";
            return;
        }

        //asset available in db
        $location_type = $asset_result->location_type;
        $location_id = $asset_result->location_id;
        $project = array();
        if ($asset_type == 2 && $location_type != 3)
            echo "Equipment is yet to be installed to any site.";
        elseif ($asset_type == 1 && $location_type != 2)
            echo "Item is yet to be installed to any equipment on a site.";
        else {
            if ($location_type == 3)
                $project = $this->projects_model->project($location_id);
            else {
                $eq_loc = $this->equipment_model->location($location_id, 2);
                if ($eq_loc->location_type != 3)
                    echo "Equipment is yet to be installed to any site.";
                else
                    $project = $this->projects_model->project($eq_loc->location_id);
            }
            if (count($project) > 0) {
                if ($project->site_id != $ticket->site_id)
                    echo "Asset found does not match this ticket's site.";
                elseif ($project->project_stage != 6) {
                    echo "Site installation not yet complete.";
                } else {
                    if ($asset_type == 2)
                        echo json_encode($asset_id);
                    else
                        //echo $asset_id->item_id;
                        echo json_encode($this->api_model->get_item($asset_id->item_id));
                }
            } else {
                echo "OOPS!!! An error occurred. ";
            }
        }
    }

    function update_ticket()
    {

        $tickets = json_decode($_POST['tickets'])->data;
        foreach ($tickets as $ticket) {

            //update ticket details
            $this->crud_model->update_record("tickets", "ticket_id", $ticket->ticketId, array(
                "ticket_close_time" => $ticket->closeTime,
                "ticket_status" => $ticket->status,
                "last_updated" => $ticket->lastUpdate
            ));

            //update user specific details
            $this->db->where(array("ticket_id" => $ticket->ticketId, "technician_id" => $this->technician->technician_id))
                ->update("tbl_ticket_staff", array(
                    "staff_report_time" => $ticket->openTime,
                    "staff_reported" => $ticket->openTime != 'null' ? 1 : 0,
                    "report" => $ticket->report
                ));
        }
    }


    function process_items()
    {
        $items = json_decode($_POST['items'])->data;
        foreach ($items as $item) {

            $this->crud_model->add_record("ticket_items",
                array(
                    "item_id" => $item->itemId,
                    "ticket_id" => $item->ticketId,
                    "fail_cause" => $item->failCause,
                    "verification_mode" => $item->verificationMode,
                    "resolution" => $item->resolution,
                    "scan_time" => $item->scanTime,
                    "resolve_time" => $item->resolveTime,
                    "user_id" => $item->userId)
            );
            if ($item->requireRepair) {
                $model = $this->api_model->get_item($item->itemId);
                $this->crud_model->add_record("asset_request",
                    array(
                        "model_id" => $model->model_id,
                        "request_qty" => 1,
                        "request_asset_type" => 1,
                        "request_category" => 4,
                        "request_category_id" => $this->technician->user_id,
                        "purpose" => "Replacement for faulty Item, CODE: " . $item->qrCode . " Problem:" . $item->failCause,
                        "request_level" => 4,
                        "requesting_user" => $this->technician->user_id
                    ));
            }
        }
    }

    function process_logs()
    {
        $logs = json_decode($_POST['logs'])->data;
        foreach ($logs as $log) {
            $this->crud_model->add_record("ticket_log", array(
                "ticket_id" => $log->ticketId,
                "user_id" => $this->technician->user_id,
                "location" => $log->logGeoLat,
                "tl_action" => $log->action,
                "tl_time" => $log->logTime
            ));
            $this->log_model->log($log->Action, $this->technician->user_id);
        }

    }

    function logout()
    {
        $this->log_model->log("Logged out of the system via maintenance app", $this->technician->user_id);
        $this->crud_model->update_record("technicians", "device_id", $this->device_id, array("device_token" => ""));
    }
}

/*
 * <?php

/**
 * Created by PhpStorm.
 * User: mayne
 * Date: 4/18/16
 * Time: 4:35 AM
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller
{
    protected $token;
    protected $device_id;
    protected $technician;

    function __construct()
    {
        parent::__construct();
        if ($token = $this->input->post("token") == "")
            exit("No direct url access is allowed here. Got it? Cool...");
        $this->token = $this->input->post("token");
        $this->device_id = $this->input->post("device_id");
        $this->technician = $this->api_model->user($this->device_id, $this->token);
    }

    //done successfully
    function login()
    {
        $technician = $this->api_model->login();
        if (count($technician) == 1) {
            echo json_encode(array('error' => false, 'user' => $technician));
            $this->log_model->log("Logged into the system via maintenance app", $technician->user_id);
        } else {
            echo json_encode(array('error' => true, 'error_message' => "Invalid credentials!!! Kindly check and try again"));
        }
    }

//done successfully
    function read_notification()
    {
        $this->update_token();
        $notification_id = $this->input->post("notification_id");
        $this->db->trans_start();
        $notification = $this->crud_model->get_record("notifications", "notification_id", $notification_id);
        $notification_user = $this->db->get_where("tbl_notification_user", array("notification_id" => $notification_id, "user_id" => $this->technician->user_id))->row();
        $this->crud_model->update_record("notification_user", "nu_id", $notification_user->nu_id, array("status" => true));
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $this->log_model->log("Viewed notification (" . $notification->notification_header . ") via the app", $this->technician->user_id);
            $message = (object)array(
                "header" => "dbContent",
                "message" => array(
                    "table" => "notifications",
                    "type" => "update",
                    "values" => $this->api_model->one_notification($notification_user->nu_id)
                )
            );
            $this->api_model->push(array($this->token), $message, true);
            echo "ok";
            return;
        }
        echo "bad";
    }

//done successfully
    function get_data()
    {
        $this->update_token();
        $tickets = json_decode($this->input->post("tickets"));
        $logs = json_decode($this->input->post("logs"));
        $items = json_decode($this->input->post("items"));

//        $this->db->trans_start();
        $this->update_ticket();
        $this->process_items();
        $this->process_logs();

        $sites = '{"table":"sites","type":"add","values":' . json_encode($this->db->get_where("tbl_sites", array("site_id > " => $this->input->post("sites")))->result()) . '}';
        $technicians = '{"table":"technicians","type":"add","values":' . json_encode($this->api_model->get_team($this->input->post("technicians"))) . '}';
        $tickets = '{"table":"tickets","type":"add","values":' . json_encode($this->tickets_model->my_tickets($this->technician->user_id, $tickets->offset)) . '}';
        $notifications = '{"table":"notifications","type":"add","values":' . json_encode($this->api_model->notifications($this->device_id, $this->input->post("notifications"))) . '}';
        $logs = '{"table":"ticket_logs","type":"add","values":' . json_encode($this->api_model->ticket_logs($this->technician->user_id, $logs->offset)) . '}';
        $items = '{"table":"ticket_items","type":"add","values":' . json_encode($this->api_model->ticket_items($this->technician->technician_id, $items->offset)) . '}';

//        $this->db->trans_complete();
//        if ($this->db->trans_status())
        echo json_encode(array($sites, $technicians, $tickets, $notifications, $logs, $items));
//        else
//            echo "fail";
    }

//done successfully
    function update_token()
    {
        if (!$this->api_model->update_token($this->token, $this->device_id)) {
            $message = (object)array(
                "header" => "command",
                "message" => "logout"
            );
            $this->api_model->push(array($this->token), $message, true);
        }
    }

    function ticket_data()
    {
        $this->update_token();
        $ticket_id = $this->input->post("ticket_id");
        $logs_offset = $this->input->post("logs");
        $items_offset = $this->input->post("items");
        $logs = '{"table":"ticket_logs","type":"add","values":' . json_encode($this->api_model->ticket_logs($ticket_id, $logs_offset)) . '}';
        $items = '{"table":"ticket_items","type":"add","values":' . json_encode($this->api_model->ticket_items($ticket_id, $items_offset)) . '}';
        echo json_encode(array($logs, $items));
    }

    function verify_item()
    {
        $asset_type = $this->input->post("type");
        $asset = $this->input->post("asset_id");
        $mode = $this->input->post("mode");
        $ticket_id = $this->input->post("ticket_id");
        $ticket = $this->tickets_model->details($ticket_id);
        if ($asset_type == 2)
            $asset_id = $this->crud_model->get_record("equipment", "equipment_no", $asset);
        else
            $asset_id = $this->crud_model->get_record("items", ($mode == 1 ? "item_code" : "item_serial_no"), $asset);
        $asset_result = $this->equipment_model->location(($asset_type == 1 ? $asset_id->item_id : $asset_id->equipment_id), $asset_type);
        if (count($asset_result) == 0) {
            echo "Asset code provided does not exist in our records.";
            return;
        }

        //asset available in db
        $location_type = $asset_result->location_type;
        $location_id = $asset_result->location_id;
        $project = array();
        if ($asset_type == 2 && $location_type != 3)
            echo "Equipment is yet to be installed to any site.";
        elseif ($asset_type == 1 && $location_type != 2)
            echo "Item is yet to be installed to any equipment on a site.";
        else {
            if ($location_type == 3)
                $project = $this->projects_model->project($location_id);
            else {
                $eq_loc = $this->equipment_model->location($location_id, 2);
                if ($eq_loc->location_type != 3)
                    echo "Equipment is yet to be installed to any site.";
                else
                    $project = $this->projects_model->project($eq_loc->location_id);
            }
            if (count($project) > 0) {
                if ($project->site_id != $ticket->site_id)
                    echo "Asset found does not match this ticket's site.";
                elseif ($project->project_stage != 6) {
                    echo "Site installation not yet complete.";
                } else {
                    if ($asset_type == 2)
                        echo json_encode($asset_id);
                    else
                        //echo $asset_id->item_id;
                        echo json_encode($this->api_model->get_item($asset_id->item_id));
                }
            } else {
                echo "OOPS!!! An error occurred. ";
            }
        }
    }

    function update_ticket()
    {

        $tickets = json_decode($_POST['tickets'])->data;
        foreach ($tickets as $ticket) {

            //update ticket details
            $this->crud_model->update_record("tickets", "ticket_id", $ticket->ticketId, array(
                "ticket_close_time" => $ticket->closeTime,
                "ticket_status" => $ticket->status,
                "last_updated" => $ticket->lastUpdate
            ));

            //update user specific details
            $this->db->where(array("ticket_id" => $ticket->ticketId, "technician_id" => $this->technician->technician_id))
                ->update("tbl_ticket_staff", array(
                    "staff_report_time" => $ticket->openTime,
                    "staff_reported" => $ticket->openTime != 'null' ? 1 : 0,
                    "report" => $ticket->report
                ));
        }
    }


    function process_items()
    {
        $items = json_decode($_POST['items'])->data;
        foreach ($items as $item) {

            $this->crud_model->add_record("ticket_items",
                array(
                    "item_id" => $item->itemId,
                    "ticket_id" => $item->ticketId,
                    "fail_cause" => $item->failCause,
                    "verification_mode" => $item->verificationMode,
                    "resolution" => $item->resolution,
                    "scan_time" => $item->scanTime,
                    "resolve_time" => $item->resolveTime,
                    "user_id" => $item->userId)
            );
            if ($item->requireRepair) {
                $model = $this->api_model->get_item($item->itemId);
                $this->crud_model->add_record("asset_request",
                    array(
                        "model_id" => $model->model_id,
                        "request_qty" => 1,
                        "request_asset_type" => 1,
                        "request_category" => 4,
                        "request_category_id" => $this->technician->user_id,
                        "purpose" => "Replacement for faulty Item, CODE: " . $item->qrCode . " Problem:" . $item->failCause,
                        "request_level" => 4,
                        "requesting_user" => $this->technician->user_id
                    ));
            }
        }
    }

    function process_logs()
    {
        $logs = json_decode($_POST['logs'])->data;
        foreach ($logs as $log) {
            $this->crud_model->add_record("ticket_log", array(
                "ticket_id" => $log->ticketId,
                "user_id" => $this->technician->user_id,
                "location" => $log->logGeoLat,
                "tl_action" => $log->action,
                "tl_time" => $log->logTime
            ));
            $this->log_model->log($log->Action, $this->technician->user_id);
        }

    }

    function logout()
    {
        $this->log_model->log("Logged out of the system via maintenance app", $this->technician->user_id);
        $this->crud_model->update_record("technicians", "device_id", $this->device_id, array("device_token" => ""));
    }
}
 */
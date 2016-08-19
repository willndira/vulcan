<?php

/**
 * Created by PhpStorm.
 * User: mayne
 * Date: 6/25/16
 * Time: 1:42 PM
 */
class Reports extends CI_Controller
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
        $var['page'] = 'System Reports';
        $this->load->template('reports_view', $var);
    }

    function maintenance()
    {
        $var['page'] = 'Maintenance Reports';
        $var['styles'] = array(
            'css/plugins/daterangepicker'
        );
        $var['scripts'] = array(
            'plugins/daterange/moment',
            'plugins/daterange/daterangepicker',
//            'https://cdn.datatables.net/buttons/1.2.1/js/dataTables.buttons.min.js',
//            '//cdn.datatables.net/buttons/1.2.1/js/buttons.flash.min.js',
//            '//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js',
//            '//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js',
//            '//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js',
//            '//cdn.datatables.net/buttons/1.2.1/js/buttons.html5.min.js',
//            '//cdn.datatables.net/buttons/1.2.1/js/buttons.print.min.js',
        );
        $var['filters'] = $this->report_filters();
        $var["report"] = $this->gen_main_report();
        $this->load->template('maintenance_reports_view', $var);
    }

    private function sites($from, $to)
    {
        return $this->crud_model->read("tickets", array("deleted" => false, "ticket_time >=" => $from, "ticket_time <=" => $to));
    }

    private function closed($from, $to, $is_open = false)
    {
        $key = ($is_open ? "ticket_time" : "ticket_time");
        return $this->crud_model->read("tickets", array("deleted" => false, $key . " >=" => $from, $key . " <=" => $to));
    }

    private function remote($from, $to, $is_remote = false)
    {
        return $this->crud_model->read("tickets", array("deleted" => false, "is_remote" => $is_remote, "ticket_time >=" => $from, "ticket_time <=" => $to));
    }

    function report_filters()
    {
        return array("sites" => "Sites", "technician" => "Technicians", "raised" => "Raised Tickets", "closed" => "Closed Tickets", "remote" => "Remote Tickets", "non_remote" => "Non Remote Tickets");
    }

    function tickets($tickets, $group = false)
    {
        $contents = array();
        $header = array("Site Manager", "Assigned To", "Site", "Escalation", "Diagnosis", "Type", "Report Time", "Resolve Time");
        foreach ($tickets as $ticket) {
            $site = $this->crud_model->read_one("sites", array("site_id" => $ticket->site_id));
            $asigned = "";
            foreach ($this->crud_model->read("ticket_staff", array("deleted" => false, "ticket_id" => $ticket->ticket_id)) as $assignment) {
                $tech = $this->crud_model->read_one("technicians", array("technician_id" => $assignment->technician_id));
                $asigned .= $this->crud_model->read_one("users", array("user_id" => $tech->user_id))->user_name;
            }
            $site_manager = $this->crud_model->read_one("site_manager", array("deleted" => false, "check_in >=" => $ticket->ticket_time, "check_out <=" => $ticket->ticket_time));
            array_push($contents, array(
                count($site_manager) == 0 ? "Not Assigned" : ($this->crud_model->read_one("users", array("user_id" => $site_manager->staff_id))->user_name),
                $asigned,
                $site->site_name,
                $ticket->ticket_issue,
                $ticket->is_remote ? "Remote" : "Site Visit",
                $ticket->ticket_report,
                $ticket->ticket_time,
                is_null($ticket->ticket_close_time) ? "Not Resolved" : $ticket->ticket_close_time,
            ));
        }
        return $this->load->view("reports/tickets", array("title" => "Site", "group" => $group, "header" => $header, "content" => $contents), true);
    }

    private function gen_main_report()
    {
        $filter = $_POST['filter'];
        $to = $_POST['mpaka_submit'];
        $from = $_POST['kutoka_submit'];
        $contents = array();

        switch ($filter) {
            case  'site':
                return $this->tickets($this->sites($from, $to), true);
            case  'closed':
                return $this->tickets($this->closed($from, $to), true);
            case  'remote':
                return $this->tickets($this->remote($from, $to, true), true);
            case  'non_remote':
                return $this->tickets($this->remote($from, $to), true);
            case  'diagram':
                return $this->draw_diagram($from, $to);
            case 'technician';
                $header = array("Date", "Site", "Technician", "Escalation", "Type", "Role", "Report", "Resolve Time");
                foreach ($this->crud_model->read("technicians", array("deleted" => false)) as $tech) {
                    $tickets = $this->crud_model->read("ticket_staff", array("deleted" => false, "technician_id" => $tech->technician_id, "ts_time >= " => $from, "ts_time <= " => $to));
                    foreach ($tickets as $ticket) {
                        $tk = $this->crud_model->read_one("tickets", array("ticket_id" => $ticket->ticket_id));
                        $site = $this->crud_model->read_one("sites", array("site_id" => $tk->site_id));
                        array_push($contents, array(
                            $ticket->ts_time,
                            $site->site_name,
                            $this->crud_model->read_one("users", array("user_id" => $tech->user_id))->user_name,
                            $tk->ticket_issue,
                            $ticket->is_remote ? "Remote" : "Site Visit",
                            $ticket->staff_role,
                            $ticket->report,
                            is_null($tk->ticket_close_time) ? "Not Resolved" : $tk->ticket_close_time,
                        ));
                    }
                }
                return $this->load->view("reports/tickets", array("title" => "Technician Assignments ", "group" => true, "header" => $header, "content" => $contents), true);
            default:
                return (object)array("title" => $this->sites("0000-00-00 00:00:00", date("Y-m-d H:m:s")), "data" => "Site Reports");
        }
    }

    private function draw_diagram($from, $to)
    {
        $header = array("Site/Day");
        $contents = array();
        $from_2 = $from;
        while (strtotime($from) <= strtotime($to)) {
            array_push($header, $from);
            $from = date("Y-m-d", strtotime("+1 day", strtotime($from)));

        }

        foreach ($this->crud_model->read("sites", array("deleted" => false)) as $site) {
            $occurence = array();
            array_push($occurence, '<b>' . $site->site_name . '</b>');
            $from_1 = $from_2;
            while (strtotime($from_1) <= strtotime($to)) {
                array_push($occurence, $this->check_occurence($site->site_id, $from_1));
                $from_1 = date("Y-m-d", strtotime("+1 day", strtotime($from_1)));
            }
            array_push($contents, $occurence);
        }
        return $this->load->view("reports/tickets", array("title" => "Technician Assignments ", "group" => false, "header" => $header, "content" => $contents), true);
    }

    private function check_occurence($site, $from)
    {
        $site_tickets = $this->crud_model->read("tickets", array("deleted" => false, "site_id" => $site, "ticket_time >=" => $from . " 00:00:00", "ticket_time <=" => $from . " 23:59:59"));
        if (count($site_tickets) > 0) {
            return "<span class='orange-text'> YES(" . count($site_tickets) . ")</span>";
        }
        return "<span class='green-text'>NO</span>";
    }
}
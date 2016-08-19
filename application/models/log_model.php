<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Log_model extends CI_Model
{
    protected $ip_address;
    protected $data;
    protected $user;

    function __construct()
    {
        parent::__construct();
        $this->user = $this->users_model->user();
    }

    function data()
    {
        $datestring = "%Y-%m-%d %H:%i:%s";
        $time = time();
        $this->ip_address = $_SERVER['REMOTE_ADDR'];
        $this->data = mdate($datestring, $time) . "|" . $this->ip_address . "|";
    }

    function log($message, $user = false)
    {
        $this->data();
        if ($user)
            $this->user = $user;
        $g_data = $this->data;
        $this->data .= $this->user->user_name . "|"
            . $this->user->user_id . "|"
            . $message;
        $this->write_activity();
        $this->data = $g_data . $message;
        $this->write_user_activity($this->user);
    }

    function log_sys($message)
    {
        $this->data();
        $this->data .= $message;
        write_file('./application/logs/system', $this->data . "%\n", 'a+');
    }


    function write_error($data)
    {
        $this->data();
        write_file('./application/logs/errors', $this->data . $data . "%\n", 'a+');
    }

    function write_activity()
    {
        write_file('./application/logs/system_use', $this->data . "%\n", 'a+');
    }

    function write_user_activity($user)
    {
        write_file('./application/logs/' . $user->user_id . '-system_use', $this->data . "%\n", 'a+');
    }
}
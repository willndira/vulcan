<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 1/17/16
 * Time: 11:18 PM
 */
class Logout extends CI_Controller
{

    function index()
    {
        if (null != $this->session->userdata('logged_admin'))
            $this->log_model->log('logged out of the system');
        $this->session->sess_destroy();
        redirect('login', 'refresh');
    }
}
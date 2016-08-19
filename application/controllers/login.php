<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 12/20/15
 * Time: 3:27 PM
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Login extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function validate()
    {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|callback_check_database');

        if ($this->form_validation->run() == FALSE) {
            $this->index();
        } else {
            redirect('dashboard', 'refresh');
        }
    }

    public function index()
    {
        if (null != $this->session->userdata('logged_admin') && count($this->users_model->user()) > 0)
            redirect('dashboard', 'refresh');
        $data['recover'] = false;
        $this->load->view('login_view', $data);
    }

    function check_database($password)
    {
        $email = $this->input->post('email');
        $result = $this->users_model->login($email, $password);
        if ($result) {
            if ($result->user_access) {
                $this->session->set_userdata('logged_admin', $result->user_id);
                $this->log_model->log('logged into the system', $result);
                return TRUE;
            } else
                $this->form_validation->set_message('check_database', '<b>Access denied!</b><br/> Your account access has been blocked.');
        } else {
            $this->form_validation->set_message('check_database', '<b>Invalid credentials</b><br/> Check and try again please');
        }
        return false;
    }
}

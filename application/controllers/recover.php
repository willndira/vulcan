<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 1/17/16
 * Time: 11:17 PM
 */
class Recover extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (null != $this->session->userdata('logged_admin'))
            redirect('dashboard', 'refresh');
        $data['recover'] = true;
        $this->load->view('login_view', $data);
    }

    function validate()
    {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|callback_check_email');
        if ($this->form_validation->run() == FALSE) {
            $this->index();
        } else {
            $this->session->set_flashdata('success', '<b>Success!</b><br/> New password sent to your email address.');
            redirect('login');
        }
    }

    function check_email($email)
    {
        if ($this->users_model->reset_password($email)) {
            return true;
        } else {
            $this->form_validation->set_message('check_email', '<b>Email not found!</b><br/>Sorry, the email provided does not exist in our records.');
        }
        return false;
    }

}
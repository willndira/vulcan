<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 1/19/16
 * Time: 11:57 PM
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Profile extends CI_Controller
{
    function  __construct()
    {
        parent::__construct();
        $this->users_model->security();
    }

    function index()
    {
        $var['page'] = 'My profile';
        $var['details'] = $this->users_model->user();
        $this->load->template('profile_view', $var);
    }

    function update()
    {
        $this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'required');
        $this->form_validation->set_rules('phone', 'Phone', 'required');
        $this->form_validation->set_rules('about', 'About', 'required');
        $this->form_validation->set_rules('category', 'User level', 'required');
        if ($this->input->post('pass1')) {
            $this->form_validation->set_rules('pass1', 'New password', 'matches[pass2]');
            $this->form_validation->set_rules('pass2', 'Confirmation password', 'required');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->user(urlencode(base64_encode($this->input->post('user_id'))));
        } else {
            $data = array(
                'user_name' => $this->input->post('first_name') . ' ' . $this->input->post('last_name'),
                'user_email' => $this->input->post('email'),
                'user_phone' => $this->input->post('phone'),
                'user_category_id' => $this->input->post('category'),
                'user_about' => $this->input->post('about'),
                'user_status' => $this->input->post('status'),
                'user_access' => $this->input->post('access')
            );
            if (isset($_FILES['userfile'])) {
                $this->users_model->upload_pic();
            }
            if (null != $this->input->post('pass1')) {
                $data['user_password'] = md5($this->input->post('pass1'));
            }
            if ($this->input->post('user_id')) {
                if ($this->users_model->update_user($data, $this->input->post('user_id'))) {
                    $this->session->set_flashdata('success', "User details updated");
                } else
                    $this->session->set_flashdata('error', "User details failed to update. Unknown error occurred");
            } else {
                $this->session->set_flashdata('error', "User details failed to update. Unknown user profile detected");
            }
            redirect('profile/user/' . urlencode(base64_encode($this->input->post('user_id'))), 'refresh');
        }
    }

    function user($user_id)
    {
        $var['page'] = 'User profile';
        $var['details'] = $this->users_model->user(base64_decode(urldecode($user_id)));
        $this->load->template('profile_view', $var);
    }
}
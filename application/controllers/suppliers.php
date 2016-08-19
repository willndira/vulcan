<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 2/2/16
 * Time: 6:12 PM
 */
class Suppliers extends CI_Controller
{
    private $user_id;

    function  __construct()
    {
        parent::__construct();
        $this->users_model->security();
        $this->user_id = $this->users_model->user()->user_id;
    }

    function index()
    {
        $var['page'] = 'Suppliers';
        $var['suppliers'] = $this->crud_model->get_records('suppliers');
        $this->load_page('supplier_view', $var);
    }

    function load_page($page, $var)
    {
        $this->load->template($page, $var);
    }

    function profile($supplier_id)
    {
        $var['page'] = 'Supplier Profile';
        $this->load_page('supplier_profile_view', $var);
    }

    function register()
    {
        $this->form_validation->set_rules('name', 'Supplier Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_check_mail');
        $this->form_validation->set_rules('phone', 'Phone', 'required|callback_check_phone');
        $this->form_validation->set_rules('cheque', 'On cheque name', 'required');
        $this->form_validation->set_rules('validity', 'Validity', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->index();
        } else {
            $data = array(
                'supplier_name' => $this->input->post('name'),
                'supplier_email' => $this->input->post('email'),
                'supplier_phone' => $this->input->post('phone'),
                'supplier_cheque' => $this->input->post('cheque'),
                'supplier_status' => $this->input->post('validity'),
                'supplier_added_by' => $this->user_id
            );
            $supplier = $this->crud_model->add_record('suppliers', $data);
            if ($supplier) {
                $this->session->set_flashdata('success', 'Supplier details captured successfully');
            }
            redirect('suppliers', 'refresh');
        }
    }

    function check_mail($mail_address)
    {
        if ($this->crud_model->get_record('suppliers', 'supplier_email', $mail_address)) {
            $this->form_validation->set_message('check_mail', '<b>Email exists!</b><br/> Supplier with the same email address already exists.');
            return false;
        }
        return true;
    }

    function check_phone($phone)
    {
        if ($this->crud_model->get_record('suppliers', 'supplier_phone', $phone)) {
            $this->form_validation->set_message('check_phone', '<b>Phone number exists!</b><br/> Supplier with the same phone number already exists.');
            return false;
        }
        return true;
    }
}
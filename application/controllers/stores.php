<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 2/11/16
 * Time: 10:19 PM
 */
class Stores extends CI_Controller
{
    private $admin_id;

    function __construct()
    {
        parent::__construct();
        $this->users_model->security();
        $this->admin_id = $this->users_model->user()->user_id;
    }

    function profile($store_id)
    {
        $var['details'] = $this->crud_model->get_record('stores', 'store_type', base64_decode(urldecode($store_id)));
        $var['man'] = $this->stores_model->manager($var['details']->store_id);
        $var['page'] = $var['details']->store_name;
        $this->load_page('store_interface', $var);
    }

    function load_page($page, $var)
    {
        /* $var['styles'] = array(
             'js/plugins/data-tables/css/jquery.dataTables.min'
         );
         $var['scripts'] = array(
             'plugins/data-tables/js/jquery.dataTables.min',
             'plugins/data-tables/data-tables-script'
         );*/
        $this->load->template($page, $var);
    }

    function holding()
    {
        $var['page'] = 'Available Items';
        $this->load_page('holding_stock', $var);
    }

    function requests()
    {
        $var['page'] = 'Requested Items';
        $var['requests'] = array();
        $this->load_page('store_requests', $var);
    }

    function register()
    {
        $this->val();
        if ($this->form_validation->run() == FALSE) {
            $this->index();
        } else {
            $data = array(
                'store_name' => $this->input->post('store_name'),
                // 'store_address' => $this->input->post('store_address'),
                //'store_phone' => $this->input->post('phone'),
                'store_type' => $this->input->post('store_type'),
                'added_by' => $this->session->userdata('logged_admin')
            );
            if ($store_id = $this->stores_model->create($data)) {
                $this->stores_model->assign_manager($store_id, $this->input->post('manager'));
                $this->session->set_flashdata('success', 'Store created successfully');
            }
            $store_type = $this->input->post('store_type');
            redirect('stores/' . ($store_type == 1 ? "main" : ($store_type == 2 ? "assembly" : ($store_type == 3 ? "installation" : "maintenance"))), 'refresh');
        }
    }

    function val()
    {
        $this->form_validation->set_rules('store_name', 'Store Name', 'trim|required');
        $this->form_validation->set_rules('store_type', 'Store type', 'trim|required');
        $this->form_validation->set_rules('store_address', 'store address', 'trim');
        $this->form_validation->set_rules('phone', 'Phone', 'trim');
        $this->form_validation->set_rules('manager', 'Store manager', 'required|numeric');
    }

    function index()
    {
        $var['page'] = 'KAPS Stores';
        $var['stores'] = $this->crud_model->get_records('stores');
        $this->load_page('stores_view', $var);
    }

    function update()
    {
        $this->val();
        $store_id = $this->input->post('store_id');
        if ($this->form_validation->run() == FALSE) {
            $this->index();
        } else {
            $data = array(
                'store_name' => $this->input->post('store_name'),
                'store_address' => $this->input->post('store_address'),
                'store_type' => $this->input->post('store_type')
            );
            $this->db->trans_start();
            if ($this->crud_model->update_record('stores', 'store_id', $store_id, $data)) {
                $f_manager = $this->stores_model->manager($store_id);
                if ($f_manager != $this->input->post('manager')) {
                    if (count($f_manager = $this->stores_model->manager($store_id)) > 0)
                        $this->stores_model->unassign_manager($store_id, $f_manager->user_id);
                    $this->stores_model->assign_manager($store_id, $this->input->post('manager'));
                }
                $this->db->trans_complete();
                $this->session->set_flashdata('success', 'Store details updated');
            }
            redirect('stores/profile/' . urlencode(base64_encode($this->input->post('store_type'))), 'refresh');
        }
    }

    function issue()
    {
        $this->form_validation->set_rules('requisition_id', 'Requisition Id', 'trim|required');
        $this->form_validation->set_rules('items', 'Issued Items', 'required');
        $requisition_id = $this->input->post('requisition_id');
        $items = $this->input->post('items');
        if ($this->form_validation->run() == FALSE) {
            $this->review(urlencode(base64_encode($requisition_id)));
        } else {
            foreach ($items as $item) {
                $data = array(
                    'requisition_id' => $requisition_id,
                    'item_id' => $item,
                    'issued_by' => $this->admin_id
                );
                $this->crud_model->add_record('requisition_issued', $data);
            }
            $this->session->set_flashdata('success', 'Items issued successfully.');
            redirect('stores/review/' . urlencode(base64_encode($requisition_id)));
            /*
             * TODO: notify requester
             */
        }
    }

    function review($request_id)
    {
        $request = $this->crud_model->get_record('requisitions', 'requisition_id', base64_decode(urldecode($request_id)));
        if (!$request) {
            redirect("unkown", "refresh");
            return;
        }
        $var['details'] = $request;
        $var['page'] = 'Request Review';
        $this->load_page('store_request_review', $var);
    }
}
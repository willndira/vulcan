<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 1/26/16
 * Time: 11:23 PM
 */
class Requisitions extends CI_Controller
{
    protected $admin_id;

    function __construct()
    {
        parent::__construct();
        $this->users_model->security();
        $this->admin_id = $this->users_model->user()->user_id;
    }

    function index()
    {
        $var['page'] = 'Item Requisitions';
        $var['pending'] = true;
        $var['requests'] = $this->crud_model->get_records('requisitions');
        $this->load_page('requisition_view', $var);
    }

    function load_page($page, $var)
    {
        $this->load->template($page, $var);
    }

    function update($requisition_id)
    {
        $this->val();
        if ($this->form_validation->run() == FALSE) {
            $this->mine();
        } else {
            $data = array(
                'item_model_id' => $this->input->post('model'),
                'requisition_purpose' => $this->input->post('reason'),
                'requisition_units' => $this->input->post('units')
            );
            if ($this->crud_model->update_record('requisitions', 'requisition_id', $requisition_id, $data)) {
                $this->session->set_flashdata('success', 'Requisition updated successfully');
                $this->crud_model->add_record('requisition_timeline', array(
                    'requisition_id' => $requisition_id,
                    'rt_action' => 'Details changed',
                    'user_id' => $this->admin_id
                ));
            }
            $this->log_model->log('Changed details  for requisition #' . $requisition_id);
            redirect('requisitions/mine', 'refresh');
        }
    }

    function val()
    {
        $this->form_validation->set_rules('model', 'Item model', 'trim|numeric|required');
        $this->form_validation->set_rules('units', 'requested units', 'trim|numeric|required');
        $this->form_validation->set_rules('reason', 'Purpose', 'required');
    }

    function mine($requision_id = false)
    {
        $var['page'] = 'My Requisitions';
        $var['edit'] = $requision_id;
        if ($requision_id) {
            if ($this->items_model->requisition_status($requision_id) != 'Pending procurement review') {
                $this->session->set_flashdata('error', 'Requisition can not be altered. It has already been reviewed');
                redirect('requisitions/mine', 'refresh');
            } else {
                $var['requisition'] = $this->items_model->requistion_details($requision_id);
                $var['model'] = $this->items_model->get_model_details($var['requisition']->item_model_id);
            }
        }
        $this->load_page('my_requisition_view', $var);
    }

    function asset($request_id)
    {
        $request = base64_decode(urldecode($request_id));
        $var['page'] = 'Asset Request';
        $var["details"] = $this->crud_model->get_record("asset_request", "request_id", $request);
        if ($var["details"]->request_asset_type == 1)
            $this->load_page('asset_request_profile', $var);
        if ($var["details"]->request_asset_type == 2)
            $this->load_page('equipment_request_profile', $var);
    }

    function store_request()
    {
        $this->form_validation->set_rules('from_store', 'Store to request from ', 'trim|numeric|required');
        $this->form_validation->set_rules('item_id', 'Asset required', 'trim|numeric|required');
        $this->form_validation->set_rules('this_store', 'Requesting', 'required');
        $this->form_validation->set_rules('item_qty', 'number of assets required', 'required');
        $this->form_validation->set_rules('item_purpose', 'Purpose of your request', 'required');
        if ($this->form_validation->run() == FALSE) {
            $var['details'] = $this->crud_model->get_record('stores', 'store_type', $this->input->post("this_store"));
            $var['man'] = $this->stores_model->manager($var['details']->store_id);
            $var['page'] = $var['details']->store_name;
            $this->load_page('store_interface', $var);
        } else {
            $this->db->trans_start();
            $this->crud_model->add_record("asset_request",
                array(
                    "model_id" => $this->input->post("item_id"),
                    "request_qty" => $this->input->post("item_qty"),
                    "request_asset_type" => 1,
                    "request_category" => 1,
                    "request_category_id" => $this->input->post("this_store"),
                    "purpose" => $this->input->post("item_purpose"),
                    "request_level" => $this->input->post("from_store"),
                    "requesting_user" => $this->admin_id
                ));
            $this->db->trans_complete();
            if ($this->db->trans_status()) {

                $this->log_model->log('Requested assets from another store');
                $this->session->set_flashdata('success', 'Asset requisition posted successfully.');
                redirect('stores/profile/' . urlencode(base64_encode($this->input->post("this_store"))), 'refresh');
            }
        }
    }

}
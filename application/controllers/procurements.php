<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 1/30/16
 * Time: 4:06 AM
 */
class Procurements extends CI_Controller
{
    protected $admin_id;

    function  __construct()
    {
        parent::__construct();
        $this->users_model->security();
        $this->admin_id = $this->users_model->user()->user_id;
    }

    function index()
    {
        $var['page'] = 'Reviews Procurement Requests';
        $var['pending'] = false;
        $var['requests'] = $this->items_model->reviewed_requisitions();
        $this->load_page('requisition_view', $var);
    }

    function pending()
    {
        $var['page'] = 'New Procurement Requests';
        $var['pending'] = true;
        $var['requests'] = $this->items_model->unreviewed_requisitions();
        $this->load_page('requisition_view', $var);
    }

    function load_page($page, $var)
    {
        $this->load->template($page, $var);
    }

    function profile($requisition_id)
    {
        $requisition_id = base64_decode(urldecode($requisition_id));
        if (!$req = $this->crud_model->get_record('requisitions', 'requisition_id', $requisition_id)) {
            redirect('unkown', 'refresh');
            return;
        }
        $var['page'] = 'Requisition Profile';
        $var['details'] = $req;
        $this->load_page('requisition_profile', $var);
    }

    function review()
    {
        $this->form_validation->set_rules('verdict', 'Review verdict', 'required');
        $this->form_validation->set_rules('comment', 'Review comment', 'required');
        if ($this->input->post('verdict') == 1) {
            $this->form_validation->set_rules('unit_cost', 'Item unit cost', 'required|numeric');
            $this->form_validation->set_rules('tax', 'Sales Tax', 'required');
            $this->form_validation->set_rules('terms', 'Purchase terms', 'required');
            $this->form_validation->set_rules('delivery', 'Mode of delivery', 'required');
            $this->form_validation->set_rules('due_date', 'Delivery Date', 'required');
            $this->form_validation->set_rules('terms', 'Item unit cost', 'required');
            $this->form_validation->set_rules('supplier', 'Supplier', 'required|numeric');
        }
        if ($this->form_validation->run() == FALSE) {
            $this->profile(urlencode(base64_encode($this->input->post('rqid'))));
        } else {
            $this->session->set_flashdata('success', 'Requisition review posted successfully');
            $data = array(
                'requisition_id' => $this->input->post('rqid'),
                'review_verdict' => $this->input->post('verdict'),
                'review_comment' => $this->input->post('comment'),
                'review_officer' => $this->admin_id
            );
            if ($lpo = $this->items_model->review($data)) {
                $this->users_model->notify(
                    $this->users_model->user($this->crud_model->get_record('requisitions', 'requisition_id', $this->input->post('rqid'))),
                    array('header' => 'Requisition Review', 'message' => 'Your requisition has been reviewed.')
                );
                if ($this->input->post('verdict')) {
                    redirect('procurements/lpo/' . $lpo, 'refresh');
                } else {
                    redirect('procurements/profile/' . urlencode(base64_encode($this->input->post('rqid'))), 'refresh');
                }
            }
        }
    }

    function edit_review($rr_id)
    {
        $var['page'] = 'Edit Requisition Review';
        $var['r_id'] = $rr_id;
        $this->load_page('edit_review', $var);
    }

    function lpo($lpo_id = false)
    {
        if ($lpo_id) {
            $var['page'] = 'Purchase Order NO.' . $lpo_id;
            $var['po'] = $this->crud_model->get_record('purchase_orders', 'po_id', $lpo_id);
            $this->load_page('lpo_view', $var);
        } else {
            $var['page'] = 'Purchase Orders';
            $this->load_page('all_lpo_view', $var);
        }
    }

    function accept_fields()
    {
        ?>
        <div class="input-field col s6">
            <select name="supplier" id="supplier" class="browser-default">
                <option selected disabled>Select Qualified Supplier</option>
                <?php
                foreach ($this->crud_model->get_records('suppliers') as $supplier) {
                    ?>
                    <option value="<?= $supplier->supplier_id ?>"><?= $supplier->supplier_name ?></option>
                    <?php
                }
                ?>
            </select>
            <label for="supplier" class="active">Qualified Supplier</label>
        </div>
        <div class="input-field col s6">
            <select name="store" id="store" class="browser-default">
                <option selected disabled>Select Store to be supplied</option>
                <?php
                foreach ($this->crud_model->get_records('stores') as $store) {
                    ?>
                    <option value="<?= $store->store_id ?>"><?= $store->store_name ?></option>
                    <?php
                }
                ?>
            </select>
            <label for="store" class="active">Receiving Store</label>
        </div>
        <div class="input-field col s6">
            <input id="unit_cost" type="number" name="unit_cost" required>
            <label for="unit_cost">Unit Cost (KES)</label>
        </div>
        <div class="input-field col s6">
            <input id="due_date" type="date" name="due_date" required>
            <label for="due_date" class="text-black">Due Date</label>
        </div>
        <div class="input-field col s6">
            <input id="delivery" type="text" name="delivery" required>
            <label for="delivery">Delivery Mode</label>
        </div>
        <div class="input-field col s6">
            <input id="terms" type="text" name="terms" required>
            <label for="terms">Shipping Terms</label>
        </div>
        <div class="input-field col s6">
            <input id="tax" type="text" name="tax" required>
            <label for="tax">Sales Tax</label>
        </div>
        <?php
    }

}
<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 1/27/16
 * Time: 12:05 AM
 */
class Items extends CI_Controller
{
    protected $user_id;

    function __construct()
    {
        parent::__construct();
        $this->users_model->security();
        $this->user_id = $this->users_model->user()->user_id;
    }

    function register()
    {
        $this->form_validation->set_rules('model', 'Item model', 'trim|required');
        $this->form_validation->set_rules('state', 'Current item status', 'trim|required');
        $this->form_validation->set_rules('serial_no', 'Serial No.', 'trim|required|callback_check_serial');
        $this->form_validation->set_rules('code', 'Unique code', 'trim|required|callback_check_code');
        if ($this->form_validation->run() == FALSE) {
            $this->index();
        } else {
            $this->db->trans_start();
            $item_id = $this->items_model->register(
                $this->input->post('model'),
                $this->input->post('serial_no'),
                $this->input->post('code'),
                $this->input->post('state')
            );
            $this->db->trans_complete();
            if ($this->db->trans_status()) {
                $this->session->set_flashdata('success', 'Item registered successfully');
                $this->log_model->log('Registered item ' . $this->input->post('code') . ' into the system');
            }
            redirect('items', 'refresh');
        }
    }

    function index()
    {
        $var['page'] = 'Items Library';
        $this->load->template('items_view', $var);
    }

    function update()
    {
        $this->form_validation->set_rules('serial_no', 'Serial No.', 'trim|required|callback_check_serial');
        $this->form_validation->set_rules('code', 'Unique code', 'trim|required|callback_check_code');
        if ($this->form_validation->run() == FALSE) {
            $this->profile($this->input->post('item_id'));
        } else {
            $this->items_model->update($this->input->post('item_id'), $this->input->post('model'),
                $this->input->post('serial_no'), $this->input->post('code'));
            redirect('items/profile/' . $this->input->post('item_id'), 'refresh');
        }
    }

    function profile($item_id = false)
    {
        $item = $this->crud_model->get_record('items', 'item_id', base64_decode(urldecode($item_id)));
        if (!$item || !$item_id) {
            redirect('items', 'refresh');
            return;
        }
        $var['page'] = 'Item profile';
        $var['details'] = $item;
        $this->load->template('items_profile', $var);
    }

    function check_serial($serial_no)
    {
        if ($item = $this->crud_model->get_record('items', 'item_serial_no', $serial_no)) {
            if (null != $this->input->post('item_id') && $item->item_id == $this->input->post('item_id')) {
                return true;
            }
            $this->form_validation->set_message('check_serial', 'Serial No. given is already seems to exists');
            return false;
        }
        return true;
    }

    function check_code($code)
    {
        if ($item = $this->crud_model->get_record('items', 'item_code', $code)) {
            if (null != $this->input->post('item_id') && $item->item_id == $this->input->post('item_id')) {
                return true;
            }
            $this->form_validation->set_message('check_code', 'Code given is already assigned to another device');
            return false;
        }
        return true;
    }

    function category($model_id)
    {
        $model_id = base64_decode(urldecode($model_id));
        $details = $this->crud_model->get_record('item_models', 'item_model_id', $model_id);
        if (!$details) {
            redirect('items/define', 'refresh');
            return;
        }
        $var['details'] = $this->items_model->get_model_details($model_id);;
        $var['page'] = 'Model Profile';
        $this->load->template('model_profile', $var);
    }

    function cat_update()
    {
        $this->form_validation->set_rules('type', 'Item category', 'trim|required');
        $this->form_validation->set_rules('make', 'Item make', 'trim|required');
        $this->form_validation->set_rules('model', 'Item model', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->define();
        } else {
            $data = array(
                'model_name' => $this->input->post('model'),
                'model_description' => $this->input->post('description'),
                'model_est_cost' => $this->input->post('model_est_cost'),
                'make_id' => $this->check_make($this->input->post('make'))
            );
            $this->crud_model->update_record('item_models', 'item_model_id', $this->input->post('model_id'), $data);
            $this->session->set_flashdata('success', 'Model details updated successfully');
            $this->log_model->log('Updated model ' . $this->input->post('model') . ' of make ' . $this->input->post('make') . ' category ' . $this->input->post('type'));
            redirect('items/category/' . urlencode(base64_encode($this->input->post('model_id'))), 'refresh');
        }
    }

    function define($define = false)
    {
        if (!$define) {
            $var['page'] = 'Component specifications';
            $this->load->template('define_items_view', $var);
        } else {
            $this->form_validation->set_rules('type', 'Item category', 'trim|required');
            $this->form_validation->set_rules('make', 'Item make', 'trim|required');
            $this->form_validation->set_rules('model', 'Item model', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                $this->define();
            } else {
                $this->check_model($this->input->post('model'));
                redirect('items/define', 'refresh');
            }
        }
    }

    function check_model($model)
    {
        $item_model = $this->crud_model->get_record('item_models', 'model_name', $model);
        if (!$item_model || $item_model->make_id != $this->check_make($this->input->post('make'))) {
            $item_model = $this->crud_model->add_record('item_models', array(
                'model_name' => $model,
                'make_id' => $this->check_make($this->input->post('make')),
                'model_description' => $this->input->post('description'),
                'model_est_cost' => $this->input->post('model_est_cost'),
                'model_added_by' => $this->user_id
            ));
            $this->log_model->log('Registered model ' . $model . ' of make ' . $this->input->post('make') . ' category ' . $this->input->post('type'));
            $this->session->set_flashdata('success', $this->session->userdata('success') . 'Model <b>' . $model . '</b> was created');
            return $item_model;
        }
        return $item_model->item_model_id;
    }

    function check_make($make)
    {
        $item_make = $this->crud_model->get_record('item_make', 'make_name', $make);
        if (!$item_make || $item_make->it_id != $this->check_type($this->input->post('type'))) {
            $make_id = $this->crud_model->add_record('item_make', array(
                'make_name' => $make,
                'it_id' => $this->check_type($this->input->post('type')),
                'make_added_by' => $this->user_id
            ));
            $this->log_model->log('Registered  make <b>' . $make . '</b> of category <b>' . $this->input->post('type') . '</b>');
            $this->session->userdata('success', $this->session->userdata('success') . 'Make <b>' . $make . '</b> was created<br/>');
            return $make_id;
        }
        return $item_make->make_id;
    }

    function check_type($item_type)
    {
        $type = $this->crud_model->get_record('item_types', 'it_name', $item_type);
        if (!$type) {
            $type = $this->crud_model->add_record('item_types',
                array('it_name' => $this->input->post('type'),
                    'it_added_by' => $this->user_id
                ));
            $this->log_model->log('Registered item category ' . $item_type);
            $this->session->userdata('success', 'Item category <b>' . $item_type . '</b> was created');
            return $type;
        }
        return $type->it_id;
    }

    function request($details)
    {
        $data = json_decode(urldecode($details));
        $former_request = $this->equipment_model->item_requests($data->model_id, $data->equipment_id);
        if ($former_request && $former_request->request_status == 1 && $former_request->request_level == $data->request_level) {
            $this->crud_model->update_record('item_requests', 'item_request_id', $former_request->item_reqest_id, array(
                "request_qty" => $data->request_qty
            ));
            echo 'Items request already done. The qty was updated instead';
        } else {
            $this->crud_model->add_record("equipment_item_request",
                array(
                    "equipment_id" => $data->equipment_id,
                    "item_request_id" => $this->crud_model->add_record("item_requests",
                        array(
                            "model_id" => $data->model_id,
                            "request_qty" => $data->request_qty,
                            "purpose" => "Assembly requirement of equipment No. " . $this->equipment_model->details($data->equipment_id)->equipment_no,
                            "request_level" => $data->request_level
                        ))
                ));
            echo 'success';
        }
    }

    function trash($item_id, $restore = false)
    {
        $this->db->trans_start();
        $this->crud_model->update_record("items", "item_id", base64_decode(urldecode($item_id)), array("deleted" => $restore));
        $this->items_model->log(base64_decode(urldecode($item_id)), !$restore ? "Recovered from trash" : "Trashed");
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            if (!$restore) {
                $this->log_model->log('Recovered item No. ' . base64_decode(urldecode($item_id)));
                echo "Asset recovery Successfully";
            } else {
                $this->log_model->log('Trashed item No. ' . base64_decode(urldecode($item_id)));
                echo "Asset trashed Successfully";
            }
        } else {
            echo 'Failed';
        }
    }

    function receive($request, $store_request = false)
    {
        $request = json_decode(base64_decode(urldecode($request)));
        $this->db->trans_start();
        $item = $this->items_model->details($request->item_id);
        $this->crud_model->update_record(
            "item_request_assignemnt",
            "item_request_assignemnt_id",
            $request->item_request_assignment_id,
            array(
                "assignment_confirmation" => true
            ));
        $this->crud_model->update_record(
            "items",
            "item_id",
            $request->item_id,
            array(
                "location_type" => $request->equipment_id,
                "location_type_id" => $request->equipment_id
            ));

        //change store item details
        if ($store_request) {
            $this->crud_model->add_record(
                "store_items", array(
                "store_id" => $request->equipment_id,
                "item_id" => $request->item_id,
                "received_by" => $this->users_model->user()->user_id,
                "si_available" => true
            ));
        }
        $this->items_model->log(
            $request->item_id,
            $store_request ? "Transferred to store" . $this->stores_model->specific($request->equipment_id)
                    ->store_name : "Transferred to  equipment No. " . $this->equipment_model->details($request->equipment_id)->equipment_no);
        if ($store_request)
            $this->stores_model->log($request->equipment_id, "Item No. " . $item->item_code . " received into stock");
        else
            $this->equipment_model->log($request->equipment_id, "Item No. " . $item->item_code . " received");
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            //$this->log_model->log('Received item No. ' . $item->item_code . ' for equipment No. '
            //     . $this->equipment_model->details($request->equipment_id)->equipmnent_no);
            echo "Item assignment  confirmation successful";
        } else {
            echo 'Item assignment  confirmation failed';
        }
    }
}
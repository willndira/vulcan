<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 1/29/16
 * Time: 1:09 PM
 */
class Items_model extends CI_Model
{
    protected $logged;

    function __construct()
    {
        parent::__construct();
        $this->logged = $this->users_model->user();
    }

    function register($model_id, $serial_no, $code, $condition)
    {
        $data = array(
            'model_id' => $model_id,
            'item_serial_no' => $serial_no,
            'item_added_by' => $this->logged->user_id,
            'item_condition' => $condition,
            'item_code' => $code
        );
        $this->db->trans_start();
        $item_id = $this->crud_model->add_record('items', $data);
        $asset = array(
            "asset_id" => $item_id,
            "asset_type" => 1,
            "location_id" => 1,
            "location_type" => 1,
            "comment" => "New item registration into store"
        );
        $this->asset_model->stock($asset);
        $this->db->trans_complete();
        $this->items_model->log($item_id, "Registered into the system");
        return $item_id;
    }

    function update($item_id, $model_id, $serial_no, $item_code)
    {
        $data = array(
            'model_id' => $model_id,
            'item_serial_no' => $serial_no,
            'item_code' => $item_code
        );
        $this->crud_model->update_record('items', 'item_id', $item_id, $data);
        $this->log($item_id, "Details changed");
        $this->log_model->log('Updated item #' . $this->input->post('item_id'));
        $this->session->set_flashdata('success', 'Item details updated successfully');
    }

    function log($item_id, $action)
    {
        $this->crud_model->add_record('item_timeline', array(
            'item_id' => $item_id,
            'activity_by' => $this->logged->user_id,
            'item_activity' => $action
        ));
    }

    function get_model_details($model_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_item_types a, tbl_item_make b,tbl_item_models c');
        $this->db->where('c.item_model_id', $model_id);
        $this->db->where('c.make_id =  b.make_id');
        $this->db->where('b.it_id = a.it_id');
        return $this->db->get()->row();
    }

    function current_location($item_id)
    {
        $item = $this->details($item_id);
        if ($item->location_type == 1) {
            return $this->stores_model->specific($item->location_type_id)->store_name;
        } else {
            if (count($equipment = $this->crud_model->get_record("equipment", "equipment_id", $item->location_type_id)) > 0) {
                return "Installed (" . $equipment->equipment_no . ")";
            }
        }
        return "Unknown";
    }

    function details($item_id)
    {
        $item = $this->crud_model->get_record('items', 'item_id', $item_id);
        return $item;
    }

    function fail_rate($item_id)
    {
        $success = count($this->db->get_where('tbl_item_timeline', array('item_id' => $item_id, 'item_state' => 1))->result());
        $fail = count($this->db->get_where('tbl_item_timeline', array('item_id' => $item_id, 'item_state' => 0))->result());
        return (object)array('rate' => ($fail / ($success + $fail)) * 100, 'success' => $success, 'fail' => $fail);
    }

    function in_store($model_id)
    {
        $this->db->select('b.*');
        $this->db->from('tbl_items a');
        $this->db->join('tbl_store_items b', 'a.item_id = b.item_id');
        $this->db->where('a.model_id', $model_id);
        $this->db->where('b.si_available', true);
        return $this->db->get()->result();
    }

    function is_available($model_id, $store_type)
    {
        $this->db->select("a.si_available");
        $this->db->from("tbl_store_items a");
        $this->db->join("tbl_items b", "b.item_id = a.item_id", " left outer");
        $this->db->join("tbl_stores c", "c.store_id = a.store_id", " left outer");
        $this->db->where("b.model_id", $model_id);
        $this->db->where("c.store_type", $store_type);

        return $this->db->get()->row();
    }

    function requests($is_assigned, $level = false, $state = false)
    {
        $this->db->select("a.*");
        $this->db->from("tbl_item_requests a");
        $this->db->join("tbl_item_request_assignemnt b", "a.item_request_id = b.item_request_id", "LEFT OUTER");
        if ($level)
            $this->db->where("a.request_level", $level);
        if ($is_assigned)
            $this->db->where("b.assignment_confirmation", $state);
        else
            $this->db->where("b.item_request_assignemnt_id IS NULL");

        return $this->db->get()->result();
    }

}
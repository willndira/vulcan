<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 2/11/16
 * Time: 10:35 PM
 */
class Stores_model extends CI_Model
{
    protected $logged;

    function __construct()
    {
        parent::__construct();
        $this->logged = $this->users_model->user();
    }

    function create($data)
    {
        $store = $this->crud_model->add_record('stores', $data);
        if ($store)
            $this->log_model->log("Created a new store " . $data['store_name']);
        return $store;
    }

    function my_store()
    {
        return $this->db->get_where('tbl_store_manager', array('user_id' => $this->users_model->user()->user_id, 'status' => true))->row();
    }

    function assign_manager($store_id, $user_id)
    {
        $query = $this->db->insert('tbl_store_manager', array(
            'store_id' => $store_id,
            'user_id' => $user_id,
            'assigned_by' => $this->logged->user_id,
            'status' => true
        ));
        if ($query) {
            $this->log_model->log(" Assigned -" . $this->users_model->user($user_id)->user_name
                . " as " . $this->specific($store_id)->store_name . " manager");
        }
    }

    function specific($store_id)
    {
        return $this->crud_model->get_record('stores', 'store_id', $store_id);
    }


    function log($store_id, $action)
    {
        $this->crud_model->add_record('store_logs', array(
            "store_id" => $store_id,
            "user_id" => $this->logged->user_id,
            "activity" => $action
        ));
    }


    function unassign_manager($store_id, $user_id)
    {
        $this->db->where(array('store_id' => $store_id, 'user_id' => $user_id, 'status' => true));
        $query = $this->db->update('tbl_store_manager', array('status' => false));
        if ($query) {
            $this->log_model->log(" Removed -" . $this->users_model->user($user_id)->user_name
                . " as " . $this->specific($store_id)->store_name . " manager");
        }
        return $query;
    }

    function asset_stock($store_id, $asset_type)
    {
        return $this->db->select("*")
            ->from("tbl_asset_location_trail")
            ->where("location_type", $asset_type)
            ->where("location_id", $store_id)
            ->where("status", true)
            ->get()
            ->result();
    }

    function similar_stock($model_id, $store_id, $asset_type)
    {
        $this->db->select("*");
        $this->db->from("tbl_asset_location_trail a");
        if ($asset_type == 1) {
            $this->db->join("tbl_items b", "a.asset_id = b.item_id");
            $this->db->where("b.model_id", $model_id);
        } else {
            $this->db->join("tbl_equipment b", "a.asset_id = b.equipment_id");
            $this->db->where("b.component_id", $model_id);
        }
        $this->db->where("a.location_id", $store_id)
            ->where("a.location_type", 1)
            ->where("a.status", true);
        return $this->db->get()->result();
    }

    function manager($store_id)
    {
        return $this->db->get_where('tbl_store_manager', array('store_id' => $store_id, 'status' => true))->row();
    }

}
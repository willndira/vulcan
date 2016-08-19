<?php

/**
 * Created by PhpStorm.
 * User: mayne
 * Date: 4/9/16
 * Time: 6:25 PM
 */
class Asset_model extends CI_Model
{
    protected $logged;

    function __construct()
    {
        parent::__construct();
        $this->logged = $this->users_model->user();
    }

    function stock($asset)
    {
        $this->crud_model->add_record("asset_location_trail", array(
            "asset_id" => $asset['asset_id'],
            "asset_type" => $asset['asset_type'],
            "handling_staff" => $this->logged->user_id,
            "location_id" => $asset['location_id'],
            "location_type" => $asset['location_type'],
            "deleted" => false,
            "confirmation" => 1,
            "status" => 1,
            "trail_comment" => $asset['comment']
        ));
    }

    function model_available($asset_type, $model_id, $location_id, $location_type)
    {
        $this->db->select("*")
            ->from("tbl_asset_location_trail a");
        if ($asset_type == 1) {
            $this->db->join("tbl_items b", "a.asset_id = b.item_id")
                ->where("b.model_id", $model_id);
        } else {
            $this->db->join("tbl_equipment b", "a.asset_id = b.equipment_id")
                ->where("b.component_id", $model_id);
        }
        $this->db->where("a.status", true)
            ->where("a.location_id", $location_id)
            ->where("a.asset_type", $asset_type)
            ->where("a.location_type", $location_type)
            ->where("a.deleted", false)
            ->where("b.deleted", false);
        return $this->db->get()
            ->result();
    }

    /*
     * Assets assigned to a store, project or equipment ... filtered with the confirmation
     */
    function assigned($location_id, $location_type, $asset_type, $is_confirmed = false)
    {
        return $this->db->get_where("tbl_asset_location_trail", array(
            "location_id" => $location_id,
            "location_type" => $location_type,
            "asset_type" => $asset_type,
            "deleted" => false,
            "confirmation" => $is_confirmed,
            "status" => true
        ))->result();
    }

    /*
     * Assets assigned to a store, project or equipment
     */
    function all_assigned($location_id, $location_type, $asset_type)
    {
        return $this->db->get_where("tbl_asset_location_trail", array(
            "location_id" => $location_id,
            "location_type" => $location_type,
            "asset_type" => $asset_type,
            "deleted" => false,
            "status" => true
        ))->result();
    }

    /*
     * Assets requested to a store, project or equipment on a specific model
     */
    function requested($model_id, $asset_type, $category, $category_id)
    {
        return $this->db->get_where("tbl_asset_request",
            array(
                "model_id" => $model_id,
                "request_category" => $category,
                "request_category_id" => $category_id,
                "deleted" => false,
                "request_asset_type" => $asset_type
            ))->row();
    }

    /*
     * Assets requested to a store, project or equipment
     */
    function store_requested($asset_type, $store_type, $status = 1, $deleted = false)
    {
        return $this->db->get_where("tbl_asset_request",
            array(
                "request_level" => $store_type,
                "deleted" => $deleted,
                "request_status" => $status,
                "request_asset_type" => $asset_type
            ))->result();
    }

    /*
     * Request made on  an equipment or project
     */
    function my_requests($category, $id, $asset_type)
    {
        return $this->db->get_where("tbl_asset_request",
            array(
                "request_category" => $category,
                "request_category_id" => $id,
                "deleted" => false,
                "request_asset_type" => $asset_type
            ))->result();
    }

    function request_assignment($request_id){

    }
}
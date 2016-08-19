<?php

/**
 * Created by PhpStorm.
 * User: mayne
 * Date: 4/10/16
 * Time: 12:06 PM
 */
class Assets extends CI_Controller
{
    private $admin_id;

    function __construct()
    {
        parent::__construct();
        $this->admin_id = $this->users_model->user()->user_id;
    }

    function index()
    {
        echo "requesting for who??? ";
    }

    function request($details)
    {
        $details = json_decode(base64_decode(urldecode($details)));
        $this->db->trans_start();
        $this->crud_model->add_record("asset_request",
            array(
                "model_id" => $details->model_id,
                "request_qty" => $details->request_qty,
                "request_asset_type" => $details->request_asset_type,
                "request_category" => $details->request_category,
                "request_category_id" => $details->request_category_id,
                "purpose" => $details->purpose,
                "request_level" => $details->request_level,
                "requesting_user" => $this->admin_id
            ));
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            echo "Request posted successfully";
        } else
            echo "unable to post your request. Try again please";
    }

    function collect($asset_location_id)
    {
        $this->db->trans_start();
        // $asset = $this->crud_model->get_record("asset_location_trail", "asset_location_id", $asset_location_id);
        $this->crud_model->update_record("asset_location_trail", "asset_location_id",
            base64_decode(urldecode($asset_location_id)), array("confirmation" => true));
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            echo "Asset added to your stock successfully";
        } else
            echo "unable to add asset to your stock";
    }

    function assign($details)
    {
        $details = json_decode(base64_decode(urldecode($details)));
        $this->db->trans_start();
        $asset = $this->db->get_where("tbl_asset_location_trail", array("status" => 1, "asset_id" => $details->asset_id))->row();
        $request = $this->db->get_where("tbl_asset_request", array("request_id" => $details->request_id))->row();
        $this->crud_model->update_record("asset_location_trail", "asset_location_id", $asset->asset_location_id, array("status" => false));
        $this->crud_model->add_record("asset_location_trail",
            array(
                "asset_id" => $details->asset_id,
                "asset_type" => $asset->asset_type,
                "handling_staff" => $this->admin_id,
                "location_id" => $request->request_category_id,
                "location_type" => $request->request_category,
                "trail_comment" => "Assignment for: " . $request->purpose,
                "status" => true,
                "confirmation" => false
            ));
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            echo "Asset assigned successfully";
        } else
            echo "unable to assign asset";
    }

    function cancel($details)
    {
        $details = json_decode(base64_decode(urldecode($details)));
        $this->db->trans_start();
        $asset = $this->db->order_by("asset_location_id", "desc")
            ->get_where("tbl_asset_location_trail", array("status" => false, "asset_id" => $details->asset_id))->row();
        $this->crud_model->update_record("asset_location_trail", "asset_location_id", $asset->asset_location_id, array("status" => true));
        $this->crud_model->delete_record("asset_location_trail", "asset_location_id", $details->asset_location_id);
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            echo "Asset assignment canceled";
        } else
            echo "unable to cancel asset assignment";

    }

    function requests($level)
    {
        $level = base64_decode(urldecode($level));
        $var['store_type'] = $level;
        $var['man'] = $this->stores_model->manager($var['store_type']);
        $var['page'] = $level == 2 ? 'Assembly asset requests' : ($level == 3 ? 'Installation asset requests' : ($level == 4 ? 'Maintenance asset requests' : ''));
        $this->load->template('assets_requests_interface', $var);
    }
}
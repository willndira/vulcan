<?php

/**
 * Created by PhpStorm.
 * User: mayne
 * Date: 4/10/16
 * Time: 12:22 AM
 */
class Delete extends CI_Controller
{
    protected $user_id;

    function __construct()
    {
        parent::__construct();
        $this->users_model->security();
        $this->user_id = $this->users_model->user()->user_id;
    }

    function index()
    {
        redirect("dashboard", "refresh");
    }

    function move()
    {

    }

    function delete($tbl, $id)
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
}
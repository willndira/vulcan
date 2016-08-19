<?php

/**
 * Created by PhpStorm.
 * User: mayne
 * Date: 4/20/16
 * Time: 11:25 AM
 */
class Assembly_model extends CI_Model
{
    protected $logged;
    protected $admin;

    function __construct()
    {
        parent::__construct();
        $this->logged = $this->users_model->user();
        if (null != $this->logged)
            $this->admin = $this->logged->user_id;
    }

    function processing($stage)
    {
        return $this->db->get_where("tbl_equipment_assembly", array(
            "deleted" => false,
            "stage" => $stage))
            ->result();
    }

    function assignment($stage, $user_id = false)
    {
        return $this->db->get_where("tbl_equipment_assembly", array(
            "assembly_manager" => ($user_id ? $user_id : $this->admin),
            "deleted" => false,
            "stage" => $stage))
            ->result();
    }

    function all()
    {
        return $this->crud_model->get_records("equipment_assembly");
    }

    function is_tasked($guide_id, $equipment_assembly_id)
    {
        return count($this->db->select("a.*")
            ->from("tbl_assembly_schedule_steps a")
            ->join("tbl_assembly_schedule b", "a.schedule_id = b.assembly_schedule_id")
            ->where("b.equipment_assembly_id", $equipment_assembly_id)
            ->where("a.setup_guide_id", $guide_id)
            ->get()
            ->result()) > 0;
    }

    function equipment_steps($component_id, $level, $test = false)
    {
        return $this->db->get_where("tbl_setup_guide",
            array("component_id" => $component_id,
                "is_test" => $test, //TESTING OR NOT
                "step_category" => $level //ASSEMBLY VS INSTALLATION
            ))->result();
    }

    function procedure_progress($ag_id, $assembly_id)
    {
        return $this->db->select("a.*")
            ->from("tbl_assembly_schedule_steps a")
            ->join("tbl_assembly_schedule b", "b.assembly_schedule_id = a.schedule_id")
            ->where("b.equipment_assembly_id", $assembly_id)
            ->where("a.setup_guide_id", $ag_id)
            ->get()
            ->row();
    }

    function get_sites()
    {

    }

    function get_tickets()
    {

    }
}
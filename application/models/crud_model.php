<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 1/19/16
 * Time: 9:19 PM
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Crud_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function add_record($table, $data)
    {
        $this->db->insert('tbl_' . $table, $data);
        return $this->db->insert_id();
    }

    function get_records($table, $key_column = false, $state = false, $limit = false)
    {
        $this->db->where('deleted', false);
        if ($limit)
            $this->db->limit(5);
        if ($key_column) {
            $query = $this->db->get_where('tbl_' . $table, array($key_column => $state));
        } else {
            $query = $this->db->get('tbl_' . $table);
        }
        return $query->result();
    }

    function get_trash($table)
    {
        return $this->db->where('deleted', true)
            ->get('tbl_' . $table)
            ->result();
    }

    function get_record($table, $primary_column, $value)
    {
        $query = $this->db->get_where('tbl_' . $table, array($primary_column => $value));
        return $query->row();
    }

    function update_record($table, $primary_column, $value, $data)
    {
        $this->db->where($primary_column, $value);
        return $this->db->update('tbl_' . $table, $data);
    }

    function flag_delete_record($table, $primary_column, $value, $state = true)
    {
        /*
         * SELECT
  TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
FROM

WHERE
  REFERENCED_TABLE_SCHEMA = '<database>' AND
  REFERENCED_COLUMN_NAME = '<column>';
         */

     //   $this->db->get_where("INFORMATION_SCHEMA.KEY_COLUMN_USAGE")
        $this->db->where($primary_column, $value);
        return $this->db->update('tbl_' . $table, array('deleted' => $state));
    }

    function delete_record($table, $primary_column, $value)
    {
        try {
            $this->db->where($primary_column, $value);
            return $this->db->delete('tbl_' . $table);
        } catch (Exception $e) {
            return false;
        }
    }


    //New style
    function read($table, $where)
    {
        return $this->db->get_where("tbl_" . $table, $where)
            ->result();
    }

    function read_one($table, $where)
    {
        return $this->db->get_where("tbl_" . $table, $where)
            ->row();
    }

    function update($table, $filter, $data)
    {
        return $this->db->where($filter)
            ->update('tbl_' . $table, $data);
    }

    function delete($table, $filter)
    {
        return $this->db->where($filter)
            ->update('tbl_' . $table, array("deleted" => true));
    }
}
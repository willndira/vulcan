<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class aps_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function insert_aps($data) {
        $this->db->insert('tbl_aps', $data);
        return $this->db->insert_id();
    }

    public function get_aps($site_id) {
        $this->db->from('tbl_aps');
        $this->db->where('site_id', $site_id);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_all_aps() {
        $this->db->from('tbl_aps');
        $this->db->join('tbl_sites', 'tbl_aps.site_id = tbl_sites.site_id');
        $query = $this->db->get();
        return $query->result();
    }

}

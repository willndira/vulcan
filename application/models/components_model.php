<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 2/15/16
 * Time: 11:43 PM
 */
class Components_model extends CI_Model
{

    function total_items($component_id)
    {
        $this->db->select_sum('model_qty');
        return 0 + $this->db->get_where('tbl_component_items', array('component_id' => $component_id, 'deleted' => false))->row()->model_qty;
    }

    function assets($component_id, $type = false)
    {
        return $this->db->get_where('tbl_component_items',
            array('component_id' => $component_id,
                'component_type' => !$type))
            ->result();
    }

    function details($component_id)
    {
        return $this->db->get_where('tbl_components', array('component_id' => $component_id))->row();
    }
}
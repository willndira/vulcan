<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 2/15/16
 * Time: 7:11 PM
 */
class Components extends CI_Controller
{
    protected $admin_id;

    function __construct()
    {
        parent::__construct();
        $this->users_model->security();
        $this->admin_id = $this->users_model->user()->user_id;
    }

    function index()
    {
        $var['page'] = 'Equipment specification';
        $this->load->template('components_view', $var);
    }

    function define()
    {
        $component_name = $this->input->post('component_name');
        $component_desc = $this->input->post('desc');
        if (!$this->crud_model->get_record('components', 'component_name', $component_name)) {
            $component_id = $this->crud_model->add_record('components',
                array(
                    'component_name' => $component_name,
                    'component_description' => $component_desc,
                    'component_added_by' => $this->admin_id
                ));
            $this->log_model->log('Registered equipment ' . $component_name . ' into the system');
            $this->session->set_flashdata('success', 'Equipment registered successfully');
            redirect('components/profile/' . urlencode(base64_encode($component_id)));
            return;
        } else {
            $this->session->set_flashdata('error', 'Equipment exists. You might consider editting it');
        }
        redirect('components');
    }

    function update()
    {
        $component_name = $this->input->post('component_name');
        $component_id = $this->input->post('component_id');
        $component_desc = $this->input->post('desc');
        if ($this->crud_model->update_record('components', 'component_id', $component_id,
            array('component_name' => $component_name, 'component_description' => $component_desc))
        ) {

            $this->log_model->log('Edited equipment  ' . $component_name . ' details');
            $this->session->set_flashdata('success', 'Equipment edited registered successfully');
        } else {
            $this->session->set_flashdata('error', 'Equipment update failed.');
        }
        redirect('components/profile/' . urlencode(base64_encode($component_id)));
    }

    function add_item()
    {
        $this->form_validation->set_rules('model_id', 'Asset model', 'trim|required');
        $this->form_validation->set_rules('model_qty', 'Number of assets  ', 'required');
        $this->form_validation->set_rules('comp_type', 'Component type ', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->profile(urlencode(base64_encode($this->input->post("component_id"))));
            return;
        }
       echo $model_id = $this->input->post('model_id');
        $model_qty = $this->input->post('model_qty');
        $comp_type = $this->input->post('comp_type');
        $desc = $this->input->post('description');
        $component_id = $this->input->post('component_id');
        if (!$this->db->get_where('tbl_component_items', array('component_id' => $component_id, 'model_id' => $model_id))->row()) {
            $this->crud_model->add_record('component_items',
                array(
                    'component_id' => $component_id,
                    'component_description' => $desc,
                    'component_type' => $comp_type,
                    'model_id' => $model_id,
                    'model_qty' => $model_qty,
                    'ci_added_by' => $this->admin_id
                ));
            $this->log_model->log('Added components to equipment type ' . $this->crud_model->get_record('components', 'component_id', $component_id)->component_name);
            $this->session->set_flashdata('success', 'Component added to equipment successfully');
        } else {
            $this->session->set_flashdata('error', 'It seems the components are already in added in the equipment. Consider editing quantity');
        }
        redirect('components/profile/' . urlencode(base64_encode($component_id)));
    }

    function profile($component_id)
    {
        $var['page'] = 'Equipment Profile';
        $var['details'] = $this->crud_model->get_record('components', 'component_id', base64_decode(urldecode($component_id)));
        $this->load->template('components_profile', $var);
    }

    function create_instruction()
    {
        $desc = $this->input->post('description');
        $component_id = $this->input->post('component_id');
        if (!$this->db->get_where('tbl_setup_guide', array('component_id' => $component_id, 'step_description' => $desc))->row()) {
            $this->db->trans_start();
            $setup_id = $this->crud_model->add_record('setup_guide',
                array(
                    'component_id' => $component_id,
                    'step_description' => $desc,
                    'step_category' => $this->input->post('step_category'),
                    'is_test' => $this->input->post('is_test'),
                    'result_type' => $this->input->post('result_type'),
                    'guide_added_by' => $this->admin_id
                ));
            if (null != $this->input->post('assets')) {
                foreach ($this->input->post('assets') as $asset) {
                    $this->crud_model->add_record('setup_items',
                        array(
                            'step_id' => $setup_id,
                            'model_id' => $asset
                        ));
                }
            }
            $this->db->trans_complete();
            if ($this->db->trans_status()) {
                $this->log_model->log('Added assembly guide for ' . $this->crud_model->get_record('components', 'component_id', $component_id)->component_name);
                $this->session->set_flashdata('success', 'Equipment instruction guide added successfully');
            }
        } else {
            $this->session->set_flashdata('error', 'It seems instruction already exists.');
        }
        redirect('components/profile/' . urlencode(base64_encode($component_id)));
    }


    function trash($level_id, $is_equipment, $is_restore = false)
    {
        $level_id = base64_decode(urldecode($level_id));
        $this->db->trans_start();
        if (!$is_equipment)
            $this->crud_model->update_record("components", "component_id", $level_id, array("deleted" => $is_restore));
        if ($is_equipment)
            $this->crud_model->update_record("equipment", "equipment_id", $level_id, array("deleted" => $is_restore));
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            if (!$is_restore) {
                if (!$is_equipment)
                    $this->log_model->log('Recovered equipment category: <b>' . $this->crud_model->get_record("components", "component_id", $level_id)->component_name . " </b>from trash");
                if ($is_equipment)
                    $this->log_model->log('Recovered equipment No.: <b>' . $this->crud_model->get_record("equipment", "equipment_id", $level_id)->equipment_no . " </b>from trash");
                echo "Recovery Successfully";
            } else {
                if (!$is_equipment)
                    $this->log_model->log('Trashed user equipment category: <b>' . $this->crud_model->get_record("components", "component_id", $level_id)->component_name . " </b>");
                if ($is_equipment)
                    $this->log_model->log('Trashed equipment No.: <b>' . $this->crud_model->get_record("equipment", "equipment_id", $level_id)->equipment_no . " </b>");
                echo "Deletion Successfully";
            }
        } else {
            echo 'Deletion failed';
        }
    }
}
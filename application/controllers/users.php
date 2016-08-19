<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 1/20/16
 * Time: 7:42 AM
 */
class Users extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->users_model->security();
    }

    function register($posted = false)
    {
        if (!$posted) {
            $var['page'] = 'Register New User';
            $this->load->template('register_user_view', $var);
        } else {
            $this->process_details();
        }
    }

    private function process_details()
    {
        $this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'required|callback_check_email');
        $this->form_validation->set_rules('phone', 'Phone', 'required|callback_check_phone');
        $this->form_validation->set_rules('about', 'About', 'required');
        $this->form_validation->set_rules('category', 'User level', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->index();
        } else {
            $data = array(
                'user_name' => $this->input->post('first_name') . ' ' . $this->input->post('last_name'),
                'user_email' => $this->input->post('email'),
                'user_phone' => $this->input->post('phone'),
                'user_category_id' => $this->input->post('category'),
                'user_about' => $this->input->post('about'),
                'user_added_by' => $this->session->userdata('logged_admin'),
                'user_status' => true,
                'user_access' => true
            );
            $user_id = $this->users_model->register_user($data);
            $this->session->userdata('success', 'User registered successfully');
            redirect('profile/user/' . urlencode(base64_encode($user_id)), 'refresh');
        }
    }

    function index()
    {
        $var['page'] = 'Registered Users';
        $this->load->template('users_view', $var);
    }

    function check_email($serial_no)
    {
        if ($item = $this->crud_model->get_record('users', 'user_email', $serial_no)) {
            $this->form_validation->set_message('check_email', 'Email already in our records');
            return false;
        }
        return true;
    }

    function check_phone($serial_no)
    {
        if ($item = $this->crud_model->get_record('users', 'user_phone', $serial_no)) {
            $this->form_validation->set_message('check_phone', 'Phone No. given is already seems to exists');
            return false;
        }
        return true;
    }

    function remove_perm($user_role_id)
    {
        $perm = base64_decode(urldecode($user_role_id));
        $cat = $this->crud_model->get_record('user_roles', 'user_role_id', $perm)->category_id;
        $this->users_model->remove_permission($perm);
        redirect('users/level_profile/' . urlencode(base64_encode($cat)));
    }

    function create_level()
    {
        $this->form_validation->set_rules('level_name', 'Level Name', 'trim|required');
        $this->form_validation->set_rules('permissions', 'Permission', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->level();
        } else {
            $level = $this->crud_model->add_record('user_category',
                array(
                    'user_category_name' => $this->input->post('level_name'),
                    'user_category_description' => $this->input->post('about'),
                    'user_category_created_by' => $this->users_model->user()->user_id
                ));
            foreach ($this->input->post('permissions') as $permission) {
                $this->crud_model->add_record('user_roles',
                    array('role_code' => $permission,
                        'category_id' => $level,
                        'ur_added_by' => $this->users_model->user()->user_id)
                );
            }
            $this->log_model->log('Created new system user category ' . $this->input->post('level_name'));
            redirect('users/level', 'refresh');
        }
    }

    function level($level = false, $action = false)
    {
        $var['category'] = false;
        if ($action) {
            if ($category = $this->users_model->get_category($category_id = base64_decode(urldecode($level)))) {
                if ($action != 'edit') {
                    if ($action == 'flag_del' || $action == 'undo_del') {
                        $this->crud_model->flag_delete_record('user_category', 'user_category_id', $category_id, $action == 'flag_del' ? true : false);
                    } elseif ($action == 'del') {
                        $this->crud_model->delete_record('user_category', 'user_category_id', $category_id);
                    }
                    redirect('users/level');
                } else
                    $var['category'] = $category;
            }
        }
        $var['page'] = 'User Categories';
        $this->load->template('user_level_view', $var);

    }

    function update_level($category)
    {
        $category_id = base64_decode(urldecode($category));
        $this->form_validation->set_rules('level_name', 'Level Name', 'trim|required');
        $this->form_validation->set_rules('about', 'Level description', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->level_profile($category);
        } else {
            $this->crud_model->update_record('user_category', 'user_category_id', $category_id,
                array(
                    'user_category_name' => $this->input->post('level_name'),
                    'user_category_description' => $this->input->post('about')
                ));
            if (null == $this->input->post('permissions'))
                $_POST['permissions'] = array();
            foreach ($this->input->post('permissions') as $permission)
                $this->crud_model->add_record('user_roles',
                    array(
                        'role_code' => $permission,
                        'category_id' => $category_id,
                        'ur_added_by' => $this->users_model->user()->user_id
                    ));
            $this->log_model->log('Updated details for system user category ' . $this->input->post('level_name'));
        }
        redirect('users/level_profile/' . $category, 'refresh');
    }

    function level_profile($level_id)
    {
        if (!$details = $this->crud_model->get_record('user_category', 'user_category_id', base64_decode(urldecode($level_id)))) {
            redirect('unkown', 'refresh');
            return;
        }
        $var['details'] = $details;
        $var['page'] = 'User Category Profile';
        $this->load->template('user_level_profile', $var);
    }

    function trash($level_id, $is_user, $is_restore = false)
    {
        $level_id = base64_decode(urldecode($level_id));
        $this->db->trans_start();
        if (!$is_user)
            $this->crud_model->update_record("user_category", "user_category_id", $level_id, array("deleted" => $is_restore));
        if ($is_user)
            $this->crud_model->update_record("users", "user_id", $level_id, array("deleted" => $is_restore));
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            if (!$is_restore) {
                if (!$is_user)
                    $this->log_model->log('Recovered user category: <b>' . $this->crud_model->get_record("user_category", "user_category_id", $level_id)->user_category_name . " </b>from trash");
                if ($is_user)
                    $this->log_model->log('Recovered user: <b>' . $this->users_model->user($level_id)->user_name . " </b>from trash");
                echo "Recovery Successfully";
            } else {
                if (!$is_user)
                    $this->log_model->log('Trashed user category: <b>' . $this->crud_model->get_record("user_category", "user_category_id", $level_id)->user_category_name . " </b>");
                if ($is_user)
                    $this->log_model->log('Trashed user: <b>' . $this->users_model->user($level_id)->user_name . " </b>");
                echo "Deletion Successfully";
            }
        } else {
            echo 'Deletion failed';
        }
    }
}
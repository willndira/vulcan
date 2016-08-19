<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 1/19/16
 * Time: 9:18 PM
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Users_model extends CI_Model
{
    private $logged_admin;

    function __construct()
    {
        parent::__construct();
        $this->logged_admin = $this->session->userdata('logged_admin');
    }

    function security($role = false)
    {
        if (null == $this->session->userdata('logged_admin') || is_null($this->user()->user_id)) {
            $this->session->userdata('error', 'Please log in to proceed.<br/>Thank you for understanding');
            redirect('login');
        }
        if ($role && !$this->requires_role(array($role))) {
            $this->session->userdata('error', 'Seems you the action overpowered you.<br/> Ask for the permission to proceed');
            redirect('dashboard', 'refresh');
        }

        /* TODO: process locking
          if (null != $this->session->userdata('locked'))
              redirect('locked');*/
    }

    function user($user_id = false)
    {
        !$user_id ? $user_id = $this->logged_admin : null;
        return $this->crud_model->get_record('users', 'user_id', $user_id);
    }

    function requires_role($required_roles)
    {
        $auth = false;
        $this->user()->user_category_id == 1 ? $auth = true : $auth = false;
        foreach ($required_roles as $required_role) {
            $query = $this->db->get_where('tbl_user_roles',
                array('role_code' => $required_role, 'category_id' => $this->user()->user_category_id));
            $query->row() ? $auth = true : false;
        }
        return $auth;
    }

    function login($email, $password)
    {
        $query = $this->db->get_where('tbl_users',
            array('user_email' => $email,
                'user_password' => md5($password))
        );
        return $query->row();
    }

    function get_category($cat_id)
    {
        return $this->crud_model->get_record('user_category', 'user_category_id', $cat_id);
    }

    function remove_permission($user_role_id)
    {
        return $this->db->delete('tbl_user_roles', array('user_role_id' => $user_role_id));
    }

    function role_selected($cat_id, $role_code)
    {
        $this->db->select('*');
        $this->db->from('tbl_user_roles');
        $this->db->where('category_id', $cat_id);
        $this->db->where('role_code', $role_code);
        return $this->db->get()->row();
    }

    function register_user($data)
    {
        $this->db->trans_start();
        $user_id = $this->crud_model->add_record('users', $data);
        $password = $this->generate_password($user_id);
        $this->communicate_model->send_new_password($user_id, $password, true);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->log_model->write_error('User registration failed.');
            return false;
        }
        $this->log_model->log('Registered new system user ' . $data['user_name']);
        return $user_id;
    }

    private function generate_password($user)
    {
        $password = substr(md5(date('Y.M-d H/m:s') . rand(0, 10202023)), 0, 8);
        $this->db->where(array('user_id' => $user));
        if ($this->db->update('tbl_users', array('user_password' => md5($password))))
            return $password;
        return false;
    }

    function reset_password($email)
    {
        $query = $this->db->get_where('tbl_users', array('user_email' => $email));
        if ($user = $query->row()) {
            if ($password = $this->generate_password($user->user_id)) {
                $this->communicate_model->send_new_password($user->user_id, $password);
                $this->log_model->log('Requested new account password ', $user);
                return $user;
            }
        }
        return false;
    }

    function update_user($data, $user_id)
    {
        $this->db->where(array('user_id' => $user_id));
        $query = $this->db->update('tbl_users', $data);
        if ($query) {
            $this->log_model->log('Edited profile details for ' . $data['user_name']);
            $this->notify(array($user_id), array('header' => 'Profile Update',
                'message' => 'Your profile details was modified by ' . $this->user()->user_name
            ));
        }
        return $query;
    }

    function notify($users, $data)
    {
        $this->db->trans_start();
        $notification_id = $this->crud_model->add_record('notifications',
            array('notification_header' => $data['header'], 'notification_message' => $data['message']));
        foreach ($users as $user) {
            $this->crud_model->add_record('notification_user', array('user_id' => $user, 'notification_id' => $notification_id));
            $this->communicate_model->communicate(
                array(
                    'subject' => $data['header'],
                    'message' => $data['message'],
                    'user_id' => $user
                ));
        }
        $this->db->trans_complete();
    }

    function user_pic($user_id)
    {
        $pic = read_file('./application/uploads/prof_pic/' . $user_id . '_prof_pic.jpg');
        return base_url() . 'application/uploads/prof_pic/' . ($pic ? $user_id . '_prof_pic.jpg' : 'default_prof_pic.jpg');
    }

    function upload_pic()
    {
        $config['upload_path'] = './application/uploads/prof_pic';
        $config['allowed_types'] = 'jpg|JPEG|jpeg|png';
        $config['overwrite'] = true;
        $config['file_name'] = $this->user()->user_id . "_prof_pic.jpg";
        $config['max_size'] = '0';
        $this->upload->initialize($config);
        if ($this->upload->do_upload())
            return true;
        else
            $this->session->set_flashdata('error', "Failed to upload your profile picture. ERROR: " . $this->upload->display_errors());
        return false;
    }

    function has_powers($role_code)
    {
        $this->db->select('c.user_name,c.user_id');
        $this->db->from('tbl_user_roles a');
        $this->db->join('tbl_user_category b', 'b.user_category_id= a.category_id');
        $this->db->join('tbl_users c', 'b.user_category_id = c.user_category_id');
        $this->db->where('a.role_code', $role_code);
        return $this->db->get()->result();
    }

    function is_assigned($technician_id)
    {
        return count($this->db->select('a.technician_id')
            ->from('tbl_ticket_staff a')
            ->join('tbl_tickets b', 'a.ticket_id = b.ticket_id')
            ->where(array('b.ticket_status < ' => 3, "a.deleted " => false, "b.deleted" => false, 'a.technician_id' => $technician_id))
            ->get()->row()) > 0;
    }

    function is_tech($user_id)
    {
        return count($this->db->get_where("tbl_technicians", array("user_id" => $user_id, "deleted" => false))->row());
    }

    /*
     * Technicians
     */

    function technician($tech_id)
    {
        return $this->db->select("*")
            ->from("tbl_technicians a")
            ->join("tbl_users b", "a.user_id =  b.user_id")
            ->where("a.technician_id", $tech_id)
            ->get()
            ->row();
    }

    function technicians()
    {
        return $this->db->select("*")
            ->from("tbl_technicians a")
            ->join("tbl_users b", "a.user_id =  b.user_id")
            ->where(array("a.deleted" => false, "b.deleted" => false))
            ->get()
            ->result();
    }

    function register_technician()
    {

    }

}
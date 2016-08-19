<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 2/12/16
 * Time: 10:58 PM
 */
class Supplies extends CI_Controller
{
    private $user_id;

    function  __construct()
    {
        parent::__construct();
        $this->users_model->security();
        $this->user_id = $this->users_model->user()->user_id;
    }

    function index()
    {
        $var['page'] = 'Supplies';
        $this->load_page('supplies_view', $var);
    }

    function load_page($page, $var)
    {
        $this->load->template($page, $var);
    }
}
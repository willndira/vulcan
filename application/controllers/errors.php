<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 1/29/16
 * Time: 2:11 PM
 */
class Errors extends CI_Controller
{

    function _404()
    {
        $var['page'] = "Error 404 - Page Not found";
        $this->load->template('error_404', $var);
    }
}
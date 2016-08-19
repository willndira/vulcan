<?php

/**
 * Created by PhpStorm.
 * User: mayneax
 * Date: 1/19/16
 * Time: 9:02 PM
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class MY_Loader extends CI_Loader
{
    public function template($template_name, $vars = array(), $return = FALSE)
    {
        $content = $this->view('header_view', $vars, $return);
        $content .= $this->view($template_name, $vars, $return);
        $content .= $this->view('footer_view', $vars, $return);

        if ($return) {
            return $content;
        }
    }
}
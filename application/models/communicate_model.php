<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Communicate_model extends CI_Model
{
    protected $user;

    function __construct()
    {
        parent::__construct();
        $this->user = $this->users_model->user();
    }

    function communicate($message_data)
    {
        $this->send_mail($message_data);
        $this->send_text($message_data);
    }

    function send_new_password($user, $user_password, $is_new = false)
    {
        $this->user = $this->users_model->user($user);
        $instructions = '';
        if (!$is_new)
            $instructions .= '<p>Your password reset request has been successful. Use the provided password to log into your account.</p>';
        else {
            $instructions .= '<p>Your account has been created successfully. Login using</p>';
            $instructions .= '<p><b>Email address: ' . $this->user->user_email . '</b></p>';
        }
        $instructions .= '<p><b>New password: ' . $user_password . '</b></p>';
        $instructions .= '<p>Once logged you can manage your account details. .</p>';

        /*
         * Send new password
         */
        $email_data = array(
            'user_id' => $this->user->user_id,
            'subject' => $is_new ? 'Account created' : 'Password Reset',
            'message' => $instructions
        );
        $this->communicate($email_data);
    }


    function send_update_member($user)
    {

    }

    function send_mail($email_data)
    {
        $this->user = $this->users_model->user($email_data['user_id']);
        $message = '<h2 style="text-align: center; color: #ff6601">' . $this->lang->line("system_name") . '</h2>';
        $message .= 'Hello <b>' . $this->user->user_name . ', </b><br/>';
        $message .= $email_data['message'];
        $message .= '<p> For more details please log on to your  <a href ="' . site_url() . '"> ' . $this->lang->line('system_name') . ' </a> portal</p>';
        $message .= '<p>' . $this->lang->line("system_name") . '</p>';

        $message .= '<div style = "font-style:italic; color:#607D8B; border-top:2px dashed;">';
        $message .= '<h4> Disclamer</h4>';
        $message .= '<p>This email message and any file(s) transmitted with it is intended solely for the individual';
        $message .= ' or entity to whom it is addressed and may contain confidential and/or legally privileged information which confidentiality';
        $message .= ' and/or privilege is not lost or waived by reason of mistaken transmission . If you have received this message by error';
        $message .= ' you are not authorized to view disseminate distribute or copy the message without the written consent of ';
        $message .= $this->lang->line('company') . ' and are requested to contact the sender by telephone or e-mail and destroy the original.';
        $message .= ' Although' . $this->lang->line('company') . 'takes all reasonable precautions to ensure that this message and any file transmitted';
        $message .= ' with it is virus free, ' . $this->lang->line('company') . ' accepts no liability for any damage that may be caused by any virus';
        $message .= ' transmitted by this email.</p></div>';


        $this->load->library('email');
        $this->email->from('wkmania1@gmail.com', $this->lang->line("system_name")); //TODO: change on deployment
        $this->email->to($this->user->user_email);
        $this->email->subject($email_data['subject']);
        $this->email->message($message);

        if ($this->email->send()) {
            $this->log_model->log_sys('Email sent to ' . $this->user->user_email);
        } else {
            $this->log_model->write_error('Failed to send email to ' . $this->user->user_email . 'ERROR:: ' . $this->email->print_debugger());
        }
    }

    function send_text($text_data)
    {
        /*
         * TODO: implement sms API here, log after success or error
         */
    }
}
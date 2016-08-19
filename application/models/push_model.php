<?php

/**
 * Created by PhpStorm.
 * User: mayne
 * Date: 4/22/16
 * Time: 5:32 PM
 */
class Push_model
{
    var $params;
    var $conn;

    public function __construct()
    {
        parent::__construct();


//        'username' => $this->SENDER_ID,
//            'server' =>'gcm-preprod.googleapis.com',
//            'resource' => 'xmpphp',
//            'password' => $this->API_KEY,
//            'host' => 'gcm.googleapis.com',
//            'port' => 5236
        $this->params = array(
            'username' => "boy1@mohannadotaibi.com",
            'password' => "123123",
            'host' => 'mohannadotaibi.com',
            'port' => 5235,
            'resource' => 'xmpphp',
            'server' => 'mohannadotaibi.com',
            'printlog' => true,
        );
        $this->conn = $this->load->library('XMPPHP_XMPP', $this->params, 'xmpp');
    }

    public function send_message($to, $message)
    {
        try {
            $this->xmpp->useEncryption(true);
            $this->xmpp->connect();
            $this->xmpp->processUntil('session_start');
            $this->xmpp->presence();
            $this->xmpp->message($to, $message);
            $this->xmpp->disconnect();
        } catch (XMPPHP_Exception $e) {
            //somewhere in between, error occurs
            echo "Error occur while sending message. Message: " . $e->getMessage();
        }
    }
}
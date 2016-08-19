<?
/*
This uses JAXL lib  https://github.com/jaxl/JAXL/releases/tag/v3.0.0
Changes for JAXL lib are...

lib/jaxl/jaxl.php ~ #358
public function get_socket_path() { //Force ssl
	return ($this->cfg['port'] == 5235 || $this->cfg['port'] == 5236 ? "ssl" : "tcp")."://".$this->cfg['host'].":".$this->cfg['port'];
}

lib/jaxl/xmpp/xmpp_stream.php
in function get_auth_pkt
$stanza->t(base64_encode( $user . chr(0) . substr($user,0,strpos($user,'@')) . chr(0) . $pass ));

Also This could be optimized with Threads f.x fetching and sending GCM in other (Worker Thread)


*/
include_once DR . 'lib/jaxl/jaxl.php';

class GCMSender
{

    const SENDER_ID = "744199497084"; //Sender Id from google api console
    const SERVER_KEY = "AIzaSyCRpiXXcwV-7zsVt5mmCrakRWgT4CNoNfs"; //Server key from google api console

    //Production server and port
    const HOST = "gcm.googleapis.com";
    const PORT = "5235";

    //DEV server and port
    //const HOST = 'gcm-preprod.googleapis.com';
    //const PORT = '5236';

    private $client;
    private $messagesSent = 0;
    private $messagesReceived = 0;

    public function __construct()
    {
        $this->client = new JAXL(array(
            'jid' => self::SENDER_ID . '@gcm.googleapis.com',
            'pass' => self::SERVER_KEY,
            'auth_type' => 'PLAIN',
            'host' => self::HOST,
            'port' => self::PORT,
            'strict' => false,
            'force_tls' => true,
            //'log_level' => JAXL_DEBUG
        ));


        $this->client->add_cb('on_auth_success', function () { //We have been authorized to send all notifications to gcm threw xmpp
            echo "On Auth Success \n";
            $this->client->set_status("available!", "dnd", 10);
            $this->sendMessages();
        });

        $this->client->add_cb("on__message", function ($stanza) { //on__message gcm xmpp protocol is a funny one
            $data = json_decode(html_entity_decode($stanza->childrens[0]->text), true);
            $messageType = $data['message_type'];
            $messageId = $data['message_id']; //message id which was sent by us
            $gcmKey = $data['from']; //gcm key;
            if ($messageType == 'nack') {
                $errorDescription = $data['error_description']; //usually empty ...
                $error = $data['error'];
                switch ($error) {
                    case 'BAD_ACK':
                        break;
                    case 'CONNECTION_DRAINING':
                        break;
                    case 'BAD_REGISTRATION':
                        break;
                    case 'DEVICE_UNREGISTERED':
                        break;
                    case 'INTERNAL_SERVER_ERROR':
                        break;
                    case 'INVALID_JSON':
                        break;
                    case 'DEVICE_MESSAGE_RATE_EXCEEDED':
                        break;
                    case 'SERVICE_UNAVAILABLE':
                        break;
                    case 'QUOTA_EXCEEDED':
                        break;
                }

                echo "On GCM Error " . $error . "\n";
            } else {
                echo 'On Gcm Message ' . $messageId . "\n";
            }

            $this->messagesReceived++;
            if ($this->messagesSent == $this->messagesReceived) {
                $this->client->send_end_stream();
            }
        });

        $this->client->add_cb('on_auth_failure', function ($reason) {
            echo "auth failure " . $reason . "\n";
            //TODO check for apikeys and restart this bastard
            $this->client->send_end_stream();
        });

        $this->client->add_cb('on_error_message', function ($stanza) {
            //TODO Maybe someday
            echo("on error message\n");
        });
    }

    private function sendMessages()
    {
        for ($i = 0; $i < 5; $i++) { //TODO fetch your gcms from db ...
            $messageId = rand(10000, 88888888); //TODO Message id from notify_queue ?

            $registrationId = "USER_REGISTRATION_ID"; //TODO registration id from android_gcm_keys

            $payload = [ //TODO notifcation data
                'hello' => "world",
                "title" => "Notification title " . $messageId,
                "text" => "message ssss",
            ];

            $collapseKey = "collapseKey"; //TODO ?
            $timeToLive = 3600 * 24 * 7; // TODO ?
            $message = GCMSender::createJsonMessage($registrationId, $messageId, $payload, $collapseKey, $timeToLive, true);
            $message = '<message id=""><gcm xmlns="google:mobile:data">' . $message . '</gcm></message>';
            $this->client->send_raw($message);
            echo "Message " . $messageId . " was sent \n";
            $this->messagesSent++;
        }
    }

    public static function createJsonMessage($toRegId, $messageId, $payload, $collapseKey, $timeToLive, $delay_while_idle)
    {
        $message = [
            'to' => $toRegId,
            'collapse_key' => $collapseKey, // Could be unset
            'time_to_live' => $timeToLive, //Could be unset
            'delay_while_idle' => true, //Could be unset
            'message_id' => (string)$messageId,
            'data' => $payload,
        ];

        return json_encode($message);
    }

    public function processGCMs()
    {
        $this->client->start();
        echo "\n\nxmpp socket ended\n";
    }
}

$time = microtime(true);

$GCMSender = new GCMSender();
$GCMSender->processGCMs();

echo "Process ended in: " . (microtime(true) - $time) . "\n";
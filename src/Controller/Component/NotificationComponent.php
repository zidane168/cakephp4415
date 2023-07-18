<?php 

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;

class NotificationComponent extends Component {

    private $server_key;
    private $sender_id;
    private $server_feedback_url;


    public function push($data, $message, $push_params = array()) {

        $status = false;
        $error_messages = array();
        $params = array();

        if (empty($message)) {
            goto result;
        }

        if (count($data) > 0) {
            $rel = $this->push_to_devices($data, $message, $push_params);
        
            if (isset($rel['status']) && ($rel['status'] == true)) {
                $status = true;

            } else {
                $status = false;
                $error_messages = $rel['error_messages'];
            }

            $params = $rel['params'];
        }
       
        result:
        return array(
            'status'                => $status,
            'error_messages'        => $error_messages,
            'params'                => $params,
        );
    }

    public function set_credential($sandbox = true) {
        if ($sandbox === true) {
            $this->server_key 				= Configure::read('push.server_key');
            $this->sender_id	 			= Configure::read('push.sender_id');
            $this->server_feedback_url 		= Configure::read('push.server_feedback_url');
        }
    }

    public function push_to_devices($device_data, $message, $push_params) {
        $ch = curl_init();

        // Set POST variables
        $this->set_credential(true);
        $url = $this->server_feedback_url;

        $headers = array(
            'Connection: keep-alive',
            'Authorization: key=' . $this->server_key,
            'Content-Type: application/json'
        );

        $fields  = array();

        $failed_case = array();
        $succeed_case = array();
        
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            // device_data
            // Array
            // (
            // 	[0] => arrayfSiW2K4tzpY:APA91bGxwkrBAI2FR3Dt5iAMwfhxLeCdSz62HCzF_4xRT4dcWw61bQxUjV7T0JK4hSKYTP2wG--A_pWGAfXlxH9vO68rR_h0crChNnGR0vnDUkgpe2KzGNsZgICEuYX0xCs5KilX3RhJ
            // 	[1] => fSiW2K4tzpY:APA91bGxwkrBAI2FR3Dt5iAMwfhxLeCdSz62HCzF_4xRT4dcWw61bQxUjV7T0JK4hSKYTP2wG--A_pWGAfXlxH9vO68rR_h0crChNnGR0vnDUkgpe2KzGNsZgICEuYX0xCs5KilX3RhJ
            // )

            // $fields = array(
            // 	// 'to' => $device_data[0]['device_token'],
            // 	'registration_ids' => $device_data,	// $device_data[0]['device_token'],
            // 	'notification' 	=> array(
            // 		'body'		=> $message['notification']['body'],
            // 		'title'		=> $message['notification']['title'],
            // 	),
            // 	'priority' => 'high',
            // 	'data' => isset($push_params['custom_data']) ? $push_params['custom_data'] : '',

            //   );
              
            $fields = array(
                'registration_ids' => $device_data,		// array
                'notification' 	=> array(
                    'body'		=> $message['notification']['body'],
                    'title'		=> $message['notification']['title'],
                ),
                'priority' => 'high',
                'data' => isset($push_params['custom_data']) ? $push_params['custom_data'] : '',

              );
            
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

            $result = curl_exec($ch);

            // {"multicast_id":9114866147884203417,"success":1,"failure":3,"canonical_ids":0,
            //	"results":[{"message_id":"0:1576208394681875%cb43adbbcb43adbb"},{"error":"InvalidRegistration"},
            //				{"error":"NotRegistered"},{"error":"NotRegistered"}]}
        
            $temp = json_decode($result, true);
            if ($temp['failure'] == 0) {		// dont have fail case
                $succeed_case = $device_data;
                $failed_case = array();

            } else {		// exist fail case
                $index = 0;
                foreach ($temp['results'] as $value) {
                    
                    if (isset($value['error'])) {
                         $failed_case[] = $device_data[$index];
                    
                    } else {
                        $succeed_case[] = $device_data[$index];
                    }

                    $index = $index + 1;
                }
            }

            $pushed = array(
                'status' => true,
                'params' => array(
                    'result'		=> $result,
                    'succeed' 		=> $succeed_case,
                    'failed' 		=> $failed_case,
                ),
            );
        } catch (\Exception $e) {
              $pushed = array(
                'status' => false,
                'params' => array(),
                'error_messages' => $e->getMessage(),
            );

        } finally {
            curl_close($ch);	 // Close the connection to the server
        }

        return $pushed;
    }
}
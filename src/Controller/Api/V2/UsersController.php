<?php

declare(strict_types=1);

namespace App\Controller\Api\V2;

use App\Controller\Api\AppController; 
use Cake\Event\EventInterface; 

use App\MyHelper\MyHelper;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{ 
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event); 
    } 

    public function checkEmailPhoneNumber()
    {
        $params = (object)array();
        $status = 500;
        $message = "";

        try {
            if ($this->request->is('post')) { 
                $payload = $this->request->getData();
                // set language/ authorization
                if (!isset($payload['user_role_id']) || empty($payload['user_role_id'])) {
                    $message    = __('missing') . " user_role_id";  

                } else {

                    $this->Api->set_language($this->language);
                    $user_id = $this->get_user_id_from_header_token();

                    if ((isset($payload['email']) || !empty($payload['email']))) {
                        $result = $this->Users->is_duplicate_email($payload['email'], $payload['user_role_id'],  $user_id);
                        if ($result) {
                            $message = __('email_is_exists');
                            goto api_result;
                        }

                    } elseif ((isset($payload['phone_number']) || !empty($payload['phone_number']))) {
                        $result = $this->Users->is_duplicate_phone($payload['phone_number'], $payload['user_role_id'],  $user_id);
                        if ($result) {
                            $message = __('phone_is_exists'); 
                            goto api_result;
                        }
                    } 

                    $message        = __('Valid');
                    $status = 200;
                }
            } else {
                $message        = __('invalid_method');
                $status         = 500;
            }
        } catch (\Exception $e) {
            $response = $this->show_catch_message_api($e);
            $this->response = $response['response'];
            $message        = $response['message'];
            $status         = $response['status'];
        }

        api_result:
        $this->Api->set_result($status, $message, $params);
        $this->Api->output($this);
    }
}

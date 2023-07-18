<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Utility\Security;
use Firebase\JWT\JWT;
use Cake\ORM\TableRegistry;

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
        $authentications = [
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "getProfile",
                'user_role_ids' =>  [MyHelper::PARENT, MyHelper::STAFF],
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "parentChangePassword",
                'user_role_id' =>  MyHelper::PARENT,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "staffChangePassword",
                'user_role_id' =>  MyHelper::STAFF,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "changePassword",
                'user_role_ids' =>  [MyHelper::PARENT, MyHelper::STAFF],
            ],
        ];
        $this->requireAuthenticate($authentications);
    }

    public function parentLogin()
    {
        $this->Api->init();
        $rel = (object)array();
        $status = 404;
        $message = "";
        try {
            if ($this->request->is('post')) {

                $payload = $this->request->getData();

                if (!isset($payload['username']) || empty($payload['username'])) {
                    $message = __('missing_parameter') . ' username';
                } elseif (!isset($payload['password']) || empty($payload['password'])) {
                    $message = __('missing_parameter') . ' password';
                } else {

                    // set language/ authorization
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }
                    $this->Api->set_language($this->language);
                    // set language/ authorization 

                    // check username is email / phone_number
                    $user = $this->Users->login($payload);

                    if (!$user || empty($user)) {
                        $this->response = $this->response->withStatus(401);
                        $message = "Unauthorized";
                        $status = 401;
                    } elseif ($user->user_role_id === MyHelper::PARENT) {
                        $rel = [
                            'sub'   => $user->id,
                            'user_role_id' => $user->user_role_id,
                            'exp'   => $user->exp,
                            'token' => $user->token,
                        ];

                        $message = __d("user", "login_successfully");
                        $status = 200;
                    } else {
                        $this->response = $this->response->withStatus(401);
                        $message = "Unauthorized";
                        $status = 401;
                    }
                }
            } else {
                $this->response = $this->response->withStatus(500);
                $message = "Invalid Method";
                $status = 500;
            }
        } catch (\Exception $ex) {
            $this->response = $this->response->withStatus(501);
            $message = "Internal Server Error" . json_encode($ex);
            $status = 501;
        }

        api_result:
        $this->Api->set_result($status, $message, $rel);
        $this->Api->output($this);
    }

    public function staffLogin()
    {
        $this->Api->init();
        $rel = (object)array();
        $status = 404;
        $message = "";
        try {
            if ($this->request->is('post')) {

                $payload = $this->request->getData();

                if (!isset($payload['username']) || empty($payload['username'])) {
                    $message = __('missing_parameter') . ' username';
                } elseif (!isset($payload['password']) || empty($payload['password'])) {
                    $message = __('missing_parameter') . ' password';
                } else {

                    // set language/ authorization
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }
                    $this->Api->set_language($this->language);
                    // set language/ authorization


                    // check username is email / phone_number
                    $user = $this->Users->login($payload);

                    if (!$user || empty($user)) {
                        $this->response = $this->response->withStatus(401);
                        $message = "Unauthorized";
                        $status = 401;
                    } elseif ($user->user_role_id === MyHelper::STAFF) {
                        $rel = [
                            'sub'   => $user->id,
                            'user_role_id' => $user->user_role_id,
                            'exp'   => $user->exp,
                            'token' => $user->token,
                        ];

                        $message = __d("user", "login_successfully");
                        $status = 200;
                    } else {
                        $this->response = $this->response->withStatus(401);
                        $message = "Unauthorized";
                        $status = 401;
                    }
                }
            } else {
                $this->response = $this->response->withStatus(500);
                $message = "Invalid Method";
                $status = 500;
            }
        } catch (\Exception $ex) {
            $this->response = $this->response->withStatus(501);
            $message = "Internal Server Error" . json_encode($ex);
            $status = 501;
        }

        api_result:
        $this->Api->set_result($status, $message, $rel);
        $this->Api->output($this);
    }


    public function login()
    {
        $this->Api->init();
        $rel = (object)array();
        $status = 404;
        $message = "";
        try {
            if ($this->request->is('post')) {

                $payload = $this->request->getData();

                if (!isset($payload['username']) || empty($payload['username'])) {
                    $message = __('missing_parameter') . ' username';
                } elseif (!isset($payload['password']) || empty($payload['password'])) {
                    $message = __('missing_parameter') . ' password';
                } elseif (!isset($payload['user_role_id']) || empty($payload['user_role_id'])) {
                    $message = __('missing_parameter') . ' user_role_id';
                } else {

                    // set language/ authorization
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }
                    $this->Api->set_language($this->language);
                    // set language/ authorization 

                    // check username is email / phone_number
                    $user = $this->Users->login($payload);

                    if (!$user || empty($user)) {

                        $this->response = $this->response->withStatus(401);
                        $message = "Unauthorized";
                        $status = 401;
                    } elseif ($user->user_role_id == $payload['user_role_id']) {
                        $rel = [
                            'sub'   => $user->id,
                            'user_role_id' => $user->user_role_id,
                            'exp'   => $user->exp,
                            'token' => $user->token,
                            'max_kids' => MyHelper::MAX_KIDS
                        ];

                        $message = __d("user", "login_successfully");
                        $status = 200;
                    } else {
                        $this->response = $this->response->withStatus(401);
                        $message = "Unauthorized";
                        $status = 401;
                    }
                }
            } else {
                $this->response = $this->response->withStatus(500);
                $message = "Invalid Method";
                $status = 500;
            }
        } catch (\Exception $ex) {
            $this->response = $this->response->withStatus(501);
            $message = "Internal Server Error" . json_encode($ex);
            $status = 501;
        }

        api_result:
        $this->Api->set_result($status, $message, $rel);
        $this->Api->output($this);
    }

    public function parentChangePassword()
    {
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();
                if (!isset($payload['old_pw']) || empty($payload['old_pw'])) {
                    $message = __('missing_parameter') . ' old_pw';
                    goto api_result;
                } elseif (!isset($payload['new_pw']) || empty($payload['new_pw'])) {
                    $message = __('missing_parameter') . ' new_pw';
                    goto api_result;
                }

                // set language/ authorization
                $payload_result = $this->get_payload_result();
                if ($payload_result['status'] != 200) {
                    $status = $payload_result['status'];
                    $this->response = $this->response->withStatus($this->status);
                    $message = $this->message;
                    goto api_result;
                }
                $this->Api->set_language($this->language);
                // set language/ authorization

                $result = $this->Users->change_password($this->user->id, $payload['old_pw'], $payload['new_pw']);
                $status = $result['status'];
                $message = $result['message'];
                $params = $result['params'];
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

    public function staffChangePassword()
    {
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();
                if (!isset($payload['old_pw']) || empty($payload['old_pw'])) {
                    $message = __('missing_parameter') . ' old_pw';
                    goto api_result;
                } elseif (!isset($payload['new_pw']) || empty($payload['new_pw'])) {
                    $message = __('missing_parameter') . ' new_pw';
                    goto api_result;
                }

                // set language/ authorization
                $payload_result = $this->get_payload_result();
                if ($payload_result['status'] != 200) {
                    $status = $payload_result['status'];
                    $this->response = $this->response->withStatus($this->status);
                    $message = $this->message;
                    goto api_result;
                }
                $this->Api->set_language($this->language);
                // set language/ authorization

                $result = $this->Users->change_password($this->user->id, $payload['old_pw'], $payload['new_pw']);
                $status = $result['status'];
                $message = $result['message'];
                $params = $result['params'];
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

    public function changePassword()
    {
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();
                if (!isset($payload['old_pw']) || empty($payload['old_pw'])) {
                    $message = __('missing_parameter') . ' old_pw';
                } elseif (!isset($payload['new_pw']) || empty($payload['new_pw'])) {
                    $message = __('missing_parameter') . ' new_pw';
                } else {

                    // set language/ authorization
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }
                    $this->Api->set_language($this->language);
                    // set language/ authorization

                    $result = $this->Users->change_password($this->user->id, $payload['old_pw'], $payload['new_pw']);
                    $status = $result['status'];
                    $message = $result['message'];
                    $params = $result['params'];
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

    public function parentResetPassword()
    {
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();
                if (!isset($payload['token_reset']) || empty($payload['token_reset'])) {
                    $message    = __('missing') . " token_reset";
                } elseif (!isset($payload['password']) || empty($payload['password'])) {
                    $message    = __('missing') . " password";
                } else {
                    $this->Api->set_language($this->language);
                    // set language/ authorization

                    $result = $this->Users->reset_password($payload, MyHelper::PARENT);
                    $status = $result['status'];
                    $message = $result['message'];
                }
                // set language/ authorization

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

    public function staffResetPassword()
    {
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();

                if (!isset($payload['phone_number']) || empty($payload['phone_number'])) {
                    $message    = __('missing') . " phone_number";
                } elseif (!isset($payload['code']) || empty($payload['code'])) {
                    $message    = __('missing') . " code";
                } elseif (!isset($payload['password']) || empty($payload['password'])) {
                    $message    = __('missing') . " password";
                } else {
                    $this->Api->set_language($this->language);
                    // set language/ authorization

                    $result = $this->Users->reset_password($payload, MyHelper::STAFF);
                    $status = $result['status'];
                    $message = $result['message'];
                }
                // set language/ authorization

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

    public function resetPassword()
    {
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();
                if (!isset($payload['token_reset']) || empty($payload['token_reset'])) {
                    $message    = __('missing') . " token_reset";
                } elseif (!isset($payload['password']) || empty($payload['password'])) {
                    $message    = __('missing') . " password";
                } else {
                    $this->Api->set_language($this->language);
                    // set language/ authorization

                    $result = $this->Users->reset_password($payload);
                    $status = $result['status'];
                    $message = $result['message'];
                }
                // set language/ authorization

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

    public function parentSendSmsVerificationCode()
    {
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();
                if ((!isset($payload['phone_number']) || empty($payload['phone_number']))) {
                    $message = __('missing_parameter') .  'phone_number';
                } elseif ((!isset($payload['verification_type']) || empty($payload['verification_type']))) {
                    $message = __('missing_parameter') .  ' verification_type';
                } else {
                    $this->Api->set_language($this->language);
                    $temp = 1;
                    $lang = $temp == 1 ? 'zh_HK' : 'en_US';
                    $receiver = array(  // array phone_number
                        array(
                            'phone'     => "+852" . $payload['phone_number'],
                            'language'  => $lang,
                        )
                    );
                    // set language/ authorization
                    $obj_UserVerifications = TableRegistry::get('UserVerifications');
                    $db = $this->Users->getConnection();
                    $db->begin();
                    if ($payload['verification_type'] == array_search('Forgot password', $obj_UserVerifications->verification_types)) {
                        // find user 
                        $user = $this->Users->find('all', [
                            'conditions' => [
                                'Users.phone_number'        => $payload['phone_number'],
                                'Users.user_role_id'    => MyHelper::PARENT
                            ]
                        ])->first();

                        if (!$user) {
                            $status = 401;
                            $message = 'INVALID_USER';
                            goto api_result;
                        }
                    }
                    // gen code, reset table info, save table info into PatientVerifications

                    $verification_method = array_search('Sms', $obj_UserVerifications->verification_methods);

                    // send succeed, disabled oldest
                    $obj_UserVerifications->update_enabled_to_disabled($payload['phone_number'],  $payload['verification_type'], $verification_method);

                    $is_dev = null;

                    if ((isset($payload['is_dev']) && !empty($payload['is_dev']))) {
                        $is_dev = $payload['is_dev'];
                    }

                    $result_UserVerification = $obj_UserVerifications->handling_verify_data(
                        $verification_method,
                        $payload['verification_type'],
                        '',
                        $payload['phone_number'],
                        $is_dev
                    );

                    if ($result_UserVerification['status'] == 999) {
                        $status = 999;
                        $message = $result_UserVerification['message'];
                        goto api_result;
                    }

                    $sms_message = "";
                    if ($payload['verification_type'] == array_search('Forgot password', $obj_UserVerifications->verification_types)) {
                        $sms_message = array(
                            $lang => __d('user', 'forgot_password', $result_UserVerification['verify_code'], Configure::read('sms.timeout_message'))
                        );
                    }
                    $title = array($lang => Configure::read('site.name'));

                    $sent_data = $this->Sms->send_sms($receiver, $title, $sms_message, 'Verification');

                    if (!$sent_data['status']) {
                        $this->write_api_log($payload, $sent_data['error_message'], $params, 'error');
                        $db->rollback();
                        $status = 999;
                        $message = 'SEND_SMS_FAILED';
                        goto api_result;
                    } else {
                        if (isset($sent_data['params']['failed']) && !empty($sent_data['params']['failed'])) {
                            $db->rollback();
                            $status = 999;
                            $message = 'SEND_SMS_FAILED';
                            goto api_result;
                        }
                    }

                    $params = array(
                        'number_sent_verification'     => $result_UserVerification['number_sent_verification']
                    );

                    $db->commit();
                    $status = 200;
                    $message = __d('user', 'send_sms_successfully');
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

    public function staffSendSmsVerificationCode()
    {
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();
                if ((!isset($payload['phone_number']) || empty($payload['phone_number']))) {
                    $message = __('missing_parameter') .  'phone_number';
                } elseif ((!isset($payload['verification_type']) || empty($payload['verification_type']))) {
                    $message = __('missing_parameter') .  ' verification_type';
                } else {
                    $this->Api->set_language($this->language);
                    $temp = 1;
                    $lang = $temp == 1 ? 'zh_HK' : 'en_US';
                    $receiver = array(  // array phone_number
                        array(
                            'phone'     => "+852" . $payload['phone_number'],
                            'language'  => $lang,
                        )
                    );
                    // set language/ authorization
                    $obj_UserVerifications = TableRegistry::get('UserVerifications');
                    $db = $this->Users->getConnection();
                    $db->begin();
                    if ($payload['verification_type'] == array_search('Forgot password', $obj_UserVerifications->verification_types)) {
                        // find user 
                        $user = $this->Users->find('all', [
                            'conditions' => [
                                'Users.phone_number'        => $payload['phone_number'],
                                'Users.user_role_id'    => MyHelper::STAFF
                            ]
                        ])->first();

                        if (!$user) {
                            $status = 401;
                            $message = 'INVALID_USER';
                            goto api_result;
                        }
                    }
                    // gen code, reset table info, save table info into PatientVerifications

                    $verification_method = array_search('Sms', $obj_UserVerifications->verification_methods);

                    // send succeed, disabled oldest
                    $obj_UserVerifications->update_enabled_to_disabled($payload['phone_number'],  $payload['verification_type'], $verification_method);

                    $is_dev = null;

                    if ((isset($payload['is_dev']) && !empty($payload['is_dev']))) {
                        $is_dev = $payload['is_dev'];
                    }

                    $result_UserVerification = $obj_UserVerifications->handling_verify_data(
                        $verification_method,
                        $payload['verification_type'],
                        '',
                        $payload['phone_number'],
                        $is_dev
                    );

                    if ($result_UserVerification['status'] == 999) {
                        $status = 999;
                        $message = $result_UserVerification['message'];
                        goto api_result;
                    }

                    $sms_message = "";
                    if ($payload['verification_type'] == array_search('Forgot password', $obj_UserVerifications->verification_types)) {
                        $sms_message = array(
                            $lang => __d('user', 'forgot_password', $result_UserVerification['verify_code'], Configure::read('sms.timeout_message'))
                        );
                    }
                    $title = array($lang => Configure::read('site.name'));

                    $sent_data = $this->Sms->send_sms($receiver, $title, $sms_message, 'Verification');

                    if (!$sent_data['status']) {
                        $this->write_api_log($payload, $sent_data['error_message'], $params, 'error');
                        $db->rollback();
                        $status = 999;
                        $message = 'SEND_SMS_FAILED';
                        goto api_result;
                    } else {
                        if (isset($sent_data['params']['failed']) && !empty($sent_data['params']['failed'])) {
                            $db->rollback();
                            $status = 999;
                            $message = 'SEND_SMS_FAILED';
                            goto api_result;
                        }
                    }

                    $params = array(
                        'number_sent_verification'     => $result_UserVerification['number_sent_verification']
                    );

                    $db->commit();
                    $status = 200;
                    $message = __d('user', 'send_sms_successfully');
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

    public function sendSmsVerificationCode()
    {
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();
                if ((!isset($payload['phone_number']) || empty($payload['phone_number']))) {
                    $message = __('missing_parameter') .  'phone_number';
                } elseif ((!isset($payload['verification_type']) || empty($payload['verification_type']))) {
                    $message = __('missing_parameter') .  ' verification_type';
                } else {
                    $this->Api->set_language($this->language);
                    $temp = 1;
                    $lang = $temp == 1 ? 'zh_HK' : 'en_US';
                    $receiver = array(  // array phone_number
                        array(
                            'phone'     => "+852" . $payload['phone_number'],
                            'language'  => $lang,
                        )
                    );
                    // set language/ authorization
                    $obj_UserVerifications = TableRegistry::get('UserVerifications');
                    $db = $this->Users->getConnection();
                    $db->begin();
                    if ($payload['verification_type'] == array_search('Forgot password', $obj_UserVerifications->verification_types)) {
                        // find user 
                        $user = $this->Users->find('all', [
                            'conditions' => [
                                'Users.phone_number'        => $payload['phone_number'],
                                // 'Users.user_role_id'    => MyHelper::STAFF
                            ]
                        ])->first();

                        if (!$user) {
                            $status = 401;
                            $message = 'INVALID_USER';
                            goto api_result;
                        }
                    }
                    // gen code, reset table info, save table info into PatientVerifications

                    $verification_method = array_search('Sms', $obj_UserVerifications->verification_methods);

                    // send succeed, disabled oldest
                    $obj_UserVerifications->update_enabled_to_disabled($payload['phone_number'],  $payload['verification_type'], $verification_method);

                    $is_dev = null;

                    if ((isset($payload['is_dev']) && !empty($payload['is_dev']))) {
                        $is_dev = $payload['is_dev'];
                    }

                    $result_UserVerification = $obj_UserVerifications->handling_verify_data(
                        $verification_method,
                        $payload['verification_type'],
                        '',
                        $payload['phone_number'],
                        $is_dev
                    );

                    if ($result_UserVerification['status'] == 999) {
                        $status = 999;
                        $message = $result_UserVerification['message'];
                        goto api_result;
                    }

                    $sms_message = "";
                    if ($payload['verification_type'] == array_search('Forgot password', $obj_UserVerifications->verification_types)) {
                        $sms_message = array(
                            $lang => __d('user', 'forgot_password', $result_UserVerification['verify_code'], Configure::read('sms.timeout_message'))
                        );
                    }
                    $title = array($lang => Configure::read('site.name'));

                    $sent_data = $this->Sms->send_sms($receiver, $title, $sms_message, 'Verification');

                    if (!$sent_data['status']) {
                        $this->write_api_log($payload, $sent_data['error_message'], $params, 'error');
                        $db->rollback();
                        $status = 999;
                        $message = 'SEND_SMS_FAILED';
                        goto api_result;
                    } else {
                        if (isset($sent_data['params']['failed']) && !empty($sent_data['params']['failed'])) {
                            $db->rollback();
                            $status = 999;
                            $message = 'SEND_SMS_FAILED';
                            goto api_result;
                        }
                    }

                    $params = array(
                        'number_sent_verification'     => $result_UserVerification['number_sent_verification']
                    );

                    $db->commit();
                    $status = 200;
                    $message = __d('user', 'send_sms_successfully');
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

    public function checkSmsForgotCode()
    {
        $params = (object)array();
        $status = 500;
        $message = "";

        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();

                if (!isset($payload['phone_number']) || empty($payload['phone_number'])) {
                    $message    = __('missing') . " phone_number";
                } elseif (!isset($payload['code']) || empty($payload['code'])) {
                    $message    = __('missing') . " code";
                } else {
                    $this->Api->set_language($this->language);
                    $response = $this->Users->check_sms_forgot_code($payload);
                    $status     = $response['status'];
                    $message    = $response['message'];
                    $params     = $response['params'];

                    $this->write_api_log($payload, $response, $params);
                }
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

    public function checkTokenReset()
    {
        $params = (object)array();
        $status = 500;
        $message = "";

        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();

                if (!isset($payload['token_reset']) || empty($payload['token_reset'])) {
                    $message    = __('missing') . " token_reset";
                } else {
                    $this->Api->set_language($this->language);
                    $response = $this->Users->check_token_reset($payload['token_reset']);
                    $status     = $response['status'];
                    $message    = $response['message'];
                    $params     = $response['params'];

                    $this->write_api_log($payload, $response, $params);
                }
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

    public function getProfile()
    {
        $params = (object)array();
        $status = 500;
        $message = "";

        try {
            if ($this->request->is('get')) {

                $payload = $this->request->getQuery();
                // set language/ authorization

                $payload_result = $this->get_payload_result();
                if ($payload_result['status'] != 200) {
                    $status = $payload_result['status'];
                    $this->response = $this->response->withStatus($this->status);
                    $message = $this->message;
                    goto api_result;
                }
                $this->Api->set_language($this->language);
                // set language/ authorization 

                if ($this->user->user_role_id == MyHelper::PARENT) {
                    $result_CidcParent = $this->loadModel('CidcParents')->get_by_user_id($this->user->id, $this->language);
                    $cidc_parent = [];

                    $default_girl   = Configure::read('host_name') .  "/img/cidckids/student/girl.svg";
                    $default_boy    = Configure::read('host_name') .  "/img/cidckids/student/boy.svg";

                    if ($result_CidcParent) {

                        $kids = [];
                        foreach ($result_CidcParent->kids as $kid) {
                            $avatar = $kid->gender == MyHelper::MALE ? $default_boy :  $default_girl;

                            $kids[] = [
                                'id'        => $kid->id, 
                                'relationship' => [
                                    'id'   => $kid->relationship->id,
                                    'name' => $kid->relationship->relationship_languages[0]->name
                                ],
                                'name'      => $kid->kid_languages[0]->name,
                                'dob'       => $kid->dob->format('d-m-Y'),
                                'gender'    => $kid->gender == MyHelper::MALE ? __d('parent', 'male') : __d('parent', 'female'),
                                'gender_id' => (int)$kid->gender,
                                'avatar'    => $kid->kid_images ? Configure::read('host_name') . '/' . $kid->kid_images[0]->path : $avatar,
                            ];
                        }

                        // format data;
                        $en_US_name = null;
                        $zh_HK_name = null; 
                        foreach ($result_CidcParent->cidc_parent_languages as $item) {
                            switch ($item->alias) {
                                case 'en_US':
                                    $en_US_name = $item->name;
                                    break;
                                case 'zh_HK':
                                    $zh_HK_name = $item->name;
                                    break; 

                                default:
                                    break;
                            }
                        }

                        $avatar = $result_CidcParent->gender == MyHelper::MALE ? $default_boy :  $default_girl;

                        $cidc_parent = [
                            'id'        => $result_CidcParent->id,
                            'name'      => $result_CidcParent->cidc_parent_languages[0]->name,
                            'en_US_name' => $en_US_name,
                            'zh_HK_name' => $zh_HK_name, 
                            'email'     => $this->user->email,
                            'gender'    => $result_CidcParent->gender == MyHelper::MALE ? __d('parent', 'male') : __d('parent', 'female'),
                            'gender_id' => (int)$result_CidcParent->gender,
                            'phone_number' => $this->user->phone_number,
                            'avatar'    => $result_CidcParent->cidc_parent_images ? Configure::read('host_name') .  '/' . $result_CidcParent->cidc_parent_images[0]->path : $avatar,
                            'kids'      => $kids,
                            'user_role_id' => $this->user->user_role_id,
                            'max_kids'  => MyHelper::MAX_KIDS,
                            'address'   => $result_CidcParent->address ? $result_CidcParent->address : ""
                        ];
                        $params = $cidc_parent;
                    }
                } elseif ($this->user->user_role_id == MyHelper::STAFF) {
                    $result_staff = $this->loadModel('Staffs')->get_by_user_id($this->user->id, $this->language);

                    if ($result_staff) {
                        $params = [
                            'id'            => $result_staff->id,
                            'phone_number'  => $this->user->phone_number,
                            'email'         => $this->user->email,
                            'gender'        => $result_staff->gender == MyHelper::MALE ? __('male') : __('female'),
                            'gender_id'     => (int)$result_staff->gender,
                            'center'        => $result_staff->center,
                            'user_role_id' => $this->user->user_role_id
                        ];
                    }
                }

                $message        = __('RETRIEVE_DATA_SUCCESSFULLY');
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

    public function checkEmailPhoneNumber()
    {
        $params = (object)array();
        $status = 500;
        $message = "";

        try {
            if ($this->request->is('get')) {

                $payload = $this->request->getQuery();
                
                if (!isset($payload['user_role_id']) || empty($payload['user_role_id'])) {
                    $message    = __('missing') . " user_role_id";
                } elseif (
                    !isset($payload['email']) &&
                    empty($payload['email']) &&
                    !isset($payload['phone_number']) &&
                    empty($payload['phone_number'])
                ) {
                    $message    = __('missing') . " email_and_phone_number";
                } else {
                    if ((isset($payload['email']) || !empty($payload['email']))) {
                        $result = $this->Users->is_duplicate_email($payload['email'], $payload['user_role_id']);
                        if ($result) {
                            $message = "EMAIL_IS_USED";
                            goto api_result;
                        }
                    } elseif ((isset($payload['phone_number']) || !empty($payload['phone_number']))) {
                        $result = $this->Users->is_duplicate_phone($payload['phone_number'], $payload['user_role_id']);
                        if ($result) {
                            $message = "PHONE_IS_USED";
                            goto api_result;
                        }
                    }
                    $message = "VALID_DATA";
                    $params = [];
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

<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AppController;
use App\MyHelper\MyHelper;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;

class StudentRegisterClassesController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $authentications = [
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "setOrder",
                'user_role_id' =>  MyHelper::PARENT,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "getDetailOrder",
                'user_role_id' =>  MyHelper::PARENT,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "getListStudentByClass",
                'user_role_id' =>  MyHelper::STAFF,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "getDetailDatesOrder",
                'user_role_id' =>  MyHelper::PARENT,
            ],
        ];
        $this->requireAuthenticate($authentications);
    }

    public function setOrder()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();

                if (!isset($payload['kid_id']) || empty($payload['kid_id'])) {
                    $message = __('missing_parameter') . ' kid_id';
                } elseif (!isset($payload['cidc_class_id']) || empty($payload['cidc_class_id'])) {
                    $message = __('missing_parameter') . ' cidc_class_id';
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

                    $db = $this->StudentRegisterClasses->getConnection();
                    $db->begin();

                    $result = $this->StudentRegisterClasses->set_order($payload);
                    $status = $result['status'];
                    $params = null;

                    if ($status == 200) {

                        // send system message
                        $cidcClass = $this->loadModel('CidcClasses')->get($payload['cidc_class_id']);
                        $class_info = $cidcClass->name . '-' . $cidcClass->code;

                        $obj_systemMessage = $this->loadModel('SystemMessages');
                        $obj_kid        = $this->loadModel('Kids');
                        $kid_infos      = $obj_kid->get_kid_info($payload['kid_id'], $this->language);

                        if ($class_info && !empty($class_info) && $kid_infos && !empty($kid_infos)) {
                            $cidc_parent_id = $this->loadModel('CidcParents')->get_id_by_user($this->user->id);
                            $kid_info       = $obj_kid->format_kid_info($kid_infos);
                            $arr_messages   = $obj_systemMessage->create_register_successfully_messages($class_info, $kid_info);

                            $result_SystemMessage =  $this->loadModel('SystemMessages')->create($payload['cidc_class_id'], $cidc_parent_id, $payload['kid_id'], $arr_messages);
                            if ($result_SystemMessage['status'] == 200) {
                                $message = 'REGISTER_CLASS_SUCCESS';
                            } else {
                                $message = 'REGISTER_CLASS_SUCCESS_WITHOUT_MESSAGE';
                            }
                        } else {
                            $message = 'REGISTER_CLASS_SUCCESS';
                        }

                        $db->commit();
                        $params = $result['params'];
                    } else {
                        $db->rollback();
                        $message = $result['message'];
                    }
                }
            }
        } catch (\Exception $ex) {
            $this->response = $this->response->withStatus(501);
            $message = $ex->getMessage();
            $status = 501;
        }

        api_result:
        $this->Api->set_result($status, $message, $params);
        $this->Api->output($this);
    }

    public function getDetailOrder()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('get')) {
                $payload = $this->request->getQuery();

                if (!isset($payload['order_id']) || empty($payload['order_id'])) {
                    $message = __('missing_parameter') . ' order_id';
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

                    $result = $this->StudentRegisterClasses->get_detail_order($payload['order_id'], $this->language);
                    $status = $result['status'];

                    $message = $result['message'];
                    $params = $result['params'];
                    $message = $result['message'];
                }
            }
        } catch (\Exception $ex) {
            $this->response = $this->response->withStatus(501);
            $message = $ex->getMessage();
            $status = 501;
        }

        api_result:
        $this->Api->set_result($status, $message, $params);
        $this->Api->output($this);
    }

    public function getListStudentByClass()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('get')) {
                $payload = $this->request->getQuery();

                if (!isset($payload['cidc_class_id']) || empty($payload['cidc_class_id'])) {
                    $message = __('missing_parameter') . ' cidc_class_id';
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
                    $class = TableRegistry::get('CidcClasses')->find('all', [
                        'conditions' => [
                            'CidcClasses.enabled' => true,
                            'CidcClasses.id' => $payload['cidc_class_id']
                        ]
                    ])->first();
                    if (!$class) {
                        $message = 'NOT_FOUND_CLASS';
                        $params = null;
                        goto api_result;
                    }
                    $result = $this->StudentRegisterClasses->get_list_student_by_class($payload, $this->language);

                    $message = __('retrieve_data_successfully');
                    $status = 200;
                    $params = $result ? $result : null;
                }
            }
        } catch (\Exception $ex) {
            $this->response = $this->response->withStatus(501);
            $message = $ex->getMessage();
            $status = 501;
        }
        api_result:
        $this->Api->set_result($status, $message, $params);
        $this->Api->output($this);
    }

    public function getDetailDatesOrder()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('get')) {
                $payload = $this->request->getQuery();

                if (!isset($payload['order_id']) || empty($payload['order_id'])) {
                    $message = __('missing_parameter') . ' order_id';
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

                    $result = $this->StudentRegisterClasses->get_detail_date_order($payload['order_id'], $this->language);
                    $status = $result['status'];

                    $message = $result['message'];
                    $params = $result['params'];
                    $message = $result['message'];
                }
            }
        } catch (\Exception $ex) {
            $this->response = $this->response->withStatus(501);
            $message = $ex->getMessage();
            $status = 501;
        }

        api_result:
        $this->Api->set_result($status, $message, $params);
        $this->Api->output($this);
    }
}

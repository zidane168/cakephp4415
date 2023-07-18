<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AppController;
use App\MyHelper\MyHelper;
use Cake\Event\EventInterface;

class SystemMessagesController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $authentications = [
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "getListPagination",
                'user_role_id' =>  MyHelper::PARENT,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "getDetail",
                'user_role_id' =>  MyHelper::PARENT,
            ],

            [
                'controller' => $this->request->getParam('controller'),
                'action' => "readAll",
                'user_role_id' =>  MyHelper::PARENT,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "remove",
                'user_role_id' =>  MyHelper::PARENT,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "getNumberUnread",
                'user_role_id' =>  MyHelper::PARENT,
            ],
        ];
        $this->requireAuthenticate($authentications);
    }

    public function getListPagination()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 999;
        $message = "";
        try {
            if ($this->request->is('get')) {
                $payload = $this->request->getQuery();

                if (!isset($payload['limit']) || empty($payload['limit'])) {
                    $message = __('missing_parameter') . ' limit';
                } elseif (!isset($payload['page']) || empty($payload['page'])) {
                    $message = __('missing_parameter') . ' page';
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

                    // get cidc_parent_id by user id
                    $cidc_parent_id = $this->loadModel('CidcParents')->get_id_by_user($this->user->id);

                    $params = $this->SystemMessages->get_list_pagination($this->language, $cidc_parent_id, $payload['limit'], $payload['page']);
                    $message = 'RETRIEVE_DATA_SUCCESSFULLY';
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

    public function getDetail()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 999;
        $message = "";
        try {
            if ($this->request->is('get')) {
                $payload = $this->request->getQuery();

                if (!isset($payload['id']) || empty($payload['id'])) {
                    $message = __('missing_parameter') . ' id';
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

                    $params = $this->SystemMessages->getDetail($payload['id'], $this->language);
                    $message = 'RETRIEVE_DATA_SUCCESSFULLY';
                }
            } else {
                $status = 501;
                $message = __('wrong_method');
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

    public function readAll()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 999;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();

                if (!isset($payload['ids']) || empty($payload['ids'])) {
                    $message = __('missing_parameter') . ' ids';
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

                    // get cidc_parent_id by user id
                    $cidc_parent_id = $this->loadModel('CidcParents')->get_id_by_user($this->user->id);
                    $ids = json_decode($payload['ids']);
                    $this->SystemMessages->read_all($ids, $cidc_parent_id);
                    $message = 'DATA_WAS_UPDATED';
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

    public function remove()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 999;
        $message = "";
        try {
            if ($this->request->is('delete')) {
                $payload = $this->request->getQuery();

                if (!isset($payload['id']) || empty($payload['id'])) {
                    $message = __('missing_parameter') . ' id';
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

                    // get cidc_parent_id by user id
                    $cidc_parent_id = $this->loadModel('CidcParents')->get_id_by_user($this->user->id);
                    $id = json_decode($payload['id']);
                    $params = $this->SystemMessages->remove($id, $cidc_parent_id);
                    if (!$params) {
                        $message = 'WRONG_ID';
                        goto api_result;
                    }
                    $message = 'DATA_IS_DELETED';
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

    public function getNumberUnread()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 999;
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

                // get cidc_parent_id by user id
                $cidc_parent_id = $this->loadModel('CidcParents')->get_id_by_user($this->user->id);
                $params = $this->SystemMessages->number_unread($cidc_parent_id);
                $message = 'RETRIEVE_DATA_SUCCESSFULLY';
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

<?php

declare(strict_types=1);

namespace App\Controller\Api\V2;

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
                'action' => "setCartOrder",
                'user_role_id' =>  MyHelper::PARENT,
            ], 
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "isValidateCart",
                'user_role_id' =>  MyHelper::PARENT,
            ], 
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "getOrder",
                'user_role_id' =>  MyHelper::PARENT,
            ], 
        ];
        $this->requireAuthenticate($authentications);
    } 

    public function setCartOrder()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 500;
        $message = "";

        $db = $this->StudentRegisterClasses->getConnection();
        $db->begin();

        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();

                if (!isset($payload['cart']) || empty($payload['cart'])) {  // [{"kid_id": 1, "cidc_class_id": 200}, { ... }]
                    $message = __('missing_parameter') . ' cart'; 
                
                } else {
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }
                    $this->Api->set_language($this->language);  
                    $result = $this->StudentRegisterClasses->set_cart_order($payload['cart'], $this->language);
                    $status = $result['status']; 

                    if ($status == 200) { 
                        // show detail order
                        $params = $result['params'];
                     
                        $obj_Order = $this->loadModel('Orders');
                        $cidc_parent_id = $this->user->cidc_parents[0]->id;
                        $details_order = $obj_Order->get_order($params['order_id'],  $cidc_parent_id, $this->language);
                        $params = $details_order;  
  

                        if (empty($details_order)) {
                            $message = 'INVALID_ORDER';
                        
                        } else { 

                            $is_send_message = TableRegistry::get('SystemMessages')->send_system_message($payload['cart'], $this->user->id, $this->language);
                            $db->commit();
                            if ($is_send_message) {
                                $message = 'REGISTER_CLASS_SUCCESS';
                            } else {
                                $message = 'REGISTER_CLASS_SUCCESS_WITHOUT_MESSAGE';
                            }   
                        }
                    
                    }  else {
                        $db->rollback();
                        $rel = $this->StudentRegisterClasses->is_validate_cart($payload['cart'], $this->language);
                        $status = $rel['status'];
                        $message = $rel['message'];  
                        $params  = $rel['params'];  
                    } 
                }
            }
        } catch (\Exception $ex) {
            $db->rollback();
            $this->response = $this->response->withStatus(501);
            $message = $ex->getMessage();
            $status = 501;
        }

        api_result:
        $this->Api->set_result($status, $message, $params);
        $this->Api->output($this);
    } 

    public function isValidateCart()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 500;
        $message = "";

        $db = $this->StudentRegisterClasses->getConnection();
        $db->begin();

        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();

                if (!isset($payload['cart']) || empty($payload['cart'])) {  // [{"kid_id": 1, "cidc_class_id": 200}, { ... }]
                    $message = __('missing_parameter') . ' cart'; 
                
                } else {
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }
                    $this->Api->set_language($this->language);  
                    $result = $this->StudentRegisterClasses->is_validate_cart($payload['cart'], $this->language);  
               
                    $status = $result['status'];
                    $message = $result['message'];  
                    $params = $result['params'];
                }
            }
        } catch (\Exception $ex) {
            $db->rollback();
            $this->response = $this->response->withStatus(501);
            $message = $ex->getMessage();
            $status = 501;
        }

        api_result:
        $this->Api->set_result($status, $message, $params);
        $this->Api->output($this);
    } 

    // test
    public function getOrder()
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
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }
                    $this->Api->set_language($this->language);  

                    $obj_Orders = $this->loadModel('Orders');
                    $cidc_parent_id = $this->user->cidc_parents[0]->id; 
 
                    $result = $obj_Orders->get_order($payload['order_id'], $cidc_parent_id, $this->language);

                    if (empty($result)) {  
                        $message = "INVALID_ORDER"; 

                    } else {

                        $status = 200;
                        $message = "";
                        $params = $result; 
                    }
                }
            } else { 
                $message = "Wrong Method";
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

                    $result = $this->StudentRegisterClasses->set_order_v2($payload); 

                    if ($result['status'] == 200) {
                         
                        // show detail order
                        $params = $result['params']; 
                     
                        $obj_Order = $this->loadModel('Orders');
                        $cidc_parent_id = $this->user->cidc_parents[0]->id;
                        $details_order = $obj_Order->get_order($params['order_id'], $cidc_parent_id, $this->language);
                        $params = $details_order;  

                        if (empty($details_order)) {
                            $message = 'INVALID_ORDER';
                        
                        } else {

                            $t[] = [
                                'kid_id' => $payload['kid_id'],
                                'cidc_class_id' => $payload['cidc_class_id'],
                            ];
                            $payload['cart'] = json_encode($t);
    
                            $is_send_message = TableRegistry::get('SystemMessages')->send_system_message($payload['cart'], $this->user->id, $this->language);
                            $db->commit();
                            if ($is_send_message) {
                                $message = 'REGISTER_CLASS_SUCCESS';
                            } else {
                                $message = 'REGISTER_CLASS_SUCCESS_WITHOUT_MESSAGE';
                            } 
                        } 
                    
                    }  else {
                        $db->rollback();
                    }
               
                    $status = $result['status'];
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

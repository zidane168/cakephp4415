<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AppController;
use App\MyHelper\MyHelper;
use Cake\Event\EventInterface;

class StudentAttendedClassesController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $authentications = [
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "setOrder",
                'user_role_id' =>  MyHelper::STAFF,
            ],
        ];
        $this->requireAuthenticate($authentications);
    }

    // public function setOrder()
    // {
    //     $this->Api->init();
    //     $params = (object)array();
    //     $status = 500;
    //     $message = "";
    //     try {
    //         if ($this->request->is('post')) {
    //             $payload = $this->request->getData();

    //             if (!isset($payload['kid_id']) || empty($payload['kid_id'])) {
    //                 $message = __('missing_parameter') . ' kid_id';
    //             } elseif (!isset($payload['cidc_class_id']) || empty($payload['cidc_class_id'])) {
    //                 $message = __('missing_parameter') . ' cidc_class_id';
    //             } else {
    //                 // set language/ authorization
    //                 $payload_result = $this->get_payload_result();
    //                 if ($payload_result['status'] != 200) {
    //                     $status = $payload_result['status'];
    //                     $this->response = $this->response->withStatus($this->status);
    //                     $message = $this->message;
    //                     goto api_result;
    //                 }
    //                 $this->Api->set_language($this->language);
    //                 // set language/ authorization

    //                 $result = $this->StudentRegisterClasses->set_order($payload);
    //                 $status = $result['status'];
    //                 if ($status == 200) {
    //                     $message = 'REGISTER_CLASS_SUCCESS';
    //                 } else {
    //                     $message = $result['message'];
    //                 }
    //             }
    //         }
    //     } catch (\Exception $ex) {
    //         $this->response = $this->response->withStatus(501);
    //         $message = $ex->getMessage();
    //         $status = 501;
    //     }

    //     api_result:
    //     $this->Api->set_result($status, $message, $params);
    //     $this->Api->output($this);
    // }

    public function getStatus()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('get')) {
                $this->Api->set_language($this->language);
                $status = 200;
                $message = 'RETRIEVE_DATA_SUCCESSFULLY';
                $params =  [
                    [
                        'status' => MyHelper::TBD,
                        'name'   => __('TBD')
                    ],

                    [
                        'status' => MyHelper::ATTENDED,
                        'name'   => __('ATTENDED')
                    ],

                    [
                        'status' => MyHelper::ABSENT,
                        'name'   => __('ABSENT')
                    ],

                    [
                        'status' => MyHelper::ON_LEAVE,
                        'name'   => __('ON_LEAVE')
                    ],

                ];
                // set language/ authorization
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

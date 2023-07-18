<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AppController;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use App\MyHelper\MyHelper;
use Cake\Core\Configure;

/**
 * CidcParents Controller
 *
 * @property \App\Model\Table\CidcParentsTable $CidcParents
 * @method \App\Model\Entity\CidcParent[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CidcClassesController extends AppController
{

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $authentications = [
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "getClassesByStaff",
                'user_role_id' =>  MyHelper::STAFF,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "updateStatusAttended",
                'user_role_id' =>  MyHelper::STAFF,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "getClassesRegister",
                'user_role_id' =>  MyHelper::PARENT,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "getAlbumTitle",
                'user_role_id' =>  MyHelper::PARENT,
            ],

        ];
        $this->requireAuthenticate($authentications);
    }

    public function getAll()
    {
        $this->Api->init();
        $params = [];
        $status = 500;
        $message = "";

        try {
            if ($this->request->is('get')) {

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

                $classes = $this->CidcClasses->get_all($this->language);
                $status     = 200;
                $message    = $message;
                $params     = $classes;
            } else {
                $message        = __('invalid_method');
                $status         = 999;
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

    public function dataselectdate()
    {
        if ($this->request->is('get')) {

            $this->Api->init();
            $data = $this->request->getQuery();
            $message = "";
            $status = false;
            $params = array();
            $this->Api->set_language($data['language']);

            $message = __('retrieve_data_successfully');
            $status  = true;
            $obj_StudentAttendedClasses = TableRegistry::get('StudentAttendedClasses');
            $params  = $obj_StudentAttendedClasses->get_dates_by_cidc_class_id($data['cidc_class_id']);

            $this->Api->set_result($status, $message, $params);
        }
        $this->Api->output($this);
    }


    public function getClassesByClassTypesv1()
    {
        $this->Api->init();
        $params = [];
        $status = 500;
        $message = "";

        try {
            if ($this->request->is('get')) {

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

                $payload = $this->request->getQuery();
                $classes = $this->CidcClasses->get_classes_by_class_types($this->language, $payload);

                $message    = 'RETRIEVE_DATA_SUCCESSFULLY';
                $params     = $classes;
            } else {
                $message        = __('invalid_method');
                $status         = 999;
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

    public function getClassesByClassTypes()
    {
        $this->Api->init();
        $params = [];
        $status = 500;
        $message = "";

        try {
            if ($this->request->is('get')) {
                $payload = $this->request->getQuery();
                if (!isset($payload['page']) || empty($payload['page'])) {
                    $message = __('missing_parameter') . ' page';
                    goto api_result;
                } elseif (!isset($payload['limit']) || empty($payload['limit'])) {
                    $message = __('missing_parameter') . ' limit';
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

                $classes = $this->CidcClasses->get_classes_by_class_types($this->language, $payload);

                $message    = 'RETRIEVE_DATA_SUCCESSFULLY';
                $params     = $classes;
            } else {
                $message        = __('invalid_method');
                $status         = 999;
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

    public function getClassesByStaff()
    {
        $this->Api->init();
        $status = 200;
        $message = "RETRIEVE_DATA_SUCCESSFULLY";
        $params = [];
        try {
            if ($this->request->is('get')) {
                $payload = $this->request->getQuery();
                if (!isset($payload['page']) || empty($payload['page'])) {
                    $message = __('missing_parameter') . ' page';
                } elseif (!isset($payload['limit']) || empty($payload['limit'])) {
                    $message = __('missing_parameter') . ' limit';
                } else {

                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto return_result;
                    }

                    $this->Api->set_language($this->language);
                    $obj_Staff  = TableRegistry::get("Staffs");
                    $center_id = $obj_Staff->get_center_id_by_user($this->user->id);
                    if (!$center_id) {
                        goto return_result;
                    }

                    $params = $this->CidcClasses->get_list_by_center_id($center_id, $payload, $this->language); 
                    $this->Api->set_language($this->language);
                }
            }
        } catch (\Exception $ex) {
            $this->response = $this->response->withStatus(501);
            $message = $ex->getMessage();
            $status = 501;
        }

        return_result:
        $this->Api->set_result($status, $message, $params);
        $this->Api->output($this);
    }

    public function updateStatusAttended()
    {
        $this->Api->init();
        $status = 999;
        $message = "";
        $params = [];
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();
                if (!isset($payload['class_id']) || empty($payload['class_id'])) {
                    $message = __('missing_parameter') . ' class_id';
                } elseif (!isset($payload['attended']) || empty($payload['attended'])) {
                    $message = __('missing_parameter') . ' attended';
                } else {
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }
                    $this->Api->set_language($this->language);
                    $obj_Staff  = TableRegistry::get("Staffs");
                    $center_id = $obj_Staff->get_center_id_by_user($this->user->id);
                    $class = $this->CidcClasses->find('all', [
                        'conditions' => [
                            'CidcClasses.enabled'       => true,
                            'CidcClasses.center_id'     => $center_id,
                            'CidcClasses.id'            => $payload['class_id'],
                            'CidcClasses.status'        => $this->CidcClasses->PUBLISHED
                        ]
                    ])->first();
                    if (!$class) {
                        $message = "NOT_FOUND_CLASS";
                        goto api_result;
                    }
                    $obj_StudentAttendedClasses = TableRegistry::get('StudentAttendedClasses');
                    if (!$obj_StudentAttendedClasses->update_status_attended($class->id, json_decode($payload['attended']))) {
                        $message = 'DATA_IS_NOT_SAVED';
                        goto api_result;
                    }
                    $status = 200;
                    $message = 'DATA_IS_SAVED';
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

    public function getDetailClassById()
    {
        $this->Api->init();
        $params = [];
        $status = 500;
        $message = "";

        try {
            if ($this->request->is('get')) {
                $payload = $this->request->getQuery();
                if (!isset($payload['id']) || empty($payload['id'])) {
                    $message = __('missing_parameter') . ' id';
                } else {

                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }
                    $this->Api->set_language($this->language);

                    $classes = $this->CidcClasses->get_detail_class_by_id($payload['id'], $this->language);

                    $message    = 'RETRIEVE_DATA_SUCCESSFULLY';
                    $params     = $classes;
                }
            } else {
                $message        = __('invalid_method');
                $status         = 999;
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

    public function getDetailClassByIdUI()
    {
        if ($this->request->is('post')) {

            $this->Api->init();
            $data = $this->request->getData();

            $message = __('retrieve_data_successfully');
            $status  = true;
            $params  = $this->CidcClasses->get_detail_class_by_id($data['id'], $data['language']);

            $this->Api->set_result($status, $message, $params);
        }
        $this->Api->output($this);
    }

    public function getKidsNoRegisterClassUI()
    {
        if ($this->request->is('post')) {

            $this->Api->init();
            $data = $this->request->getData();

            $message = __('retrieve_data_successfully');
            $status  = true;
            $params = null;
            $params  = $this->loadModel('Kids')->get_kids_no_register_class($data['id'], $data['language']);

            $this->Api->set_result($status, $message, $params);
        }
        $this->Api->output($this);
    }

    public function getKidsRegisterClassUI()
    {
        if ($this->request->is('post')) {

            $this->Api->init();
            $data = $this->request->getData();

            $message = __('retrieve_data_successfully');
            $status  = true;
            $params = null;
            $params  = $this->loadModel('Kids')->get_kids_register_class($data['id'], $data['language']);

            $this->Api->set_result($status, $message, $params);
        }
        $this->Api->output($this);
    }

    public function getListDateByClass()
    {
        $this->Api->init();
        $params = [];
        $status = 500;
        $message = "";

        try {
            if ($this->request->is('get')) {
                $payload = $this->request->getQuery();
                if (!isset($payload['cidc_class_id']) || empty($payload['cidc_class_id'])) {
                    $message = __('missing_parameter') . ' cidc_class_id';
                } elseif (!isset($payload['current_cidc_class_id']) || empty($payload['current_cidc_class_id'])) {
                    $message = __('missing_parameter') . ' current_cidc_class_id';
                } else {
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }
                    $this->Api->set_language($this->language);

                    $dates = $this->CidcClasses->get_list_date_by_class($payload['current_cidc_class_id'], $payload['cidc_class_id']);
                    if (!$dates) {
                        $message = 'NOT_FOUND_CLASS';
                        $params = null;
                        goto api_result;
                    }

                    $message    = 'RETRIEVE_DATA_SUCCESSFULLY';
                    $params     = $dates;
                }
            } else {
                $message        = __('invalid_method');
                $status         = 999;
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

    public function getListDateByKidClass()
    {
        $this->Api->init();
        $params = [];
        $status = 500;
        $message = "";

        try {
            if ($this->request->is('get')) {
                $payload = $this->request->getQuery();
                if (!isset($payload['cidc_class_id']) || empty($payload['cidc_class_id'])) {
                    $message = __('missing_parameter') . ' cidc_class_id';
                } elseif (!isset($payload['kid_id']) || empty($payload['kid_id'])) {
                    $message = __('missing_parameter') . ' kid_id';
                } else {
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }
                    $this->Api->set_language($this->language);

                    $dates = $this->CidcClasses->get_list_date_by_kid_class($payload['kid_id'], $payload['cidc_class_id']);
                    if (!$dates) {
                        $message = 'NOT_FOUND_CLASS';
                        $params = null;
                        goto api_result;
                    }

                    $message    = 'RETRIEVE_DATA_SUCCESSFULLY';
                    $params     = $dates;
                }
            } else {
                $message        = __('invalid_method');
                $status         = 999;
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

    public function getClassesByKidIdUI()
    {
        if ($this->request->is('get')) {

            $this->Api->init();
            $data = $this->request->getQuery();
            $message = "";
            $status = false;
            $params = array();
            $this->Api->set_language($data['language']);

            $message = __('retrieve_data_successfully');
            $status  = true;

            $params  = $this->CidcClasses->get_list_by_kid_id($data['language'], $data['kid_id']);

            $this->Api->set_result($status, $message, $params);
        }
        $this->Api->output($this);
    }

    public function getClassesByFromClasssIdUI()
    {
        if ($this->request->is('get')) {

            $this->Api->init();
            $data = $this->request->getQuery();
            $message = "";
            $status = false;
            $params = array();
            $this->Api->set_language($data['language']);

            $class = $this->CidcClasses->find('all', [
                'conditions' => [
                    'CidcClasses.id' => $data['from_cidc_class_id'],
                    'CidcClasses.enabled' => true
                ]
            ])->first();
            if (!$class) {
                goto api_result;
            }
            $conditions = [
                'CidcClasses.program_id' => $class->program_id,
                'CidcClasses.enabled' => true,
                'CidcClasses.status' => $this->CidcClasses->PUBLISHED
            ];
            if ($class->class_type_id == MyHelper::NONCIRCULAR) {
                $conditions['CidcClasses.id !='] = $data['from_cidc_class_id'];
            }
            $params  = $this->CidcClasses->get_list($data['language'], $conditions);
            $status = true;
            $message = __('retrieve_data_successfully');
            api_result:
            $this->Api->set_result($status, $message, $params);
        }
        $this->Api->output($this);
    }

    // get all class same program
    public function getClassesByFromClassId()
    {
        $this->Api->init();
        $params = [];
        $status = 500;
        $message = "";

        try {
            if ($this->request->is('get')) {
                $payload = $this->request->getQuery();
                if (!isset($payload['from_cidc_class_id']) || empty($payload['from_cidc_class_id'])) {
                    $message = __('missing_parameter') . ' from_cidc_class_id';
                } elseif (!isset($payload['page']) || empty($payload['page'])) {
                    $message = __('missing_parameter') . ' page';
                } elseif (!isset($payload['limit']) || empty($payload['limit'])) {
                    $message = __('missing_parameter') . ' limit';
                } else {
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }
                    $this->Api->set_language($this->language);

                    $class = $this->CidcClasses->find('all', [
                        'conditions' => [
                            'CidcClasses.id' => $payload['from_cidc_class_id'],
                            'CidcClasses.enabled' => true
                        ]
                    ])->first();
                    if (!$class) {
                        $message = 'NOT_FOUND_CLASS';
                        $params = [];
                        goto api_result;
                    }
                    $conditions = [
                        'CidcClasses.enabled' => true,
                        'CidcClasses.program_id' => $class->program_id,
                        'CidcClasses.status' => $this->CidcClasses->PUBLISHED
                    ];
                    if ($class->class_type_id == MyHelper::NONCIRCULAR) {
                        $conditions['CidcClasses.id !='] = $payload['from_cidc_class_id'];
                    }
                    $classes = $this->CidcClasses->get_classes_by_from_class($this->language, $payload, $conditions);

                    $message    = 'RETRIEVE_DATA_SUCCESSFULLY';
                    $params     = $classes;
                }
            } else {
                $message        = __('invalid_method');
                $status         = 999;
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

     // get all class same program
    public function webClassesByFromClassId()
    {
        $this->Api->init();
        $params = [];
        $status = 500;
        $message = "";

        try {
            if ($this->request->is('get')) {
                $payload = $this->request->getQuery();
                if (!isset($payload['from_cidc_class_id']) || empty($payload['from_cidc_class_id'])) {
                    $message = __('missing_parameter') . ' from_cidc_class_id';
                } elseif (!isset($payload['page']) || empty($payload['page'])) {
                    $message = __('missing_parameter') . ' page';
                } elseif (!isset($payload['limit']) || empty($payload['limit'])) {
                    $message = __('missing_parameter') . ' limit';
                } else {
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }
                    $this->Api->set_language($this->language);

                    $class = $this->CidcClasses->find('all', [
                        'conditions' => [
                            'CidcClasses.id' => $payload['from_cidc_class_id'],
                            'CidcClasses.enabled' => true
                        ]
                    ])->first();
                    if (!$class) {
                        $message = 'NOT_FOUND_CLASS';
                        $params = [];
                        goto api_result;
                    }
                    $conditions = [
                        'CidcClasses.enabled' => true,
                        'CidcClasses.program_id' => $class->program_id,
                        'CidcClasses.status' => $this->CidcClasses->PUBLISHED
                    ];
                    if ($class->class_type_id == MyHelper::NONCIRCULAR) {
                        $conditions['CidcClasses.id !='] = $payload['from_cidc_class_id'];
                    }
                    $classes = $this->CidcClasses->get_classes_dates_by_from_class($this->language, $payload, $conditions);

                    $message    = 'RETRIEVE_DATA_SUCCESSFULLY';
                    $params     = $classes;
                }
            } else {
                $message        = __('invalid_method');
                $status         = 999;
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

    public function getAttendedByClass()
    {
        $this->Api->init();
        $params = [];
        $status = 500;
        $message = "";

        try {
            if ($this->request->is('get')) {
                $payload = $this->request->getQuery();
                if (!isset($payload['cidc_class_id']) || empty($payload['cidc_class_id'])) {
                    $message = __('missing_parameter') . ' cidc_class_id';
                } elseif (!isset($payload['date']) || empty($payload['date'])) {
                    $message = __('missing_parameter') . ' date';
                } elseif (!isset($payload['status']) || empty($payload['status'])) {
                    $message = __('missing_parameter') . ' status';
                } elseif (!isset($payload['page']) || empty($payload['page'])) {
                    $message = __('missing_parameter') . ' page';
                } elseif (!isset($payload['limit']) || empty($payload['limit'])) {
                    $message = __('missing_parameter') . ' limit';
                } else {
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }
                    $this->Api->set_language($this->language);

                    $class = $this->CidcClasses->find('all', [
                        'conditions' => [
                            'CidcClasses.id' => $payload['cidc_class_id'],
                            'CidcClasses.enabled' => true
                        ]
                    ])->first();
                    if (!$class) {
                        $message = 'NOT_FOUND_CLASS';
                        $params = [];
                        goto api_result;
                    }
                    $classes = $this->CidcClasses->get_attended_by_class($this->language, $payload);

                    $message    = 'RETRIEVE_DATA_SUCCESSFULLY';
                    $params     = $classes;
                }
            } else {
                $message        = __('invalid_method');
                $status         = 999;
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

    public function getClassesRegister()
    {
        $this->Api->init();
        $params = [];
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('get')) {
                $payload = $this->request->getQuery();
                if (!isset($payload['kid_id']) || empty($payload['kid_id'])) {
                    $message = __('missing_parameter') . ' kid_id';
                } elseif (!isset($payload['page']) || empty($payload['page'])) {
                    $message = __('missing_parameter') . ' page';
                } elseif (!isset($payload['limit']) || empty($payload['limit'])) {
                    $message = __('missing_parameter') . ' limit';
                } else {
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }
                    $this->Api->set_language($this->language);

                    $obj_Kids = TableRegistry::get('Kids');
                    $kid_ids = $obj_Kids->get_kid_ids([
                        'Users.id' => $this->user->id,
                        'Kids.enabled' => true
                    ]);
                    if (!in_array($payload['kid_id'], $kid_ids)) {
                        $message = 'NOT_FOUND_KID';
                        $params = [];
                        goto api_result;
                    }
                    $classes = $this->CidcClasses->get_classes_register_by_kid($this->language, $payload['kid_id'], $payload);

                    $message    = 'RETRIEVE_DATA_SUCCESSFULLY';
                    $params     = $classes;
                }
            } else {
                $message        = __('invalid_method');
                $status         = 999;
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

    public function getAlbumTitle()
    {
        $this->Api->init();
        $params = [];
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('get')) {
                $payload = $this->request->getQuery();
                if (!isset($payload['page']) || empty($payload['page'])) {
                    $message = __('missing_parameter') . ' page';
                } elseif (!isset($payload['limit']) || empty($payload['limit'])) {
                    $message = __('missing_parameter') . ' limit';
                } else {
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }
                    $this->Api->set_language($this->language);

                    $obj_Kids = TableRegistry::get('Kids');
                    $kid_ids = $obj_Kids->get_kid_ids([
                        'Users.id' => $this->user->id,
                        'Kids.enabled' => true
                    ]);
                    if (!$kid_ids) {
                        $message = 'RETRIEVE_DATA_SUCCESSFULLY';
                        $params = [
                            'items' => [],
                            'count' => 0
                        ];
                        goto api_result;
                    }
                    $class_ids = $this->CidcClasses->get_class_ids_by_kid_ids($kid_ids);
                    if (!$class_ids) {
                        $message = 'RETRIEVE_DATA_SUCCESSFULLY';
                        $params = [
                            'items' => [],
                            'count' => 0
                        ];
                        goto api_result;
                    }
                    $params = $this->CidcClasses->get_album_by_class_ids($class_ids, $payload);

                    $message    = 'RETRIEVE_DATA_SUCCESSFULLY';
                    // $params     = $class_ids;
                }
            } else {
                $message        = __('invalid_method');
                $status         = 999;
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

    public function getImageTitle()
    {
        $this->Api->init();
        $params = [];
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('get')) {
                $payload = $this->request->getQuery();
                if (!isset($payload['cidc_class_id']) || empty($payload['cidc_class_id'])) {
                    $message = __('missing_parameter') . ' cidc_class_id';
                } else {
                    $payload_result = $this->get_payload_result();
                    $this->Api->set_language($this->language);

                    $class = $this->CidcClasses->find('all', [
                        'conditions' => [
                            'CidcClasses.id' => $payload['cidc_class_id'],
                            'CidcClasses.enabled' => true
                        ],
                        'contain' => [
                            'Albums'
                        ]
                    ])->first();
                    if (!$class) {
                        $message = 'NOT_FOUND_CLASS';
                        $params = null;
                        goto api_result;
                    }
                    $url = MyHelper::getUrl();
                    $params = [
                        'id' => $class->id,
                        'name' => $class->name,
                        'code' => $class->code,
                        'image' => isset($class->albums[0]->path) ? $url . $class->albums[0]->path : null
                    ];

                    $message    = 'RETRIEVE_DATA_SUCCESSFULLY';
                    // $params     = $class_ids;
                }
            } else {
                $message        = __('invalid_method');
                $status         = 999;
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

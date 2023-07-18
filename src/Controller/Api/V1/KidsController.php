<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AppController;
use Cake\Event\EventInterface;
use App\MyHelper\MyHelper;
use Cake\ORM\TableRegistry;

class KidsController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $authentications = [
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "getDetail",
                'user_role_id' =>  MyHelper::PARENT,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "edit",
                'user_role_id' =>  MyHelper::PARENT,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "remove",
                'user_role_id' =>  MyHelper::PARENT,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "add",
                'user_role_id' =>  MyHelper::PARENT,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "getListPagination",
                'user_role_id' =>  MyHelper::PARENT,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "staffGetListPagination",
                'user_role_id' =>  MyHelper::STAFF,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "staffGetDetail",
                'user_role_id' =>  MyHelper::STAFF,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "staffGetListKidsAttended",
                'user_role_id' =>  MyHelper::STAFF,
            ],
        ];
        $this->requireAuthenticate($authentications);
    }

    public function getDetail()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 500;
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

                    $kid_ids = $this->Kids->get_kid_ids(
                        [
                            'Kids.enabled' => true,
                            'Users.id'     => $this->user->id
                        ]
                    );
                    if (!in_array($payload['id'], $kid_ids)) {
                        $message = 'NOT_FOUND_KID';
                        goto api_result;
                    }
                    $params = $this->Kids->get_detail($this->user->id, $this->language, $payload['id']);
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

    public function edit()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();

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
                    if (isset($payload['number_of_siblings']) && !empty($payload['number_of_siblings']) && (int)$payload['number_of_siblings'] < 0) {
                        $payload['number_of_siblings'] = 0;
                    }
                    $db = $this->Kids->getConnection();
                    $db->begin();
                    $result = $this->Kids->edit($this->user->id, $payload, $this->language);
                    $status = $result['status'];
                    $message = $result['message'];
                    if ($status != 200) {
                        goto api_result;
                    }
                    if (isset($payload['avatar']) && !empty($payload['avatar'])) {
                        $avatar_data = [];

                        $relative_path = 'uploads' . DS . 'KidImages';
                        $file_name_suffix = "image";
                        $key = 1;
                        $uploaded = $this->Common->upload_images($payload['avatar'], $relative_path, $file_name_suffix, $key);
                        if ($uploaded) {
                            $temp = array(
                                'path'              => $uploaded['path'],
                                'name'              => $uploaded['re_name'],
                                'width'             => $uploaded['width'],
                                'height'            => $uploaded['height'],
                                'size'              => $uploaded['size'],
                                'kid_id'            => $payload['id'],
                            );
                            $avatar_data[] = $temp;
                        }

                        if (isset($avatar_data) && !empty($avatar_data)) {
                            $data_images = $this->KidImages->find('all', [
                                'fields' => ['KidImages.id'],
                                'conditions' => [
                                    'KidImages.kid_id' => $payload['id']
                                ]
                            ]);
                            $ids = [];
                            foreach ($data_images as $img) {
                                $ids[]  = $img->id;
                            }
                            // delete physical file + db
                            if ($ids) {
                                $this->Kids->remove_uploaded_image('KidImages', $ids);
                            }
                            // save new file + db;
                            $orm_Image = $this->Kids->KidImages->newEntities($avatar_data);
                            if (!$this->Kids->KidImages->saveMany($orm_Image)) {
                                $db->rollback();
                                $status = 999;
                                $message = __('data_is_not_saved') . ' KidImages';
                                goto api_result;
                            }
                        }
                    }
                    $db->commit();
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
        $status = 500;
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

                    $result = $this->Kids->remove($this->user->id, $payload['id']);
                    $status = $result['status'];
                    $message = $result['message'];
                    $params = $result['params'];
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

    public function add()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();

                if (!isset($payload['gender'])) {
                    $message = __('missing_parameter') . ' gender';
                } elseif (!isset($payload['dob']) || empty($payload['dob'])) {
                    $message = __('missing_parameter') . ' dob';
                } elseif (!isset($payload['relationship_id']) || empty($payload['relationship_id'])) {
                    $message = __('missing_parameter') . ' relationship_id';
                }
                // elseif (!isset($payload['number_of_siblings']) || empty($payload['number_of_siblings'])) {
                //     $message = __('missing_parameter') . ' number_of_siblings';
                // } 
                // elseif (!isset($payload['special_attention_needed']) || empty($payload['special_attention_needed'])) {
                //     $message = __('missing_parameter') . ' special_attention_needed';
                // } 
                // elseif (!isset($payload['caretaker']) || empty($payload['caretaker'])) {
                //     $message = __('missing_parameter') . ' caretaker';
                // } 
                elseif (!isset($payload['zh_HK_name']) || empty($payload['zh_HK_name'])) {
                    $message = __('missing_parameter') . ' zh_HK_name'; 
                } elseif (!isset($payload['en_US_name']) || empty($payload['en_US_name'])) {
                    $message = __('missing_parameter') . ' en_US_name'; 
                } elseif (!isset($payload['emergency_contacts']) || empty($payload['emergency_contacts'])) {
                    $message = __('missing_parameter') . ' emergency_contacts';
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
                    $kid_ids = $this->Kids->get_kid_ids(
                        [
                            'Kids.enabled' => true,
                            'Users.id' => $this->user->id
                        ]
                    );
                    if (count($kid_ids) >= 8) {
                        $message = 'OVER_KID';
                        $params = null;
                        goto api_result;
                    }

                    $db = $this->Kids->getConnection();
                    $db->begin();
                    $result = $this->Kids->add_kid_api($this->user->id, $payload);
                    $status = $result['status'];
                    $message = $result['message'];
                    $params = $result['params'];
                    if ($status != 200) {
                        goto api_result;
                    }
                    if (isset($payload['number_of_siblings']) && !empty($payload['number_of_siblings']) && (int)$payload['number_of_siblings'] < 0) {
                        $payload['number_of_siblings'] = 0;
                    }

                    if (!isset($payload['number_of_siblings']) || empty($payload['number_of_siblings'])) {
                        $payload['number_of_siblings'] = 0;
                    }
                    if (isset($payload['avatar']) && !empty($payload['avatar'])) {
                        $avatar_data = [];

                        $relative_path = 'uploads' . DS . 'KidImages';
                        $file_name_suffix = "image";
                        $key = 1;
                        $uploaded = $this->Common->upload_images($payload['avatar'], $relative_path, $file_name_suffix, $key);
                        if ($uploaded) {
                            $temp = array(
                                'path'              => $uploaded['path'],
                                'name'              => $uploaded['re_name'],
                                'width'             => $uploaded['width'],
                                'height'            => $uploaded['height'],
                                'size'              => $uploaded['size'],
                                'kid_id'            => $params,
                            );
                            $avatar_data[] = $temp;
                        }
                        if (isset($avatar_data) && !empty($avatar_data)) {
                            // save new file + db;
                            $orm_Image = $this->Kids->KidImages->newEntities($avatar_data);
                            if (!$this->Kids->KidImages->saveMany($orm_Image)) {
                                $db->rollback();
                                $status = 999;
                                $message = __('data_is_not_saved') . ' KidImages';
                                goto api_result;
                            }
                        }
                    }
                    $db->commit();
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

    public function getListPagination()
    {
        $this->Api->init();
        $params = (object)array();
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

                    $params = $this->Kids->get_list_by_token($this->user->id, $this->language, $payload);
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

    public function staffGetListPagination()
    {
        $this->Api->init();
        $params = (object)array();
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
                    $obj_Staff  = TableRegistry::get("Staffs");
                    $center_id = $obj_Staff->get_center_id_by_user($this->user->id);

                    $params = $this->Kids->get_list_kid_by_center_id($this->language, $payload, $center_id);
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

    public function staffGetDetail()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 500;
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
                    // debug($payload);
                    // exit;
                    $obj_Staff  = TableRegistry::get("Staffs");
                    $center_id = $obj_Staff->get_center_id_by_user($this->user->id);

                    $kid_ids = $this->Kids->get_kid_ids_by_center_id($center_id);
                    if (!in_array($payload['id'], $kid_ids)) {
                        $message = 'NOT_FOUND_KID';
                        $params = null;
                        goto api_result;
                    }
                    $params = $this->Kids->staff_get_detail($this->language, $payload['id']);
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

    public function staffGetListKidsAttended()
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
                } elseif (!isset($payload['date']) || empty($payload['date'])) {
                    $message = __('missing_parameter') . ' date';
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
                    // debug($payload);
                    // exit;
                    $obj_Staff  = TableRegistry::get("Staffs");
                    $center_id = $obj_Staff->get_center_id_by_user($this->user->id);
                    $obj_Class = TableRegistry::get('CidcClasses');
                    $class = $obj_Class->find('all', [
                        'conditions' => [
                            'CidcClasses.id' => $payload['cidc_class_id'],
                            'CidcClasses.center_id' => $center_id
                        ]
                    ])->first();
                    if (!$class) {
                        $message = "CLASS_NOT_VALID";
                        goto api_result;
                    }

                    $params = $this->Kids->staff_get_kids_attended($payload['cidc_class_id'], $payload['date'], $this->language);
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
}

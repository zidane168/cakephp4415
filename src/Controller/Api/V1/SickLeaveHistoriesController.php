<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AppController;
use App\MyHelper\MyHelper;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;

class SickLeaveHistoriesController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $authentications = [
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "submit",
                'user_role_id' =>  MyHelper::PARENT,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "webSubmit",
                'user_role_id' =>  MyHelper::PARENT,
            ],
        ];
        $this->requireAuthenticate($authentications);
    }

    public function submit()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();
                if (!isset($payload['cidc_class_id']) || empty($payload['cidc_class_id'])) {
                    $message = __('missing_parameter') . ' cidc_class_id';
                } elseif (!isset($payload['date']) || empty($payload['date'])) {
                    $message = __('missing_parameter') . ' date';
                } elseif (!isset($payload['kid_id']) || empty($payload['kid_id'])) {
                    $message = __('missing_parameter') . ' kid_id';
                } elseif (!isset($payload['time']) || empty($payload['time'])) {
                    $message = __('missing_parameter') . ' time';
                } elseif (!isset($payload['kid_id']) || empty($payload['kid_id'])) {
                    $message = __('missing_parameter') . ' kid_id';
                } elseif (!isset($payload['reason']) || empty($payload['reason'])) {
                    $message = __('missing_parameter') . ' reason';
                } else {
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }

                    $obj_Classes = TableRegistry::get('CidcClasses');
                    // check from class
                    $class = $obj_Classes->find('all', [
                        'conditions' => [
                            'CidcClasses.id' => $payload['cidc_class_id'],
                            'CidcClasses.enabled' => true,
                            'CidcClasses.status' => $obj_Classes->PUBLISHED

                        ]
                    ])->first();
                    if (!$class) {
                        $status = 999;
                        $message = 'NOT_FOUND_CLASS';
                        goto api_result;
                    }

                    // check kid
                    $obj_Kids  = TableRegistry::get('Kids');
                    $kid_ids = $obj_Kids->get_kid_ids(
                        [
                            'Kids.enabled' => true,
                            'Kids.id' => $payload['kid_id'],
                            'Users.id' => $this->user->id
                        ]
                    );
                    if (!in_array($payload['kid_id'], $kid_ids)) {
                        $status = 999;
                        $message = "NOT_FOUND_KID";
                        goto api_result;
                    }

                    // check student register class
                    $obj_StudentRegisterClasses = TableRegistry::get('StudentRegisterClasses');
                    $kid_registered_class = $obj_StudentRegisterClasses->find('all', [
                        'conditions' => [
                            'StudentRegisterClasses.kid_id' => $payload['kid_id'],
                            'StudentRegisterClasses.cidc_class_id' => $payload['cidc_class_id'],
                            'StudentRegisterClasses.status' => MyHelper::PAID
                        ]
                    ])->first();
                    if (!$kid_registered_class) {
                        $message = 'KID_NOT_REGISTER_CLASS';
                        $status = 999;
                        goto api_result;
                    }

                    $dates_class = $obj_Classes->get_list_date_by_class($payload['cidc_class_id'], $payload['cidc_class_id']);
                    if (!$dates_class) {
                        $message = 'NOT_FOUND_FCLASS';
                        $status  = 999;
                        goto api_result;
                    }

                    if (!in_array($payload['date'], $dates_class['dates'])) {
                        $message = 'DATE_INVALID';
                        $status = 999;
                        goto api_result;
                    }

                    $exist_item = $this->SickLeaveHistories->find(
                        'all',
                        [
                            'conditions' => [
                                'SickLeaveHistories.kid_id'                => $payload['kid_id'],
                                'SickLeaveHistories.cidc_class_id'         => $payload['cidc_class_id'],
                                'SickLeaveHistories.date'                  => $payload['date'],
                            ],
                        ]
                    )->first();
                    if ($exist_item) {
                        $message = 'DUPLICATE_SICK_LEAVE';
                        $status = 999;
                        goto api_result;
                    }

                    $db = $this->SickLeaveHistories->getConnection();
                    $db->begin();
                    // $entity  = $this->SickLeaveHistories->newEmptyEntity();
                    $entity  = $this->SickLeaveHistories->newEntity($payload);
                    // pr($entity);
                    // exit;
                    if ($model = $this->SickLeaveHistories->save($entity)) {
                        if (isset($payload['files']) && !empty($payload['files'])) {
                            $files = $payload['files'];

                            $relative_path = 'uploads' . DS . 'SickLeaveHistoryFiles';
                            $file_name_suffix = "file";
                            $temp = [];
                            foreach ($files as $key => $file) {


                                if ($file->getSize() == 0) {
                                    continue;
                                }

                                $uploaded = $this->Common->upload_files($file, $relative_path, $file_name_suffix, $key);

                                if ($uploaded) {
                                    $temp[] = array(
                                        'sick_leave_history_id'         => $model->id,
                                        'file_name'         => $uploaded['ori_name'],
                                        'path'              => $uploaded['path'],
                                        'size'              => $file->getSize(),
                                        'ext'               => $uploaded['ext'],
                                    );
                                }
                            }


                            $file_entities = $this->SickLeaveHistories->SickLeaveHistoryFiles->newEntities($temp);

                            if (!empty($file_entities)) {
                                if (!$this->SickLeaveHistories->SickLeaveHistoryFiles->saveMany($file_entities)) {
                                    $db->rollback();
                                    $message = 'DATA_FILE_NOT_SAVED';
                                    goto api_result;
                                }
                            }
                        }
                        $db->commit();
                        $message = 'DATA_IS_SAVED';
                        goto api_result;
                    } else {
                        $db->rollback();
                        $message  = 'DATA_NOT_SAVED';
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

    public function webSubmit()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();
                if (!isset($payload['cidc_class_id']) || empty($payload['cidc_class_id'])) {
                    $message = __('missing_parameter') . ' cidc_class_id';
                } elseif (!isset($payload['date']) || empty($payload['date'])) {
                    $message = __('missing_parameter') . ' date';
                } elseif (!isset($payload['kid_id']) || empty($payload['kid_id'])) {
                    $message = __('missing_parameter') . ' kid_id';
                } elseif (!isset($payload['kid_id']) || empty($payload['kid_id'])) {
                    $message = __('missing_parameter') . ' kid_id';
                } elseif (!isset($payload['reason']) || empty($payload['reason'])) {
                    $message = __('missing_parameter') . ' reason';
                } else {
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }

                    $obj_Classes = TableRegistry::get('CidcClasses');
                    // check from class
                    $class = $obj_Classes->find('all', [
                        'conditions' => [
                            'CidcClasses.id' => $payload['cidc_class_id'],
                            'CidcClasses.enabled' => true,
                            'CidcClasses.status' => $obj_Classes->PUBLISHED

                        ]
                    ])->first();
                    if (!$class) {
                        $status = 999;
                        $message = 'NOT_FOUND_CLASS';
                        goto api_result;
                    }

                    // check kid
                    $obj_Kids  = TableRegistry::get('Kids');
                    $kid_ids = $obj_Kids->get_kid_ids(
                        [
                            'Kids.enabled' => true,
                            'Kids.id' => $payload['kid_id'],
                            'Users.id' => $this->user->id
                        ]
                    );
                    if (!in_array($payload['kid_id'], $kid_ids)) {
                        $status = 999;
                        $message = "NOT_FOUND_KID";
                        goto api_result;
                    }

                    // check student register class
                    $obj_StudentRegisterClasses = TableRegistry::get('StudentRegisterClasses');
                    $kid_registered_class = $obj_StudentRegisterClasses->find('all', [
                        'conditions' => [
                            'StudentRegisterClasses.kid_id' => $payload['kid_id'],
                            'StudentRegisterClasses.cidc_class_id' => $payload['cidc_class_id'],
                            'StudentRegisterClasses.status' => MyHelper::PAID
                        ]
                    ])->first();
                    if (!$kid_registered_class) {
                        $message = 'KID_NOT_REGISTER_CLASS';
                        $status = 999;
                        goto api_result;
                    }

                    $dates_class = $obj_Classes->get_list_date_by_class($payload['cidc_class_id'], $payload['cidc_class_id']);
                    if (!$dates_class) {
                        $message = 'NOT_FOUND_FCLASS';
                        $status  = 999;
                        goto api_result;
                    }

                    if (!in_array($payload['date'], $dates_class['dates'])) {
                        $message = 'DATE_INVALID';
                        $status = 999;
                        goto api_result;
                    }

                    $exist_item = $this->SickLeaveHistories->find(
                        'all',
                        [
                            'conditions' => [
                                'SickLeaveHistories.kid_id'                => $payload['kid_id'],
                                'SickLeaveHistories.cidc_class_id'         => $payload['cidc_class_id'],
                                'SickLeaveHistories.date'                  => $payload['date'],
                            ],
                        ]
                    )->first();
                    if ($exist_item) {
                        $message = 'DUPLICATE_SICK_LEAVE';
                        $status = 999;
                        goto api_result;
                    }

                    $db = $this->SickLeaveHistories->getConnection();
                    $db->begin();
                    // $entity  = $this->SickLeaveHistories->newEmptyEntity();
                    $payload['time'] = $class->start_time;
                    $entity  = $this->SickLeaveHistories->newEntity($payload);

                    if ($model = $this->SickLeaveHistories->save($entity)) {
                        if (
                            isset($payload['files']) &&
                            !empty($payload['files']) &&
                            count(json_decode($payload['files'])) != 0
                        ) {
                            $files = json_decode($payload['files']);
                            $obj_SickLeave = TableRegistry::get('SickLeaveHistoryFiles');
                            $entities = $obj_SickLeave->find('all', [
                                'conditions' => [
                                    'SickLeaveHistoryFiles.path IN' => $files,
                                    'SickLeaveHistoryFiles.is_official_link' => 0
                                ]
                            ])->toArray();
                            if (!!$entities) {
                                foreach ($entities as &$entity) {
                                    $entity->is_official_link = 1;
                                    $entity->sick_leave_history_id = $model->id;
                                }
                                if (!$obj_SickLeave->saveMany($entities)) {
                                    $db->rollback();
                                    $status = 500;
                                    $message = 'DATA_FILE_IS_NOT_SAVED';
                                    $params = null;
                                    goto api_result;
                                }
                            }
                        }
                        $db->commit();
                        $message = 'DATA_IS_SAVED';
                        goto api_result;
                    } else {
                        $db->rollback();
                        $message  = 'DATA_NOT_SAVED';
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
}

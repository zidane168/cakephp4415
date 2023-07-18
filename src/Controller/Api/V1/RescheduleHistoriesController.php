<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AppController;
use App\MyHelper\MyHelper;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;

class RescheduleHistoriesController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $authentications = [
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "reschedule",
                'user_role_id' =>  MyHelper::PARENT,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "webReschedule",
                'user_role_id' =>  MyHelper::PARENT,
            ],
        ];
        $this->requireAuthenticate($authentications);
    }

    public function reschedule()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();
                if (!isset($payload['from_cidc_class_id']) || empty($payload['from_cidc_class_id'])) {
                    $message = __('missing_parameter') . ' from_cidc_class_id';
                } elseif (!isset($payload['to_cidc_class_id']) || empty($payload['to_cidc_class_id'])) {
                    $message = __('missing_parameter') . ' to_cidc_class_id';
                } elseif (!isset($payload['date_from']) || empty($payload['date_from'])) {
                    $message = __('missing_parameter') . ' date_from';
                } elseif (!isset($payload['date_to']) || empty($payload['date_to'])) {
                    $message = __('missing_parameter') . ' date_to';
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
                    $from_class = $obj_Classes->find('all', [
                        'conditions' => [
                            'CidcClasses.id' => $payload['from_cidc_class_id'],
                            'CidcClasses.enabled' => true,
                            'CidcClasses.status' => $obj_Classes->PUBLISHED

                        ]
                    ])->first();
                    if (!$from_class) {
                        $status = 999;
                        $message = 'NOT_FOUND_FROM_CLASS';
                        goto api_result;
                    }
                    $payload['from_start_time'] = $from_class->start_time->format('H:i');
                    $payload['from_end_time'] = $from_class->end_time->format('H:i');

                    // check published class
                    $to_class = $obj_Classes->find('all', [
                        'conditions' => [
                            'CidcClasses.id' => $payload['to_cidc_class_id'],
                            'CidcClasses.enabled' => true,
                            'CidcClasses.status' => $obj_Classes->PUBLISHED

                        ]
                    ])->first();
                    if (!$to_class) {
                        $status = 999;
                        $message = 'NOT_FOUND_TO_CLASS';
                        goto api_result;
                    }
                    $payload['to_start_time'] = $to_class->start_time->format('H:i');
                    $payload['to_end_time'] = $to_class->end_time->format('H:i');

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
                            'StudentRegisterClasses.cidc_class_id' => $payload['from_cidc_class_id'],
                            'StudentRegisterClasses.status' => MyHelper::PAID
                        ]
                    ])->first();
                    if (!$kid_registered_class) {
                        $message = 'KID_NOT_REGISTER_FROM_CLASS';
                        $status = 999;
                        goto api_result;
                    }

                    $dates_from_class = $obj_Classes->get_list_date_by_class($payload['from_cidc_class_id'], $payload['from_cidc_class_id']);
                    if (!$dates_from_class) {
                        $message = 'NOT_FOUND_FROM_CLASS';
                        $status  = 999;
                        goto api_result;
                    }

                    if (!in_array($payload['date_from'], $dates_from_class['dates'])) {
                        $message = 'DATE_FROM_INVALID';
                        $status = 999;
                        goto api_result;
                    }

                    $dates_to_class = $obj_Classes->get_list_date_by_class($payload['from_cidc_class_id'], $payload['to_cidc_class_id']);
                    if (!$dates_to_class) {
                        $message = 'NOT_FOUND_TO_CLASS';
                        $status  = 999;
                        goto api_result;
                    }
                    if (!in_array($payload['date_to'], $dates_to_class['dates'])) {
                        $message = 'DATE_TO_INVALID';
                        $status = 999;
                        goto api_result;
                    }

                    switch ($from_class->class_type_id) {
                        case MyHelper::CIRCULAR:
                            if ($payload['date_from'] == $payload['date_to']) {
                                $message = 'SAME_DATE_WHEN_CLASS_IS_CIRCULAR';
                                $status = 999;
                                goto api_result;
                            }

                            break;
                        case MyHelper::NONCIRCULAR:
                            if ($payload['from_cidc_class_id'] == $payload['to_cidc_class_id']) {
                                $message = 'SAME_CLASS_WHEN_CLASS_IS_NON_CIRCULAR';
                                $status = 999;
                                goto api_result;
                            }
                            break;
                        default:
                            break;
                    }
                    $exist_item = $this->RescheduleHistories->find(
                        'all',
                        [
                            'conditions' => [
                                'RescheduleHistories.kid_id'                => $payload['kid_id'],
                                'RescheduleHistories.from_cidc_class_id'    => $payload['from_cidc_class_id'],
                                'RescheduleHistories.to_cidc_class_id'      => $payload['to_cidc_class_id'],
                                'RescheduleHistories.date_from'             => $payload['date_from'],
                                'RescheduleHistories.date_to'               => $payload['date_to'],
                            ],
                        ]
                    )->first();
                    if ($exist_item) {
                        $message = 'DUPLICATE_RESCHEDULE';
                        $status = 999;
                        goto api_result;
                    }
                    $db = $this->RescheduleHistories->getConnection();
                    $db->begin();
                    // $entity  = $this->RescheduleHistories->newEmptyEntity();
                    $entity  = $this->RescheduleHistories->newEntity($payload);
                    // pr($entity);
                    // exit;
                    if ($model = $this->RescheduleHistories->save($entity)) {
                        if (isset($payload['files']) && !empty($payload['files'])) {
                            $files = $payload['files'];

                            $relative_path = 'uploads' . DS . 'RescheduleHistoryFiles';
                            $file_name_suffix = "file";
                            $temp = [];
                            foreach ($files as $key => $file) {


                                if ($file->getSize() == 0) {
                                    continue;
                                }

                                $uploaded = $this->Common->upload_files($file, $relative_path, $file_name_suffix, $key);

                                if ($uploaded) {
                                    $temp[] = array(
                                        'reschedule_history_id'         => $model->id,
                                        'file_name'         => $uploaded['ori_name'],
                                        'path'              => $uploaded['path'],
                                        'size'              => $file->getSize(),
                                        'ext'               => $uploaded['ext'],
                                    );
                                }
                            }


                            $file_entities = $this->RescheduleHistories->RescheduleHistoryFiles->newEntities($temp);

                            if (!empty($file_entities)) {
                                if (!$this->RescheduleHistories->RescheduleHistoryFiles->saveMany($file_entities)) {
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

    public function webReschedule()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();
                if (!isset($payload['from_cidc_class_id']) || empty($payload['from_cidc_class_id'])) {
                    $message = __('missing_parameter') . ' from_cidc_class_id';
                } elseif (!isset($payload['to_cidc_class_id']) || empty($payload['to_cidc_class_id'])) {
                    $message = __('missing_parameter') . ' to_cidc_class_id';
                } elseif (!isset($payload['date_from']) || empty($payload['date_from'])) {
                    $message = __('missing_parameter') . ' date_from';
                } elseif (!isset($payload['date_to']) || empty($payload['date_to'])) {
                    $message = __('missing_parameter') . ' date_to';
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
                    $from_class = $obj_Classes->find('all', [
                        'conditions' => [
                            'CidcClasses.id' => $payload['from_cidc_class_id'],
                            'CidcClasses.enabled' => true,
                            'CidcClasses.status' => $obj_Classes->PUBLISHED

                        ]
                    ])->first();
                    if (!$from_class) {
                        $status = 999;
                        $message = 'NOT_FOUND_FROM_CLASS';
                        goto api_result;
                    }
                    $payload['from_start_time'] = $from_class->start_time->format('H:i');
                    $payload['from_end_time'] = $from_class->end_time->format('H:i');

                    // check published class
                    $to_class = $obj_Classes->find('all', [
                        'conditions' => [
                            'CidcClasses.id' => $payload['to_cidc_class_id'],
                            'CidcClasses.enabled' => true,
                            'CidcClasses.status' => $obj_Classes->PUBLISHED

                        ]
                    ])->first();
                    if (!$to_class) {
                        $status = 999;
                        $message = 'NOT_FOUND_TO_CLASS';
                        goto api_result;
                    }
                    $payload['to_start_time'] = $to_class->start_time->format('H:i');
                    $payload['to_end_time'] = $to_class->end_time->format('H:i');

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
                            'StudentRegisterClasses.cidc_class_id' => $payload['from_cidc_class_id'],
                            'StudentRegisterClasses.status' => MyHelper::PAID
                        ]
                    ])->first();
                    if (!$kid_registered_class) {
                        $message = 'KID_NOT_REGISTER_FROM_CLASS';
                        $status = 999;
                        goto api_result;
                    }

                    $dates_from_class = $obj_Classes->get_list_date_by_class($payload['from_cidc_class_id'], $payload['from_cidc_class_id']);
                    if (!$dates_from_class) {
                        $message = 'NOT_FOUND_FROM_CLASS';
                        $status  = 999;
                        goto api_result;
                    }

                    if (!in_array($payload['date_from'], $dates_from_class['dates'])) {
                        $message = 'DATE_FROM_INVALID';
                        $status = 999;
                        goto api_result;
                    }

                    $dates_to_class = $obj_Classes->get_list_date_by_class($payload['from_cidc_class_id'], $payload['to_cidc_class_id']);
                    if (!$dates_to_class) {
                        $message = 'NOT_FOUND_TO_CLASS';
                        $status  = 999;
                        goto api_result;
                    }
                    if (!in_array($payload['date_to'], $dates_to_class['dates'])) {
                        $message = 'DATE_TO_INVALID';
                        $status = 999;
                        goto api_result;
                    }

                    switch ($from_class->class_type_id) {
                        case MyHelper::CIRCULAR:
                            if ($payload['date_from'] == $payload['date_to']) {
                                $message = 'SAME_DATE_WHEN_CLASS_IS_CIRCULAR';
                                $status = 999;
                                goto api_result;
                            }

                            break;
                        case MyHelper::NONCIRCULAR:
                            if ($payload['from_cidc_class_id'] == $payload['to_cidc_class_id']) {
                                $message = 'SAME_CLASS_WHEN_CLASS_IS_NON_CIRCULAR';
                                $status = 999;
                                goto api_result;
                            }
                            break;
                        default:
                            break;
                    }
                    $exist_item = $this->RescheduleHistories->find(
                        'all',
                        [
                            'conditions' => [
                                'RescheduleHistories.kid_id'                => $payload['kid_id'],
                                'RescheduleHistories.from_cidc_class_id'    => $payload['from_cidc_class_id'],
                                'RescheduleHistories.to_cidc_class_id'      => $payload['to_cidc_class_id'],
                                'RescheduleHistories.date_from'             => $payload['date_from'],
                                'RescheduleHistories.date_to'               => $payload['date_to'],
                            ],
                        ]
                    )->first();
                    if ($exist_item) {
                        $message = 'DUPLICATE_RESCHEDULE';
                        $status = 999;
                        goto api_result;
                    }
                    $db = $this->RescheduleHistories->getConnection();
                    $db->begin();
                    $entity  = $this->RescheduleHistories->newEntity($payload);
                    $entity->date_from = $payload['date_from'];
                    $entity->date_to = $payload['date_to'];
                    if ($model = $this->RescheduleHistories->save($entity)) {
                        if (
                            isset($payload['files']) &&
                            !empty($payload['files']) &&
                            count(json_decode($payload['files'])) != 0
                        ) {
                            $files = json_decode($payload['files']);
                            $obj_Reschedule = TableRegistry::get('RescheduleHistoryFiles');
                            $entities = $obj_Reschedule->find('all', [
                                'conditions' => [
                                    'RescheduleHistoryFiles.path IN' => $files,
                                    'RescheduleHistoryFiles.is_official_link' => 0
                                ]
                            ])->toArray();
                            if (!!$entities) {
                                foreach ($entities as &$entity) {
                                    $entity->is_official_link = 1;
                                    $entity->reschedule_history_id = $model->id;
                                }
                                if (!$obj_Reschedule->saveMany($entities)) {
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

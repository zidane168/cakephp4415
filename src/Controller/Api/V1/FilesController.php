<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AppController;
use Cake\Event\EventInterface;
use App\MyHelper\MyHelper;
use Cake\ORM\TableRegistry;

class FilesController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $authentications = [
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "upload",
                'user_role_id' =>  MyHelper::PARENT,
            ],
        ];
        $this->requireAuthenticate($authentications);
    }

    public function upload()
    {
        $this->Api->init();
        $params = array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();

                if (!isset($payload['files']) || empty($payload['files'])) {
                    $message = __('missing_parameter') . ' files';
                } elseif (!isset($payload['type']) || empty($payload['type'])) {
                    $message = __('missing_parameter') . ' type';
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
                    $relative_path = 'uploads' . DS;
                    $files = $payload['files'];
                    $file_name_suffix = "file";
                    $url = MyHelper::getUrl();
                    switch ($payload['type']) {
                        case MyHelper::SICK_LEAVE:
                            $relative_path = $relative_path . 'SickLeaveHistoryFiles';
                            foreach ($files as $key => $file) {


                                if ($file->getSize() == 0) {
                                    continue;
                                }

                                $uploaded = $this->Common->upload_files($file, $relative_path, $file_name_suffix, $key);

                                if ($uploaded) {
                                    $temp[] = array(
                                        'sick_leave_history_id'         => null,
                                        'file_name'         => $uploaded['ori_name'],
                                        'path'              => $uploaded['path'],
                                        'size'              => $file->getSize(),
                                        'ext'               => $uploaded['ext'],
                                    );
                                    $params[] = [
                                        'host'           => $url,
                                        'original_name' => $uploaded['ori_name'],
                                        'path' => $uploaded['path']
                                    ];
                                }
                            }
                            $obj_SickLeave = TableRegistry::get('SickLeaveHistoryFiles');
                            $file_entities = $obj_SickLeave->newEntities($temp);

                            if (!empty($file_entities)) {
                                if (!$obj_SickLeave->saveMany($file_entities)) {
                                    $message = 'DATA_FILE_SICK_LEAVE_NOT_SAVED';
                                    $params = [
                                        'temp' => $temp,
                                        'file_entities' => $file_entities
                                    ];
                                    goto api_result;
                                }
                            }

                            break;
                        case MyHelper::RESCHEDULE:
                            $relative_path = $relative_path . 'RescheduleHistoryFiles';
                            foreach ($files as $key => $file) {


                                if ($file->getSize() == 0) {
                                    continue;
                                }

                                $uploaded = $this->Common->upload_files($file, $relative_path, $file_name_suffix, $key);

                                if ($uploaded) {
                                    $temp[] = array(
                                        'reschedule_history_id'         => null,
                                        'file_name'                     => $uploaded['ori_name'],
                                        'path'                          => $uploaded['path'],
                                        'size'                          => $file->getSize(),
                                        'ext'                           => $uploaded['ext'],
                                    );
                                    $params[] = [
                                        'host'           => $url,
                                        'original_name' => $uploaded['ori_name'],
                                        'path'          => $uploaded['path']
                                    ];
                                }
                                $obj_Reschedule = TableRegistry::get('RescheduleHistoryFiles');
                                $file_entities = $obj_Reschedule->newEntities($temp);

                                if (!empty($file_entities)) {
                                    if (!$obj_Reschedule->saveMany($file_entities)) {
                                        $message = 'DATA_FILE_NOT_SAVED';
                                        $params = [];
                                        goto api_result;
                                    }
                                }
                            }
                            break;
                        default:
                            $message = "WRONG_TYPE";
                            $params = [];
                            goto api_result;
                    }

                    $temp = [];
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

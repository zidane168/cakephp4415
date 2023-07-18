<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use App\MyHelper\MyHelper;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;


/**
 * RescheduleHistories Controller
 *
 * @method \App\Model\Entity\RescheduleHistory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class RescheduleHistoriesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $data_search = $this->request->getQuery();
        $_conditions =  array();
        if (isset($data_search['status']) && $data_search['status'] != "") {
            $_conditions['RescheduleHistories.enabled'] = intval($data_search['status']);
        }

        if (isset($data_search['from_cidc_class_id']) && $data_search['from_cidc_class_id'] != "") {
            $_conditions['RescheduleHistories.from_cidc_class_id'] = intval($data_search['from_cidc_class_id']);
        }

        if (isset($data_search['to_cidc_class_id']) && $data_search['to_cidc_class_id'] != "") {
            $_conditions['RescheduleHistories.to_cidc_class_id'] = intval($data_search['to_cidc_class_id']);
        }

        if (isset($data_search['kid_id']) && $data_search['kid_id'] != "") {
            $_conditions['RescheduleHistories.kid_id'] = intval($data_search['kid_id']);
        }
        $this->paginate = [
            'conditions' => $_conditions,
            'order' => [
                'RescheduleHistories.id DESC'
            ]
        ];

        $rescheduleHistories = $this->paginate($this->RescheduleHistories, [
            'limit' => Configure::read('web.limit')
        ]);
        $obj_CidcClasses = TableRegistry::get('CidcClasses');
        $cidcClasses = $obj_CidcClasses->get_list($this->lang18)->toArray();
        $obj_Kids = TableRegistry::get('Kids');
        $kids = $obj_Kids->get_list($this->lang18)->toArray();
        $statuses = MyHelper::getStatusPendingApproval();
        $this->set(compact('rescheduleHistories', 'cidcClasses', 'kids', 'statuses', 'data_search'));
    }

    /**
     * View method
     *
     * @param string|null $id Reschedule History id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $rescheduleHistory = $this->RescheduleHistories->get($id, [
            'contain' => [
                'CreatedBy', 'ModifiedBy',
                'RescheduleHistoryFiles'
            ],
        ]); 
        $url = MyHelper::getUrl();
        $obj_CidcClasses = TableRegistry::get('CidcClasses');
        $cidcClasses = $obj_CidcClasses->get_list($this->lang18)->toArray();
        $obj_Kids = TableRegistry::get('Kids');
        $kids = $obj_Kids->get_list($this->lang18)->toArray();
        $statuses = MyHelper::getStatusPendingApproval();
        $this->set(compact('rescheduleHistory', 'cidcClasses', 'kids', 'url'));
    }
    public function load_image()
    {
        $images_model = "RescheduleHistoryFiles";
        $this->set(compact('images_model'));
    }
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $model = 'RescheduleHistories';
        $images_model = 'RescheduleHistoryFiles';
        $rescheduleHistory = $this->RescheduleHistories->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $rescheduleHistory = $this->RescheduleHistories->patchEntity($rescheduleHistory, $this->request->getData());
            $rescheduleHistory->date_from = $data['from_date_id'];
            $rescheduleHistory->date_to = $data['to_date_id'];
            $db = $this->RescheduleHistories->getConnection();
            $db->begin();
            if ($model = $this->RescheduleHistories->save($rescheduleHistory)) {
 
                if ($data['status'] == MyHelper::APPROVAL) {
                    $obj_StudentAttendedClass = TableRegistry::get('StudentAttendedClasses');
                    $result_StudentAttendedClasses = $obj_StudentAttendedClass->find('all', [
                        'conditions' => [
                            'StudentAttendedClasses.kid_id'         => $data['kid_id'],
                            'StudentAttendedClasses.cidc_class_id'  => $data['from_cidc_class_id'],
                            'StudentAttendedClasses.date'           => date('Y-m-d', strtotime($data['from_date_id'])),
                        ],
                    ])->first();
                    $result_StudentAttendedClasses->cidc_class_id = $data['to_cidc_class_id'];
                    $result_StudentAttendedClasses->date =  date('Y-m-d', strtotime($data['to_date_id']));
                    $result_StudentAttendedClasses->status = null; 
 
                    if (!$obj_StudentAttendedClass->save($result_StudentAttendedClasses)) {
                        $db->rollback();
                        $this->Flash->error(__('data_is_not_saved' . ' Student Attended Classes'));
                    }
                }

                // save file
                if (isset($data['RescheduleHistoryFiles']) && !empty($data['RescheduleHistoryFiles'])) {

                    $relative_path = 'uploads' . DS . 'RescheduleHistoryFiles';
                    $file_name_suffix = "file";
                    $files = $data['RescheduleHistoryFiles'];
                    $temp = [];
                    foreach ($files as $key => $file) {
                        $f = $file['image'];

                        if ($f->getSize() == 0) {
                            continue;
                        }

                        $uploaded = $this->Common->upload_files($f, $relative_path, $file_name_suffix, $key);

                        if ($uploaded) {
                            $temp[] = array(
                                'reschedule_history_id'         => $model->id,
                                'file_name'         => $uploaded['ori_name'],
                                'path'              => $uploaded['path'],
                                'size'              => $f->getSize(),
                                'ext'               => $uploaded['ext'],
                            );
                        }
                    }


                    $file_entities = $this->RescheduleHistories->RescheduleHistoryFiles->newEntities($temp);

                    if (!empty($file_entities)) {
                        if (!$this->RescheduleHistories->RescheduleHistoryFiles->saveMany($file_entities)) {
                            $db->rollback();
                            $this->Flash->error(__('data_is_not_saved'));
                        }
                    }
                }
                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            }
            $db->rollback();
            $this->Flash->error(__('The reschedule history could not be saved. Please, try again.'));
        }
        $obj_CidcClasses = TableRegistry::get('CidcClasses');
        $cidcClasses = $obj_CidcClasses->get_list($this->lang18);
        $obj_Kids = TableRegistry::get('Kids');
        $kids = $obj_Kids->get_list($this->lang18);
        $statuses = MyHelper::getStatusPendingApproval();
        $current_language = $this->lang18;
        $this->load_image();
        $this->set(compact('rescheduleHistory', 'cidcClasses', 'statuses', 'kids', 'current_language'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Reschedule History id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $rescheduleHistory = $this->RescheduleHistories->get($id, [
            'contain' => [
                'RescheduleHistoryFiles'
            ],
        ]);
        $images_edit_data =  json_encode($rescheduleHistory['reschedule_history_files']);
        $model = 'RescheduleHistories';
        $images_model = 'RescheduleHistoryFiles';
        if ($this->request->is(['patch', 'post', 'put'])) {

            $data = $this->request->getData(); 
            $rescheduleHistory = $this->RescheduleHistories->patchEntity($rescheduleHistory, $data);  
            $db = $this->RescheduleHistories->getConnection();
            $db->begin();
            if ($this->RescheduleHistories->save($rescheduleHistory)) {
                // 3  save files
                $files = $this->request->getData('RescheduleHistoryFiles');

                if (isset($files) && !empty($files)) {
                    $temp = array();

                    foreach ($files as $key => $file) {

                        $relative_path = 'uploads' . DS . $images_model;
                        $file_name_suffix = "file";

                        $f = $file['image'];
                        if ($f->getSize() == 0) {
                            continue;
                        }

                        $uploaded = $this->Common->upload_files($f, $relative_path, $file_name_suffix, $key);

                        $temp[] = array(
                            'reschedule_history_id'         => $id,
                            'file_name'         => $uploaded['ori_name'],
                            'path'              => $uploaded['path'],
                            'size'              => $f->getSize(),
                            'ext'               => $uploaded['ext'],
                        );
                    } // end foreach

                    $orm_RescheduleHistoryFiles = $this->RescheduleHistories->RescheduleHistoryFiles->newEntities($temp);
                    if (!empty($orm_RescheduleHistoryFiles)) {

                        if (!$this->RescheduleHistories->RescheduleHistoryFiles->saveMany($orm_RescheduleHistoryFiles)) {
                            $db->rollback();
                            $this->Flash->error(__('data_is_not_saved'));
                            $this->redirect(array('action' => 'index'));
                        }
                    }
                }

                // 4, delete images
                $remove_images = $this->request->getData('remove_image')[0];

                if (isset($remove_images) && !empty($remove_images)) {
                    $remove_images = json_decode($remove_images);
                    $this->RescheduleHistories->remove_uploaded_image('RescheduleHistoryFiles', $remove_images);
                }
   
                if ($data['status'] == MyHelper::APPROVAL) {
                    $obj_StudentAttendedClasses = TableRegistry::get('StudentAttendedClasses');
 
                    $result_StudentAttendedClasses = $obj_StudentAttendedClasses->find('all', [
                        'conditions' => [
                            'StudentAttendedClasses.kid_id'         => $rescheduleHistory->kid_id,
                            'StudentAttendedClasses.cidc_class_id'  => $rescheduleHistory->from_cidc_class_id,  
                            'StudentAttendedClasses.date'           => $rescheduleHistory->date_from->format('Y-m-d'), 
                        ]
                    ])->first();
                    $result_StudentAttendedClasses->cidc_class_id = $rescheduleHistory->to_cidc_class_id;
                    $result_StudentAttendedClasses->date = $rescheduleHistory->date_to->format('Y-m-d');
                    $result_StudentAttendedClasses->status = null;
 
                    if (!$obj_StudentAttendedClasses->save($result_StudentAttendedClasses)) {
                        $db->rollback();
                        $this->Flash->error(__('data_is_not_saved') . '  Student Attended Classes');
                    }
                }
                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            }
            $db->rollback();

            $this->Flash->error(__('The reschedule history could not be saved. Please, try again.'));
        }
        $obj_CidcClasses = TableRegistry::get('CidcClasses');
        $cidcClasses = $obj_CidcClasses->get_list($this->lang18);
        $obj_Kids = TableRegistry::get('Kids');
        $kids = $obj_Kids->get_list($this->lang18)->toArray();
        $statuses = MyHelper::getStatusPendingApproval();
        $current_language = $this->lang18;
        $this->load_image();
        $this->set(compact('rescheduleHistory', 'cidcClasses', 'statuses', 'kids', 'current_language', 'images_edit_data'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Reschedule History id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function _delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $rescheduleHistory = $this->RescheduleHistories->get($id);
        if ($this->RescheduleHistories->delete($rescheduleHistory)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The reschedule history could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

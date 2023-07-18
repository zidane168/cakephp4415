<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use App\MyHelper\MyHelper;

/**
 * SickLeaveHistories Controller
 *
 * @method \App\Model\Entity\SickLeaveHistory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SickLeaveHistoriesController extends AppController
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
            $_conditions['SickLeaveHistories.enabled'] = intval($data_search['status']);
        }
        if (isset($data_search['from_cidc_class_id']) && $data_search['from_cidc_class_id'] != "") {
            $_conditions['SickLeaveHistories.from_cidc_class_id'] = intval($data_search['from_cidc_class_id']);
        }
        if (isset($data_search['kid_id']) && $data_search['kid_id'] != "") {
            $_conditions['SickLeaveHistories.kid_id'] = intval($data_search['kid_id']);
        }
        $this->paginate = [
            'conditions' => $_conditions,
            'order' => [
                'SickLeaveHistories.id DESC'
            ]
        ];
        $sickLeaveHistories = $this->paginate(
            $this->SickLeaveHistories,
            [
                'limit' => Configure::read('web.limit')
            ]
        );
        $obj_CidcClasses = TableRegistry::get('CidcClasses');
        $cidcClasses = $obj_CidcClasses->get_list($this->lang18)->toArray();
        $obj_Kids = TableRegistry::get('Kids');
        $kids = $obj_Kids->get_list($this->lang18)->toArray();
        $this->set(compact('sickLeaveHistories', 'cidcClasses', 'kids', 'data_search'));
    }

    /**
     * View method
     *
     * @param string|null $id Sick Leave History id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $sickLeaveHistory = $this->SickLeaveHistories->get($id, [
            'contain' => [
                'SickLeaveHistoryFiles',
                'CreatedBy', 'ModifiedBy',
            ],
        ]);
        $url = MyHelper::getUrl();
        $obj_CidcClasses = TableRegistry::get('CidcClasses');
        $cidcClasses = $obj_CidcClasses->get_list($this->lang18)->toArray();
        $obj_Kids = TableRegistry::get('Kids');
        $kids = $obj_Kids->get_list($this->lang18)->toArray();
        $this->set(compact('sickLeaveHistory', 'cidcClasses', 'kids', 'url'));
    }
    public function load_image()
    {
        $images_model = "SickLeaveHistoryFiles";
        $this->set(compact('images_model'));
    }
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $model = 'SickLeaveHistories';
        $images_model = 'SickLeaveHistoryFiles';
        $sickLeaveHistory = $this->SickLeaveHistories->newEmptyEntity();
        $db = $this->SickLeaveHistories->getConnection();
        $db->begin();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['date'] = $data['date_id'];
            $sickLeaveHistory = $this->SickLeaveHistories->patchEntity($sickLeaveHistory, $data);

            if ($model = $this->SickLeaveHistories->save($sickLeaveHistory)) {
                if (isset($data['SickLeaveHistoryFiles']) && !empty($data['SickLeaveHistoryFiles'])) {

                    $relative_path = 'uploads' . DS . 'SickLeaveHistoryFiles';
                    $file_name_suffix = "file";
                    $files = $data['SickLeaveHistoryFiles'];
                    $temp = [];
                    foreach ($files as $key => $file) {
                        $f = $file['image'];

                        if ($f->getSize() == 0) {
                            continue;
                        }

                        $uploaded = $this->Common->upload_files($f, $relative_path, $file_name_suffix, $key);

                        if ($uploaded) {
                            $temp[] = array(
                                'sick_leave_history_id'         => $model->id,
                                'file_name'         => $uploaded['ori_name'],
                                'path'              => $uploaded['path'],
                                'size'              => $f->getSize(),
                                'ext'               => $uploaded['ext'],
                            );
                        }
                    }


                    $file_entities = $this->SickLeaveHistories->SickLeaveHistoryFiles->newEntities($temp);

                    if (!empty($file_entities)) {
                        if (!$this->SickLeaveHistories->SickLeaveHistoryFiles->saveMany($file_entities)) {
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
            $this->Flash->error(__('The sick leave history could not be saved. Please, try again.'));
        }
        $obj_CidcClasses = TableRegistry::get('CidcClasses');
        $cidcClasses = $obj_CidcClasses->get_list($this->lang18);
        $obj_Kids = TableRegistry::get('Kids');
        $kids = $obj_Kids->get_list($this->lang18);
        $current_language = $this->lang18;
        $this->load_image();
        $this->set(compact('sickLeaveHistory', 'cidcClasses', 'kids', 'current_language'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Sick Leave History id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $sickLeaveHistory = $this->SickLeaveHistories->get($id, [
            'contain' => [
                'SickLeaveHistoryFiles'
            ],
        ]);

        $images_edit_data =  json_encode($sickLeaveHistory['sick_leave_history_files']);
        $model = 'SickLeaveHistories';
        $images_model = 'SickLeaveHistoryFiles';
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            if ($data['kid_id'] == 0) {
                unset($data['kid_id']);
            }
            $sickLeaveHistory = $this->SickLeaveHistories->patchEntity($sickLeaveHistory, $data);
            if ($data['date_id'] != 0) {
                $sickLeaveHistory->date = $data['date_id'];
            }
            $db = $this->SickLeaveHistories->getConnection();
            $db->begin();
            if ($this->SickLeaveHistories->save($sickLeaveHistory)) {
                // 3  save files
                $files = $this->request->getData('SickLeaveHistoryFiles');

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
                            'sick_leave_history_id'         => $id,
                            'file_name'         => $uploaded['ori_name'],
                            'path'              => $uploaded['path'],
                            'size'              => $f->getSize(),
                            'ext'               => $uploaded['ext'],
                        );
                    } // end foreach

                    $orm_SickLeaveHistoryFiles = $this->SickLeaveHistories->SickLeaveHistoryFiles->newEntities($temp);
                    if (!empty($orm_SickLeaveHistoryFiles)) {

                        if (!$this->SickLeaveHistories->SickLeaveHistoryFiles->saveMany($orm_SickLeaveHistoryFiles)) {
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
                    $this->SickLeaveHistories->remove_uploaded_image('SickLeaveHistoryFiles', $remove_images);
                }
                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            }
            $db->rollback();
            $this->Flash->error(__('The sick leave history could not be saved. Please, try again.'));
        }
        $obj_CidcClasses = TableRegistry::get('CidcClasses');
        $cidcClasses = $obj_CidcClasses->get_list($this->lang18);
        $obj_Kids = TableRegistry::get('Kids');
        $kids = $obj_Kids->get_list($this->lang18);
        $current_language = $this->lang18;
        $this->load_image();
        $this->set(compact('sickLeaveHistory', 'cidcClasses', 'kids', 'current_language', 'images_edit_data'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Sick Leave History id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $sickLeaveHistory = $this->SickLeaveHistories->get($id);
        if ($this->SickLeaveHistories->delete($sickLeaveHistory)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The sick leave history could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

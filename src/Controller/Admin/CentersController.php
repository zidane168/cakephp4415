<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;

/**
 * Centers Controller
 *
 * @property \App\Model\Table\CentersTable $Centers
 * @method \App\Model\Entity\Center[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CentersController extends AppController
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

        // get list centers managements belong to administrator
        $session = $this->request->getSession(); 
        $list_centers_management = $session->read('administrator.list_centers_management'); 
 
        if ($list_centers_management && count($list_centers_management) > 0) {
            $_conditions['Centers.id IN '] = $list_centers_management;
        }

        if (isset($data_search['status']) && $data_search['status'] != "") {
            $_conditions['Centers.enabled'] = intval($data_search['status']);
        }

        if (isset($data_search['name']) && !empty($data_search['name'])) {
            $_conditions['LOWER(CenterLanguages.name) LIKE'] = '%' . trim(strtolower($data_search['name'])) . '%';
        }
        $this->paginate = array(
            'fields' => [
                'Centers.id',
                'Centers.code',
                'Centers.account',
                'Centers.username',
                'Centers.bank_name',
                'Centers.latitude',
                'Centers.longitude',
                'Centers.phone_us',
                'Centers.fax_us',
                'Centers.visit_us',
                'Centers.mail_us',
                'Centers.district_id',
                'Centers.sort',
                'Centers.enabled',
                'Centers.created',
                'Centers.modified',
                'CenterLanguages.name'
            ],
            'conditions' => $_conditions,
            'order' => [
                'Centers.id DESC'
            ],
            'contain' => [
                'Districts' => [
                    'fields' => [
                        'Districts.id'
                    ],
                    'conditions' => [
                        'Districts.enabled' => true
                    ],
                    'DistrictLanguages' => [
                        'fields' => [
                            'DistrictLanguages.district_id',
                            'DistrictLanguages.name'
                        ],
                        'conditions' => [
                            'DistrictLanguages.alias' => $this->lang18
                        ]
                    ]
                ],
                'CenterLanguages' => [
                    'fields' => [
                        'CenterLanguages.center_id',
                        'CenterLanguages.name', 
                    ],
                    'conditions' => [
                        'CenterLanguages.alias' => $this->lang18
                    ]
                ]
            ],
            'join' => [
                'table' => 'center_languages',
                'alias' => 'CenterLanguages',
                'type' => 'INNER',
                'conditions' => [
                    'CenterLanguages.center_id = Centers.id',
                    'CenterLanguages.alias' => $this->lang18,
                ],
            ]
        );
        $centers = $this->paginate($this->Centers, array(
            'limit' => Configure::read('web.limit')
        ));

        $this->set(compact('centers', 'data_search'));
    }

    /**
     * View method
     *
     * @param string|null $id Center id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    { 
        $this->check_manage_data_permission_action($id);  

        $center = $this->Centers->get($id, [
            'contain' => [
                'Districts' => [
                    'fields' => [
                        'Districts.id',
                    ],
                    'DistrictLanguages' => [
                        'fields' => [
                            'DistrictLanguages.district_id',
                            'DistrictLanguages.name',
                        ],
                        'conditions' => [
                            'DistrictLanguages.alias' => $this->lang18,
                        ],
                    ],
                ],
                'CreatedBy', 'ModifiedBy', 'Administrators', 'CenterFiles', 'CenterLanguages'],
        ]);
        $language_input_fields = array(
            'name'
        );
        $languages = $center->center_languages;

        $this->set(compact('center', 'language_input_fields', 'languages'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->check_manage_data_permission_action();

        $model = 'Centers'; 
        $center = $this->Centers->newEmptyEntity();
        if ($this->request->is('post')) {
            $db = $this->Centers->getConnection();
            $db->begin();

            $data_center = $this->request->getData(); 

            $center = $this->Centers->patchEntity($center, $data_center);
            $center_language = $this->Centers->CenterLanguages->newEntities($data_center['CenterLanguages']);

            if ($model = $this->Centers->save($center)) {

                // 2, save language
                if (isset($center_language) && !empty($center_language)) {
                    foreach ($center_language as $language) {
                        $language['center_id'] = $model->id;
                    }
                    if (!$this->Centers->CenterLanguages->saveMany($center_language)) {
                        $this->Flash->error(__('data_is_not_saved'));
                        goto load_data;
                    }
                }

                // save file
                if (isset($data_center['CenterFiles']) && !empty($data_center['CenterFiles'])) {

                    $relative_path = 'uploads' . DS . 'CenterFiles';
                    $file_name_suffix = "file";
                    $files = $data_center['CenterFiles'];
                    $temp = [];
                    foreach ($files as $key => $file) {
                        $f = $file['image'];

                        if ($f->getSize() == 0) {
                            continue;
                        }

                        $uploaded = $this->Common->upload_files($f, $relative_path, $file_name_suffix, $key);

                        if ($uploaded) {
                            $temp[] = array(
                                'center_id'         => $model->id,
                                'file_name'         => $uploaded['ori_name'],
                                'path'              => $uploaded['path'],
                                'size'              => $f->getSize(),
                                'ext'               => $uploaded['ext'],
                            );
                        }
                    }

                    $file_entities = $this->Centers->CenterFiles->newEntities($temp);
                    if (!empty($file_entities)) {

                        if (!$this->Centers->CenterFiles->saveMany($file_entities)) {
                            $db->rollback();
                            $this->Flash->error(__('data_is_not_saved'));
                        }
                    }
                }

                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The center could not be saved. Please, try again.'));
            }
        }
        load_data:
        $current_language = $this->lang18;
        $districts = $this->Centers->Districts->get_list($current_language);
        $this->load_language();
        $this->load_image();
        $this->set(compact('center', 'districts', 'current_language'));
    }

    public function load_image()
    {
        $images_model = "CenterFiles";
        $this->set(compact('images_model'));
    }

    public function load_language()
    {
        $language_input_fields = array(
            'id',
            'alias',
            'name', 
        );

        $obj_Language = TableRegistry::get('Languages');
        $languages_list = $obj_Language->get_languages();

        $languages_model = 'CenterLanguages';
        $languages_edit_model = 'center_languages';

        $this->set(compact('language_input_fields', 'languages_list', 'languages_model', 'languages_edit_model'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Center id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->check_manage_data_permission_action($id);

        $images_model = "CenterFiles";
        $center = $this->Centers->get($id, [
            'contain' => [
                'CenterFiles' => [
                    'fields' => [
                        'CenterFiles.id',
                        'CenterFiles.center_id',
                        'CenterFiles.file_name',
                        'CenterFiles.ext',
                        'CenterFiles.size',
                    ],
                ],
                'CenterLanguages',
            ],
        ]);

        $images_edit_data =  json_encode($center['center_files']);

        // add this row for replace $this->request->data (cakephp 2)
        $languages_edit_data   = isset($center['center_languages']) && !empty($center['center_languages']) ? $center['center_languages'] : false;

        if ($this->request->is(['patch', 'post', 'put'])) {

            $db = $this->Centers->getConnection();
            $db->begin();

            $center = $this->Centers->patchEntity($center, $this->request->getData());
            if ($this->Centers->save($center)) {

                // 3  save files
                $files = $this->request->getData('CenterFiles');

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
                            'center_id'         => $id,
                            'file_name'         => $uploaded['ori_name'],
                            'path'              => $uploaded['path'],
                            'size'              => $f->getSize(),
                            'ext'               => $uploaded['ext'],
                        );
                    } // end foreach

                    $orm_CenterFiles = $this->Centers->CenterFiles->newEntities($temp);
                    if (!empty($orm_CenterFiles)) {

                        if (!$this->Centers->CenterFiles->saveMany($orm_CenterFiles)) {
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
                    $this->Centers->remove_uploaded_image('CenterFiles', $remove_images);
                }

                $db->commit();
                $this->Flash->success(__('data_is_saved'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The center could not be saved. Please, try again.'));
                $db->rollback();
            }
        }

        load_data:
        $this->load_language();
        $this->load_image();

        $districts = $this->Centers->Districts->get_list($this->lang18);
        $this->set(compact('center', 'districts', 'images_edit_data', 'languages_edit_data'));
    }



    /**
     * Delete method
     *
     * @param string|null $id Center id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->check_manage_data_permission_action($id);
        
        $this->request->allowMethod(['post', 'delete']);
        $center = $this->Centers->get($id);
        if ($this->Centers->delete($center)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The center could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

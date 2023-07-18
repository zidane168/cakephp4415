<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Core\Configure; 
use App\MyHelper\MyHelper; 

/**
 * StudentRegisterClasses Controller
 *
 * @property \App\Model\Table\StudentRegisterClassesTable $StudentRegisterClasses
 * @method \App\Model\Entity\StudentRegisterClass[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StudentRegisterClassesController extends AppController
{ 
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        // $this->paginate = [
        //     'contain' => ['CidcClasses', 'Kids', 'CreatedBy', 'ModifiedBy'],
        // ];
        // $studentRegisterClasses = $this->paginate($this->StudentRegisterClasses);

        // $this->set(compact('studentRegisterClasses'));

        $data_search = $this->request->getQuery();
        $_conditions =  array();

        $session = $this->request->getSession(); 

        $list_centers_management = $session->read('administrator.list_centers_management'); 
        
        if ($list_centers_management && count($list_centers_management) > 0) {
            $cidcClasses = $this->StudentRegisterClasses->CidcClasses->get_list_id_belong_center($this->lang18, $list_centers_management);
 
            if ($cidcClasses) {
                $_conditions['StudentRegisterClasses.cidc_class_id IN'] = $cidcClasses;
            }  
        }

        if (isset($data_search['status']) && $data_search['status'] != "") {
            $_conditions['StudentRegisterClasses.enabled'] = intval($data_search['status']);
        }

        if (isset($data_search['cidc_class_id']) && $data_search['cidc_class_id'] != "") {
            $_conditions['StudentRegisterClasses.cidc_class_id'] = intval($data_search['cidc_class_id']);
        }

        if (isset($data_search['kid_id']) && $data_search['kid_id'] != "") {
            $_conditions['StudentRegisterClasses.kid_id'] = intval($data_search['kid_id']);
        }

        $this->paginate = array(
            'fields' => [
                'StudentRegisterClasses.id',
                'StudentRegisterClasses.cidc_class_id',
                'StudentRegisterClasses.kid_id',
                'StudentRegisterClasses.fee',
                'StudentRegisterClasses.is_attended',
                'StudentRegisterClasses.order_id',
                'StudentRegisterClasses.status',
                'StudentRegisterClasses.enabled',
                'StudentRegisterClasses.created',
                'StudentRegisterClasses.modified',
            ],
            'conditions' => $_conditions,
            'order' => [
                'StudentRegisterClasses.modified DESC',
                'StudentRegisterClasses.created DESC',
                'StudentRegisterClasses.id DESC'
            ],
            'contain' => [
                'CidcClasses' => [
                    'fields' => [
                        'CidcClasses.id',
                        'CidcClasses.name', 
                        'CidcClasses.start_date',
                        'CidcClasses.end_date', 
                        'CidcClasses.start_time',
                        'CidcClasses.end_time', 
                    ],
                ],
                'Kids' => [
                    'fields' => [
                        'Kids.id'
                    ],
                    'KidLanguages' => [
                        'fields' => [
                            'KidLanguages.kid_id',
                            'KidLanguages.name',
                        ],
                        'conditions' => [
                            'KidLanguages.alias' => $this->lang18,
                        ],
                    ],
                ],
            ],
        );

        $studentRegisterClasses = $this->paginate($this->StudentRegisterClasses, array(
            'limit' => Configure::read('web.limit')
        ));

        $cidcClasses = $this->StudentRegisterClasses->CidcClasses->get_list_belong_center($this->lang18, $list_centers_management);
   
        $kids = $this->StudentRegisterClasses->Kids->get_list($this->lang18);

        $this->set(compact('studentRegisterClasses', 'data_search', 'cidcClasses', 'kids'));
    }

    /**
     * View method
     *
     * @param string|null $id Student Register Class id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $studentRegisterClass = $this->StudentRegisterClasses->get($id, [
            'contain' => [
                'Orders' => [
                    'fields' => [
                        'Orders.id',
                        'Orders.order_number',
                    ],
                ],
                'CidcClasses', 'Kids', 'CreatedBy', 'ModifiedBy'],
        ]);
        $url = MyHelper::getUrl();
        $this->set(compact('studentRegisterClass', 'url'));
    }

    public function load_image()
    {
        $images_model = "StudentRegisterClassReceipts";
        $this->set(compact('images_model'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $model = 'StudentRegisterClasses';
        $studentRegisterClass = $this->StudentRegisterClasses->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $obj_CidcClasses = $this->loadModel('CidcClasses');
            $result_CidcClasses = $obj_CidcClasses->get($data['cidc_class_id']);

            $data['fee'] = $result_CidcClasses->fee;
            $data['status'] = intval($data['status']);
            $data['order_id'] = $this->StudentRegisterClasses->gen_order_id($data['cidc_class_id'], $data['kid_id']);
            $studentRegisterClass = $this->StudentRegisterClasses->patchEntity($studentRegisterClass, $data);
            $db = $this->StudentRegisterClasses->getConnection();
            $db->begin();
            if (  $result_CidcClasses->number_of_register >= $result_CidcClasses->maximum_of_students ) {
                $db->rollback();
                $this->Flash->error(__d('cidcclass', 'full_slot_already'));
                return $this->redirect(['action' => 'index']);
            }

            if ($model = $this->StudentRegisterClasses->save($studentRegisterClass)) {

                // +1 number of register;
                $result_CidcClasses->number_of_register = $result_CidcClasses->number_of_register + 1;
                if (!$obj_CidcClasses->save($result_CidcClasses)) {
                    $db->rollback();
                    $this->Flash->error(__('data_is_not_saved') . ' CidcClasses');
                    return $this->redirect(['action' => 'index']);
                }

                // save file 
                if (isset($data['StudentRegisterClassReceipts']) && !empty($data['StudentRegisterClassReceipts'])) {

                    $relative_path = 'uploads' . DS . 'StudentRegisterClassReceipts';
                    $file_name_suffix = "file";
                    $files = $data['StudentRegisterClassReceipts'];
                    $temp = [];
                    foreach ($files as $key => $file) {
                        $f = $file['image'];
                        if (!$f) {
                            continue;
                        }
                        if ($f->getSize() == 0) {
                            continue;
                        }

                        $uploaded = $this->Common->upload_files($f, $relative_path, $file_name_suffix, $key);
                        if ($uploaded) {
                            $temp[] = array(
                                'student_register_class_id'         => $model->id,
                                'file_name'         => $uploaded['ori_name'],
                                'path'              => $uploaded['path'],
                                'size'              => $f->getSize(),
                                'ext'               => $uploaded['ext'],
                            );
                        }
                    }

                    $file_entities = $this->StudentRegisterClasses->StudentRegisterClassReceipts->newEntities($temp);
                    if (!empty($file_entities)) {

                        if (!$this->StudentRegisterClasses->StudentRegisterClassReceipts->saveMany($file_entities)) {
                            $db->rollback();
                            $this->Flash->error(__('data_is_not_saved'));
                        }
                    }
                }

                // send system message
                $cidcClass = $this->loadModel('CidcClasses')->get($data['cidc_class_id']);
                $class_info     = $cidcClass->name . '-' . $cidcClass->code;

                $obj_kid        = $this->loadModel('Kids');
                $kid_infos      = $obj_kid->get_kid_info($data['kid_id'], $this->lang18);

                if ($class_info && !empty($class_info) && $kid_infos && !empty($kid_infos)) {
                    $cidc_parent_id = $kid_infos->cidc_parent_id;
                    $kid_info       = $obj_kid->format_kid_info($kid_infos);

                    $obj_systemMessage = $this->loadModel('SystemMessages');
                    $result_systemMessage = [];
                    if ($data['status'] == MyHelper::UNPAID) {
                        $arr_messages = $obj_systemMessage->create_register_successfully_messages($class_info, $kid_info);
                        $result_systemMessage =  $obj_systemMessage->create($data['cidc_class_id'], $cidc_parent_id, $data['kid_id'], $arr_messages);
                    } elseif ($data['status'] == MyHelper::PAID) {
                        $arr_messages = $obj_systemMessage->create_register_successfully_messages($class_info, $kid_info);
                        $result_systemMessage =  $obj_systemMessage->create($data['cidc_class_id'], $cidc_parent_id, $data['kid_id'], $arr_messages);

                        $arr_messages = $obj_systemMessage->create_payment_successfully_messages($class_info, $kid_info);
                        $result_systemMessage =  $obj_systemMessage->create($data['cidc_class_id'], $cidc_parent_id, $data['kid_id'], $arr_messages);
                    }

                    if ($result_systemMessage['status'] == 200) {
                        $this->Flash->success(__('data_is_saved'));
                    } else {
                        $this->Flash->success(__('data_is_saved') . ' without message');
                    }
                } else {
                    $this->Flash->success(__('data_is_saved'));
                }

                $db->commit();
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The student register class could not be saved. Please, try again.'));
        }
        $cidcClasses = $this->StudentRegisterClasses->CidcClasses->get_list($this->lang18);
        
        $session = $this->request->getSession(); 
        $list_centers_management = $session->read('administrator.list_centers_management'); 
        $cidcClasses = $this->StudentRegisterClasses->CidcClasses->get_list_belong_center($this->lang18, $list_centers_management);
   
        $feeCidcClasses = $this->StudentRegisterClasses->CidcClasses->get_list_name_fee()->toArray();
        $kids = $this->StudentRegisterClasses->Kids->get_list($this->lang18);
        $statuses = MyHelper::getStatusPaidUnpaid();
        $this->load_image();

        $socket_server_url = Configure::read('socket.server');
        $this->set(compact('studentRegisterClass', 'cidcClasses', 'kids',  'feeCidcClasses', 'statuses', 'socket_server_url'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Student Register Class id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $images_model = "StudentRegisterClassReceipts";
        $studentRegisterClass = $this->StudentRegisterClasses->get($id, [
            'contain' => [
                'StudentRegisterClassReceipts'
            ],
        ]);

        if ($studentRegisterClass->status == MyHelper::PAID) {
            $this->Flash->error(__d('cidcclass', 'cannot_change_info_payment_paid_already'));
            return $this->redirect(['action' => 'index']);
        }

        $images_edit_data =  json_encode($studentRegisterClass['student_register_class_receipts']);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $db = $this->StudentRegisterClasses->getConnection();
            $db->begin();
            $data = $this->request->getData();
            $result_CidcClasses = $this->loadModel('CidcClasses')->get($studentRegisterClass['cidc_class_id']);

            if ($result_CidcClasses->number_of_register > $result_CidcClasses->maximum_of_students) {
                $db->rollback();
                $this->Flash->error(__d('cidcclass', 'full_slot_already'));
                return $this->redirect(['action' => 'index']);
            }

            $data['fee'] = $result_CidcClasses->fee;
            $data['status'] = intval($data['status']);

            $studentRegisterClass = $this->StudentRegisterClasses->patchEntity($studentRegisterClass, $data);

            if ($this->StudentRegisterClasses->save($studentRegisterClass)) {
                // 3  save files
                $files = $this->request->getData('StudentRegisterClassReceipts');

                if (isset($files) && !empty($files)) {
                    $temp = array();

                    foreach ($files as $key => $file) {

                        $relative_path = 'uploads' . DS . $images_model;
                        $file_name_suffix = "file";
                        $f = $file['image'];
                        if (!$f) {
                            continue;
                        }

                        if ($f->getSize() == 0) {
                            continue;
                        }

                        $uploaded = $this->Common->upload_files($f, $relative_path, $file_name_suffix, $key);

                        $temp[] = array(
                            'student_register_class_id'         => $id,
                            'file_name'         => $uploaded['ori_name'],
                            'path'              => $uploaded['path'],
                            'size'              => $f->getSize(),
                            'ext'               => $uploaded['ext'],
                        );
                    } // end foreach

                    $orm_StudentRegisterClassReceipts = $this->StudentRegisterClasses->StudentRegisterClassReceipts->newEntities($temp);
                    if (!empty($orm_StudentRegisterClassReceipts)) {

                        if (!$this->StudentRegisterClasses->StudentRegisterClassReceipts->saveMany($orm_StudentRegisterClassReceipts)) {
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
                    $this->StudentRegisterClasses->remove_uploaded_image('StudentRegisterClassReceipts', $remove_images);
                }

                // send system message
                $cidcClass = $this->loadModel('CidcClasses')->get($data['cidc_class_id']);
                $class_info = $cidcClass->name . '-' . $cidcClass->code;

                $obj_kid = $this->loadModel('Kids');
                $kid_infos = $obj_kid->get_kid_info($data['kid_id'], $this->lang18);

                if ($class_info && !empty($class_info) && $kid_infos && !empty($kid_infos)) {

                    $cidc_parent_id = $kid_infos->cidc_parent_id;
                    $kid_info       = $obj_kid->format_kid_info($kid_infos);

                    $obj_systemMessage = $this->loadModel('SystemMessages');

                    if ($data['status'] == MyHelper::PAID) {
                        $result_systemMessage = [];
                        $arr_messages = $obj_systemMessage->create_payment_successfully_messages($class_info, $kid_info);
                        $result_systemMessage =  $obj_systemMessage->create($data['cidc_class_id'], $cidc_parent_id, $data['kid_id'], $arr_messages);

                        if ($result_systemMessage['status'] == 200) {
                            $this->Flash->success(__('data_is_saved'));
                        } else {
                            $this->Flash->success(__('data_is_saved') . ' without message');
                        }
                    }
                } else {
                    $this->Flash->success(__('data_is_saved'));
                }

                $db->commit();

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The student register class could not be saved. Please, try again.'));
        }
        $cidcClasses = $this->StudentRegisterClasses->CidcClasses->get_list($this->lang18);
        $kids = $this->loadModel('Kids')->get_kids_no_register_class($studentRegisterClass['cidc_class_id'], $this->lang18, $studentRegisterClass['kid_id']);
        $current_kid_id = $studentRegisterClass['kid_id'];
        $statuses = MyHelper::getStatusPaidUnpaid();
        $socket_server_url = Configure::read('socket.server');
        $this->set(compact('studentRegisterClass', 'current_kid_id', 'kids', 'cidcClasses',  'statuses', 'images_model', 'images_edit_data', 'socket_server_url'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Student Register Class id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $studentRegisterClass = $this->StudentRegisterClasses->get($id);

        $db = $this->StudentRegisterClasses->getConnection();
        $db->begin();
        if ($this->StudentRegisterClasses->delete($studentRegisterClass)) {


            // decrease 1 number of register
            $obj_CidcClasses = $this->loadModel('CidcClasses');
            $result_CidcClasses = $obj_CidcClasses->get($studentRegisterClass->cidc_class_id);
            $result_CidcClasses->number_of_register =  $result_CidcClasses->number_of_register - 1;

            if (!$obj_CidcClasses->save($result_CidcClasses)) {
                $db->rollback();
                $this->Flash->success(__('data_is_not_saved')  . ' number of register');
            } else {

                $db->commit();
                $this->Flash->success(__('data_is_deleted'));
            }
        } else {
            $db->rollback();
            $this->Flash->error(__('The student register class could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

}

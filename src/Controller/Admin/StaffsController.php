<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

use App\Controller\Admin\AppController;
use App\MyHelper\MyHelper;

/**
 * Staffs Controller
 *
 * @property \App\Model\Table\StaffsTable $Staffs
 * @method \App\Model\Entity\Staff[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StaffsController extends AppController
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
        $genders = MyHelper::getGenders();

        $session = $this->request->getSession(); 
        $list_centers_management = $session->read('administrator.list_centers_management'); 
        if ($list_centers_management && count($list_centers_management) > 0) {
            $_conditions['Staffs.center_id IN'] = $list_centers_management;
        }

        if (isset($data_search['status']) && $data_search['status'] != "") {
            $_conditions['Users.enabled'] = intval($data_search['status']);
        }
        if (isset($data_search['name']) && !empty($data_search['name'])) {
            $_conditions['LOWER(StaffLanguages.name) LIKE'] = '%' . trim(strtolower($data_search['name'])) . '%';
        }

        if (isset($data_search['center_id']) && $data_search['center_id'] != "") {
            $_conditions['Staffs.center_id'] = intval($data_search['center_id']);
        }
        $this->paginate = array(
            'fields' => [
                'Staffs.id',
                'Staffs.user_id',
                'Staffs.center_id',
                'Staffs.gender',
                'Staffs.created',
                'Staffs.modified',
                'StaffLanguages.name',
                'Users.phone_number',
                'Users.email',
                'Users.enabled'
            ],
            'conditions' => $_conditions,
            'order' => [
                'Staffs.id DESC'
            ],
            'contain' => [
                'Centers' => [
                    'fields' => [
                        'Centers.id',
                    ],
                    'conditions' => [
                        'Centers.enabled' => true
                    ],
                    'CenterLanguages' => [
                        'conditions' => [
                            'CenterLanguages.alias' => $this->lang18
                        ]
                    ]
                ],
            ],
            'join' => [
                [
                    'table' => 'staff_languages',
                    'alias' => 'StaffLanguages',
                    'type' => 'INNER',
                    'conditions' => [
                        'StaffLanguages.staff_id = Staffs.id',
                        'StaffLanguages.alias' => $this->lang18,
                    ],
                ],
                [
                    'table' => 'users',
                    'alias' => 'Users',
                    'type' => 'INNER',
                    'conditions' => [
                        'Users.id = Staffs.user_id',
                    ],
                ]

            ]
        );
        $staffs = $this->paginate($this->Staffs, array(
            'limit' => Configure::read('web.limit')
        ));

        $administrator_id = $session->read('administrator.list_centers_management'); 
        $centers = $this->Staffs->Centers->get_list_belong_administrator($this->lang18, $administrator_id);
 
        $this->set(compact('staffs', 'data_search', 'genders', 'centers'));
    }

    /**
     * View method
     *
     * @param string|null $id Staff id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    { 
        $staff = $this->Staffs->get($id, [
            'contain' => [
                'CreatedBy', 'ModifiedBy',
                'StaffLanguages' => [
                    'conditions' => [
                        'StaffLanguages.alias' => $this->lang18
                    ]
                ],
                'Users'
            ],
        ]);

        $this->check_manage_data_permission_action($staff->center_id);

        $genders = MyHelper::getGenders();
        $language_input_fields = array(
            'name'
        );
        $languages = $staff->staff_languages;
        
        $this->set(compact('staff', 'language_input_fields', 'languages', 'genders'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $model = 'Staffs';
        $genders = MyHelper::getGenders();

        $staff = $this->Staffs->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $db = $this->Staffs->getConnection();
            $db->begin();
            $addUser = $this->Staffs->Users->add($this->getRole(), $data['phone_number'], $data['password'], $data['email']);
            if ($addUser['status'] != 200) {
                $db->rollback();
                $this->Flash->error($addUser['message']);
                goto load_data;
            }

            $_cidcParent = [
                'center_id' => $data['center_id'],
                'gender'    => $data['gender'],
                'user_id'   => $addUser['params']['id'],
            ];
            $staff = $this->Staffs->patchEntity($staff, $_cidcParent);
            $staff_language = $this->Staffs->StaffLanguages->newEntities($data['StaffLanguages']);

            if ($model = $this->Staffs->save($staff)) {

                // 2, save language
                if (isset($staff_language) && !empty($staff_language)) {
                    foreach ($staff_language as $language) {
                        $language['staff_id'] = $model->id;
                    }
                    if (!$this->Staffs->StaffLanguages->saveMany($staff_language)) {
                        $this->Flash->error(__('data_is_not_saved'));
                        goto load_data;
                    }
                }
                save_data:
                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The staff could not be saved. Please, try again.'));
            }
        }

        load_data:
        $current_language = $this->lang18;

        $session = $this->request->getSession();
        $administrator_id = $session->read('administrator.list_centers_management'); 
        $centers = $this->Staffs->Centers->get_list_belong_administrator($this->lang18, $administrator_id);
       
        $this->load_language();
        $this->set(compact('staff', 'current_language', 'genders', 'centers'));
    }

    public function load_language()
    {
        $language_input_fields = array(
            'id',
            'alias',
            'name'
        );

        $obj_Language = TableRegistry::get('Languages');
        $languages_list = $obj_Language->get_languages();

        $languages_model = 'StaffLanguages';
        $languages_edit_model = 'staff_languages';

        $this->set(compact('language_input_fields', 'languages_list', 'languages_model', 'languages_edit_model'));
    }
    /**
     * Edit method
     *
     * @param string|null $id Staff id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $genders = MyHelper::getGenders();
        $staff = $this->Staffs->get($id, [
            'contain' => [
                'StaffLanguages',
                'Users'
            ],
        ]);
        $languages_edit_data = (isset($staff['staff_languages']) && !empty($staff['staff_languages'])) ? $staff['staff_languages'] : false;

        // add row for the replace this->request->date_add
        if ($this->request->is(['patch', 'post', 'put'])) {

            $data = $this->request->getData();

            $staff = $this->Staffs->patchEntity($staff, $data);
            $db  = $this->Staffs->getConnection();
            $db->begin();

            if (isset($data['email']) && !empty($data['email']) ) {
                $update_user = $this->Staffs->Users->update($staff->user_id, $this->getRole(), $data['phone_number'], $data['email']);
                if ($update_user['status'] != 200) {
                    $db->rollback();
                    $this->Flash->error($update_user['message']);
                    goto load_data;
                }

            } else {
                $update_user = $this->Staffs->Users->update($staff->user_id, $this->getRole(), $data['phone_number']);
                if ($update_user['status'] != 200) {
                    $db->rollback();
                    $this->Flash->error($update_user['message']);
                    goto load_data;
                }
            } 
           
            if ($model = $this->Staffs->save($staff)) {
                save_data:
                $db->commit();
                $this->Flash->success(__('data_is_saved'));
                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The staff could not be saved. Please, try again.'));
            }
        }

        load_data:
        $this->load_language();
        $current_language = $this->lang18;
        $staff->phone_number = $staff->user->phone_number;
        $staff->email = $staff->user->email;
        $this->set(compact('staff', 'current_language', 'languages_edit_data', 'genders'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Staff id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $staff = $this->Staffs->get($id, [
            'contain' => ['StaffLanguages']
        ]);
        
        $this->check_manage_data_permission_action($staff->center_id);

        if ($this->Staffs->delete($staff)) {
            $this->Staffs->Users->deleteAll(
                [
                    'Users.id' => $staff->user_id
                ]

            );
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The staff could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    private function getRole()
    {
        return MyHelper::STAFF;
    }

    public function enabledDisabledFeature($id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $staff = $this->Staffs->get($id);

        if ($staff) {

            $user = $this->Staffs->Users->find('all', [
                'conditions' => [
                    'Users.id' => $staff->user_id
                ],
                'fields' => [
                    'Users.id',
                    'Users.enabled'
                ],
            ])->first();

            if ($user) {
                $this->Staffs->Users->query()->update()
                    ->set(['enabled' => !$user->enabled])
                    ->where(['id' => $staff->user_id])
                    ->execute();

                $this->Flash->success(__('data_was_updated'));
            } else {
                $this->Flash->error(__('The staff could not be updated. Please, try again.'));
            }
        } else {
            $this->Flash->error(__('The staff could not be updated. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

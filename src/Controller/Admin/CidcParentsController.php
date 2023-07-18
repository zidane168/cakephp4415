<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use PhpParser\Node\Expr\FuncCall;
use App\MyHelper\MyHelper;
use Cake\Routing\Router;

/**
 * CidcParents Controller
 *
 * @method \App\Model\Entity\CidcParent[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CidcParentsController extends AppController
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

        if (isset($data_search['name']) && !empty($data_search['name'])) {
            $_conditions['LOWER(CidcParentLanguages.name) LIKE'] = '%' . trim(strtolower($data_search['name'])) . '%';
        }
        $this->paginate = array(
            'fields' => [
                'CidcParents.id',
                'CidcParents.gender',
                'CidcParents.user_id',
                'CidcParents.created',
                'CidcParents.modified',
                'CidcParentLanguages.name'
            ],
            'conditions' => $_conditions,
            'order' => [
                'CidcParents.id DESC'
            ],
            'contain' => [
                'CidcParentImages' => [
                    'fields' => [
                        'CidcParentImages.cidc_parent_id',
                        'CidcParentImages.path',
                    ],
                ],
                'Users' => [
                    'fields' => [
                        'Users.id',
                        'Users.phone_number',
                        'Users.email',
                        'Users.enabled',
                    ],
                ],
            ],
            'join' => [
                'table' => 'cidc_parent_languages',
                'alias' => 'CidcParentLanguages',
                'type' => 'INNER',
                'conditions' => [
                    'CidcParentLanguages.cidc_parent_id = CidcParents.id',
                    'CidcParentLanguages.alias' => $this->lang18,
                ],
            ]
        );
        $test = $this->CidcParents->Users->find()->first();
        $cidcParents = $this->paginate($this->CidcParents, array(
            'limit' => Configure::read('web.limit')
        ));
        $host = MyHelper::getUrl(); 

        $this->set(compact('cidcParents', 'data_search', 'genders', 'host'));
    }

    /**
     * View method
     *
     * @param string|null $id Cidc Parent id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $cidcParent = $this->CidcParents->get($id, [
            'contain' => [
                'CreatedBy', 'ModifiedBy',
                'CidcParentLanguages' => [
                    'conditions' => [
                        'CidcParentLanguages.alias' => $this->lang18
                    ]
                ],
                'CidcParentImages',
                'Kids' => [
                    'fields' => [
                        'Kids.id',
                        'Kids.cidc_parent_id',
                        'Kids.gender'
                    ],
                    'conditions' => [
                        'Kids.enabled' => true
                    ],
                    'Relationships' => [
                        'fields' => [
                            'Relationships.id',
                        ],
                        'RelationshipLanguages' => [
                            'fields' => [
                                'RelationshipLanguages.relationship_id',
                                'RelationshipLanguages.name',
                            ],
                            'conditions' => [
                                'RelationshipLanguages.alias' => $this->lang18,
                            ],
                        ],
                    ],
                    'KidLanguages' => [
                        'fields' => [
                            'KidLanguages.kid_id',
                            'KidLanguages.name'
                        ],
                        'conditions' => [
                            'KidLanguages.alias' => $this->lang18
                        ]
                    ],
                    'KidImages' => [
                        'fields' => [
                            'KidImages.kid_id',
                            'KidImages.path'
                        ]
                    ]
                ],
                'Users' => [
                    'fields' => [
                        'Users.phone_number',
                        'Users.email',
                    ]
                ]
            ],
        ]);
        $genders = MyHelper::getGenders();
        $language_input_fields = array(
            'name'
        );
        $languages = $cidcParent->cidc_parent_languages;
        $host = MyHelper::getUrl();

        $url = Router::url('/', true); 
        
        $this->set(compact('url', 'cidcParent', 'language_input_fields', 'languages', 'genders', 'host'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $model = 'CidcParents';
        $genders = MyHelper::getGenders();

        $cidcParent = $this->CidcParents->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();

            $db = $this->CidcParents->getConnection();
            $db->begin();
            $addUser = $this->CidcParents->Users->add($this->getRole(), $data['phone_number'], $data['password'], $data['email']);
            if ($addUser['status'] != 200) {
                $db->rollback();
                $this->Flash->error($addUser['message']);
                goto load_data;
            }

            $_cidcParent = [
                'gender'    => $data['gender'],
                'user_id'   => $addUser['params']['id'],
                'address'   => $data['address']
            ];
            $cidcParent = $this->CidcParents->patchEntity($cidcParent, $_cidcParent);
            $cidc_parent_language = $this->CidcParents->CidcParentLanguages->newEntities($data['CidcParentLanguages']);

            if ($model = $this->CidcParents->save($cidcParent)) {

                // 2, save language
                if (isset($cidc_parent_language) && !empty($cidc_parent_language)) {
                    foreach ($cidc_parent_language as $language) {
                        $language['cidc_parent_id'] = $model->id;
                    }
                    if (!$this->CidcParents->CidcParentLanguages->saveMany($cidc_parent_language)) {
                        $db->rollback();
                        $this->Flash->error(__('data_is_not_saved'));
                        goto load_data;
                    }
                }

                // 3, save image
                if (isset($data['CidcParentImages']) && !empty($data['CidcParentImages'])) {

                    // nothing
                    if ($data['CidcParentImages'][0]['image']->getSize() == 0) {
                        goto save_data;
                    }

                    $relative_path = 'uploads' . DS . 'ParentImages';
                    $file_name_suffix = "image";
                    $file = $data['CidcParentImages'][0]['image'];

                    $uploaded = $this->Common->upload_images($file, $relative_path, $file_name_suffix, 1);
 
                    if ($uploaded) {
                        $image = array(
                            'cidc_parent_id'  => $model->id,
                            'path'              => $uploaded['path'],
                            'size'              => $uploaded['size'],
                            'width'             => $uploaded['width'],
                            'height'            => $uploaded['height'],
                            'name'              => $uploaded['re_name']
                        );
 
                        $image = $this->CidcParents->CidcParentImages->newEntity($image);
                        if (!empty($image)) {
                            // delete old first
                            $this->CidcParents->CidcParentImages->deleteAll(
                                ['CidcParentImages.cidc_parent_id' =>  $model->id]
                            );

                            if (!$this->CidcParents->CidcParentImages->save($image)) {
                                $db->rollback();
                                $this->Flash->error(__('data_is_not_saved'));
                                goto load_data;
                            }
                        }
                    }
                }
                save_data:
                $db->commit(); 
                $this->Flash->success(__d('parent', 'you_have_created_parent_successfully_you_should_create_a_children_by_this_link'));

                return $this->redirect([ 'controller' => 'kids', 'action' => 'add', $model->id ]);
            } else {
                $db->rollback();
                $this->Flash->error(__('The cidcParent could not be saved. Please, try again.'));
            }
        }

        load_data:
        $current_language = $this->lang18;
        $this->load_language();
        $this->set(compact('cidcParent', 'current_language', 'genders'));
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

        $languages_model = 'CidcParentLanguages';
        $languages_edit_model = 'cidc_parent_languages';

        $this->set(compact('language_input_fields', 'languages_list', 'languages_model', 'languages_edit_model'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Cidc Parent id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $genders = MyHelper::getGenders();
        $cidcParent = $this->CidcParents->get($id, [
            'contain' => [
                'CidcParentLanguages',
                'CidcParentImages',
                'Users'
            ],
        ]);
        $languages_edit_data = (isset($cidcParent['cidc_parent_languages']) && !empty($cidcParent['cidc_parent_languages'])) ? $cidcParent['cidc_parent_languages'] : false;

        // add row for the replace this->request->date_add
        if ($this->request->is(['patch', 'post', 'put'])) {

            $data = $this->request->getData();

            $cidcParent = $this->CidcParents->patchEntity($cidcParent, $data);
            $db  = $this->CidcParents->getConnection();
            $db->begin();

            if (isset($data['email']) && !empty($data['email']) ) {
                $update_user = $this->CidcParents->Users->update($cidcParent->user_id, $this->getRole(), $data['phone_number'], $data['email']);
                if ($update_user['status'] != 200) {
                    $db->rollback();
                    $this->Flash->error($update_user['message']);
                    goto load_data;
                }

            } else {
                $update_user = $this->CidcParents->Users->update($cidcParent->user_id, $this->getRole(), $data['phone_number']);
                if ($update_user['status'] != 200) {
                    $db->rollback();
                    $this->Flash->error($update_user['message']);
                    goto load_data;
                }
            }
          
            if ($model = $this->CidcParents->save($cidcParent)) {

                if (isset($data['CidcParentImages']) && !empty($data['CidcParentImages'])) {

                    // nothing
                    if ($data['CidcParentImages'][0]['image']->getSize() == 0) {
                        goto save_data;
                    }

                    $relative_path = 'uploads' . DS . 'ParentImages';
                    $file_name_suffix = "image";
                    $file = $data['CidcParentImages'][0]['image'];

                    $uploaded = $this->Common->upload_images($file, $relative_path, $file_name_suffix, 1);

                    if ($uploaded) {
                        $image = array(
                            'cidc_parent_id'    => $model->id,
                            'path'              => $uploaded['path'],
                            'size'              => $uploaded['size'],
                            'width'             => $uploaded['width'],
                            'height'            => $uploaded['height'],
                            'name'              => $uploaded['re_name']
                        );



                        $image = $this->CidcParents->CidcParentImages->newEntity($image);
                        if (!empty($image)) {
                            // delete old first
                            $this->CidcParents->CidcParentImages->deleteAll(
                                ['CidcParentImages.cidc_parent_id' =>  $model->id]
                            );

                            if (!$this->CidcParents->CidcParentImages->save($image)) {
                                $db->rollback();
                                $this->Flash->error(__('data_is_not_saved'));
                                goto load_data;
                            }
                        }
                    }
                }
                save_data:
                $db->commit();
                $this->Flash->success(__('data_is_saved'));
                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The cidcParent could not be saved. Please, try again.'));
            }
        }

        load_data:
        $this->load_language();
        $current_language = $this->lang18;
        $cidcParent->phone_number = $cidcParent->user->phone_number;
        $cidcParent->email = $cidcParent->user->email;
        $this->set(compact('cidcParent', 'current_language', 'languages_edit_data', 'genders'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Cidc Parent id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cidcParent = $this->CidcParents->get($id);
        if ($this->CidcParents->delete($cidcParent)) {
            $this->CidcParents->Users->deleteAll(
                [
                    'Users.id' => $cidcParent->user_id
                ]

            );
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The cidc parent could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    private function getRole()
    {
        return MyHelper::PARENT;
    }

    public function enabledDisabledFeature($id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cidcParent = $this->CidcParents->get($id);

        if ($cidcParent) {

            $user = $this->CidcParents->Users->find('all', [
                'conditions' => [
                    'Users.id' => $cidcParent->user_id
                ],
                'fields' => [
                    'Users.id',
                    'Users.enabled'
                ],
            ])->first();

            if ($user) {
                $this->CidcParents->Users->query()->update()
                    ->set(['enabled' => !$user->enabled])
                    ->where(['id' => $cidcParent->user_id])
                    ->execute();

                // update enabled of kids too
                $result_Kids = $this->CidcParents->Kids->find('all', [
                    'conditions' => [
                        'Kids.cidc_parent_id' => $id,
                    ],
                    'fields' => [
                        'Kids.id',
                    ]
                ]);

                foreach ($result_Kids as $value) {
                    $this->CidcParents->Kids->query()->update()
                        ->set(['enabled' => !$user->enabled])
                        ->where(['id' => $value->id])
                        ->execute();
                }

                $this->Flash->success(__('data_was_updated'));
            } else {
                $this->Flash->error(__('The cidc parent could not be updated. Please, try again.'));
            }
        } else {
            $this->Flash->error(__('The cidc parent could not be updated. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function call_format_phone_number($phone_number) {
        return $this->CidcParents->format_phone_number($phone_number);
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use App\MyHelper\MyHelper;

/**
 * Kids Controller
 *
 * @method \App\Model\Entity\Kid[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class KidsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $data_search = $this->request->getQuery();
        $genders = MyHelper::getGenders();
        $_conditions =  array();

        if (isset($data_search['status']) && $data_search['status'] != "") {
            $_conditions['Kids.enabled'] = intval($data_search['status']);
        }

        if (isset($data_search['cidc_parent_id']) && $data_search['cidc_parent_id'] != "") {
            $_conditions['Kids.cidc_parent_id'] = intval($data_search['cidc_parent_id']);
        }

        if (isset($data_search['relationship_id']) && $data_search['relationship_id'] != "") {
            $_conditions['Kids.relationship_id'] = intval($data_search['relationship_id']);
        }

        if (isset($data_search['name']) && !empty($data_search['name'])) {
            $_conditions['LOWER(KidLanguages.name) LIKE'] = '%' . trim(strtolower($data_search['name'])) . '%';
        }
        $this->paginate = array(
            'fields' => [
                'Kids.id',
                'Kids.cidc_parent_id',
                'Kids.relationship_id',
                'Kids.gender',
                'Kids.dob',
                'Kids.caretaker',
                'Kids.special_attention_needed',
                'Kids.number_of_siblings',
                'Kids.enabled',
                'Kids.created',
                'Kids.modified',
                'KidLanguages.name'
            ],
            'conditions' => $_conditions,
            'order' => [
                'Kids.id DESC'
            ],
            'contain' => [
                'CidcParents' => [
                    'fields' => [
                        'CidcParents.id',
                    ],
                    'CidcParentLanguages' => [
                        'fields' => [
                            'CidcParentLanguages.cidc_parent_id',
                            'CidcParentLanguages.name'
                        ],
                        'conditions' => [
                            'CidcParentLanguages.alias' => $this->lang18
                        ]
                    ]
                ],
                'Relationships' => [
                    'fields' => [
                        'Relationships.id'
                    ],
                    'conditions' => [
                        'Relationships.enabled' => true
                    ],
                    'RelationshipLanguages' => [
                        'fields' => [
                            'RelationshipLanguages.relationship_id',
                            'RelationshipLanguages.name',
                        ],
                        'conditions' => [
                            'RelationshipLanguages.alias' => $this->lang18
                        ]
                    ]
                ]
            ],
            'join' => [
                'table' => 'kid_languages',
                'alias' => 'KidLanguages',
                'type' => 'INNER',
                'conditions' => [
                    'KidLanguages.kid_id = Kids.id',
                    'KidLanguages.alias' => $this->lang18,
                ],
            ]
        );
        $kids = $this->paginate($this->Kids, array(
            'limit' => Configure::read('web.limit')
        ));

        $cidcParents = $this->Kids->CidcParents->get_list($this->lang18);
        $relationships = $this->Kids->Relationships->get_list($this->lang18);
        $this->set(compact('kids', 'data_search', 'genders', 'cidcParents', 'relationships'));
    }

    /**
     * View method
     *
     * @param string|null $id Kid id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $kid = $this->Kids->get($id, [
            'contain' => [
                'KidLanguages' => [],
                'KidImages'    => [],
                'CidcParents' => [
                    'fields' => [
                        'CidcParents.id',
                    ],
                    'CidcParentLanguages' => [
                        'fields' => [
                            'CidcParentLanguages.cidc_parent_id',
                            'CidcParentLanguages.name'
                        ],
                        'conditions' => [
                            'CidcParentLanguages.alias' => $this->lang18
                        ]
                    ]
                ],
                'Relationships' => [
                    'fields' => [
                        'Relationships.id'
                    ],
                    'conditions' => [
                        'Relationships.enabled' => true
                    ],
                    'RelationshipLanguages' => [
                        'fields' => [
                            'RelationshipLanguages.relationship_id',
                            'RelationshipLanguages.name',
                        ],
                        'conditions' => [
                            'RelationshipLanguages.alias' => $this->lang18
                        ]
                    ]
                ],
                'CreatedBy', 'ModifiedBy',
                'KidsEmergencies' => [
                    'fields' => [
                        'KidsEmergencies.kid_id',
                        'KidsEmergencies.relationship_id',
                        'KidsEmergencies.emergency_contact_id',
                    ],
                    'EmergencyContacts' => [
                        'fields' => [
                            'EmergencyContacts.id',
                            'EmergencyContacts.phone_number',
                        ],
                        'conditions' => ['EmergencyContacts.enabled' => true],
                        'EmergencyContactLanguages' => [
                            'fields' => [
                                'EmergencyContactLanguages.emergency_contact_id',
                                'EmergencyContactLanguages.name'
                            ],
                            'conditions' => [
                                'EmergencyContactLanguages.alias' => $this->lang18
                            ]
                        ]
                    ],
                    'Relationships'     => [
                        'fields' => [
                            'Relationships.id',
                        ],
                        'RelationshipLanguages' => [
                            'fields' => [
                                'RelationshipLanguages.relationship_id',
                                'RelationshipLanguages.name'
                            ],
                            'conditions' => [
                                'RelationshipLanguages.alias' => $this->lang18
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        $language_input_fields = array(
            'name'
        );
        $languages = $kid->kid_languages;
        $genders = MyHelper::getGenders();
        $images = $kid->kid_images;
        $emer_contact = $kid->kids_emergencies;
        $relationship = $kid->kids_emergencies;
        $obj_Relationships = TableRegistry::get('Relationships');
        $relationships  = $obj_Relationships->get_list($this->lang18);
        $obj_EmerContacts = TableRegistry::get('EmergencyContacts');
        $emergencyContacts = $obj_EmerContacts->get_list($this->lang18);

        $this->loadModel('Kids');
        $this->set('kidsModel', $this->Kids);
        $this->set(compact('kid', 'language_input_fields', 'languages', 'genders', 'images'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add($id = null)
    {
        $parent_index = $id ? $id : null;

        $model = 'Kids';
        $genders = MyHelper::getGenders();

        $kid = $this->Kids->newEmptyEntity();
        if ($this->request->is('post')) {
            $data_request = $this->request->getData();
            $db = $this->Kids->getConnection();
            $db->begin();

            $kid = $this->Kids->patchEntity($kid, $this->request->getData());
            $kid_language = $this->Kids->KidLanguages->newEntities($this->request->getData()['KidLanguages']);
            if ($model = $this->Kids->save($kid)) {

                // 2, save language
                if (isset($kid_language) && !empty($kid_language)) {
                    foreach ($kid_language as $language) {
                        $language['kid_id'] = $model->id;
                    }
                    if (!$this->Kids->KidLanguages->saveMany($kid_language)) {
                        $this->Flash->error(__('data_is_not_saved'));
                        goto load_data;
                    }
                }

                // 3, save emer-contact
                if (
                    isset($data_request['emergency_contact_id']) && !empty($data_request['emergency_contact_id']) ||
                    isset($data_request['emergency_contact_relationship_id']) && !empty($data_request['emergency_contact_relationship_id'])
                ) {
                    $emer_contact = $this->Kids->KidsEmergencies->newEmptyEntity();
                    $emer_contact = $this->Kids->KidsEmergencies->patchEntity($emer_contact, $data_request);
                    $emer_contact['kid_id'] = $model->id;
                    if (!$this->Kids->KidsEmergencies->save($emer_contact)) {
                        $this->Flash->error(__('data_contact_is_not_saved'));
                        goto load_data;
                    }
                }
                if (isset($data_request['KidImages']) && !empty($data_request['KidImages'])) {

                    // nothing
                    if ($data_request['KidImages'][0]['image']->getSize() == 0) {
                        goto save_data;
                    }

                    $relative_path = 'uploads' . DS . 'KidImages';
                    $file_name_suffix = "image";
                    $file = $data_request['KidImages'][0]['image'];

                    $uploaded = $this->Common->upload_images($file, $relative_path, $file_name_suffix, 1);

                    if ($uploaded) {
                        $image = array(
                            'kid_id'  => $model->id,
                            'path'              => $uploaded['path'],
                            'size'              => $uploaded['size'],
                            'width'             => $uploaded['width'],
                            'height'            => $uploaded['height'],
                            'name'              => $uploaded['re_name']
                        );

                        $image = $this->Kids->KidImages->newEntity($image);

                        if (!empty($image)) {
                            // delete old first
                            $this->Kids->KidImages->deleteAll(
                                ['KidImages.kid_id' =>  $model->id]
                            );

                            if (!$this->Kids->KidImages->save($image)) {
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
                $this->Flash->error(__('The CidcClass could not be saved. Please, try again.'));
            }
        }
        load_data:
        $current_language = $this->lang18;
        $relationships = $this->Kids->Relationships->get_list($current_language);
        $cidcParents = $this->Kids->CidcParents->get_list($current_language);
        $emergencyContacts = $this->Kids->KidsEmergencies->EmergencyContacts->get_list($current_language);
        $this->load_language();
        $this->set(compact('kid', 'current_language', 'relationships', 'cidcParents', 'genders', 'emergencyContacts', 'parent_index'));
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

        $languages_model = 'KidLanguages';
        $languages_edit_model = 'kid_languages';

        $this->set(compact('language_input_fields', 'languages_list', 'languages_model', 'languages_edit_model'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Kid id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $kid = $this->Kids->get($id, [
            'contain' => [
                'KidLanguages',
                'KidsEmergencies',
                'KidImages'
            ],
        ]);
        $genders = MyHelper::getGenders();
        $languages_edit_data = (isset($kid['kid_languages']) && !empty($kid['kid_languages'])) ? $kid['kid_languages'] : false;

        // add row for the replace this->request->date_add
        if ($this->request->is(['patch', 'post', 'put'])) {

            $db  = $this->Kids->getConnection();
            $db->begin();

            $kid = $this->Kids->patchEntity($kid, $this->request->getData());
            $data_request =  $this->request->getData();
            if ($model = $this->Kids->save($kid)) {
                // update 
                $this->Kids->KidsEmergencies->deleteAll([
                    'kid_id' => $id
                ]);
                $kidEmers = $this->Kids->KidsEmergencies->newEmptyEntity();
                $kidEmers->emergency_contact_id = $data_request['emergency_contact_id'];
                $kidEmers->relationship_id = $data_request['relationship_id'];
                $kidEmers->kid_id = $id;
                if (!$this->Kids->KidsEmergencies->save($kidEmers)) {
                    $db->rollback();
                    $this->Flash->error(__('data_KID_EMERS_is_not_saved'));
                    goto load_data;
                }
                if (isset($data_request['KidImages']) && !empty($data_request['KidImages'])) {

                    // nothing
                    if ($data_request['KidImages'][0]['image']->getSize() == 0) {
                        goto save_data;
                    }

                    $relative_path = 'uploads' . DS . 'KidImages';
                    $file_name_suffix = "image";
                    $file = $data_request['KidImages'][0]['image'];

                    $uploaded = $this->Common->upload_images($file, $relative_path, $file_name_suffix, 1);

                    if ($uploaded) {
                        $image = array(
                            'kid_id'  => $model->id,
                            'path'              => $uploaded['path'],
                            'size'              => $uploaded['size'],
                            'width'             => $uploaded['width'],
                            'height'            => $uploaded['height'],
                            'name'              => $uploaded['re_name']
                        );

                        $image = $this->Kids->KidImages->newEntity($image);

                        if (!empty($image)) {
                            // delete old first
                            $this->Kids->KidImages->deleteAll(
                                ['KidImages.kid_id' =>  $model->id]
                            );

                            if (!$this->Kids->KidImages->save($image)) {
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
                $this->Flash->error(__('The kid could not be saved. Please, try again.'));
            }
        }
        load_data:
        $current_language = $this->lang18;
        $obj_Relationships = TableRegistry::get('Relationships');
        $relationships  = $obj_Relationships->get_list($this->lang18);
        $obj_EmerContacts = TableRegistry::get('EmergencyContacts');
        $emergencyContacts = $obj_EmerContacts->get_list($this->lang18);
        $cidcParents = $this->Kids->CidcParents->get_list($current_language);
        $kid['emergency_contact_id'] = isset($kid->kids_emergencies) && !empty($kid->kids_emergencies) ? $kid->kids_emergencies[0]->emergency_contact_id : null;
        $kid['relationship_id'] = isset($kid->kids_emergencies) && !empty($kid->kids_emergencies)  ? $kid->kids_emergencies[0]->relationship_id : null ;
        $this->load_language();
        $this->set(compact('kid', 'current_language', 'relationships', 'cidcParents', 'emergencyContacts', 'languages_edit_data', 'genders'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Kid id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $kid = $this->Kids->get($id);
        if ($this->Kids->delete($kid)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The kid could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

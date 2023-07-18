<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\MyHelper\MyHelper;
use PhpParser\Node\Expr\Cast\Object_;
use Cake\Routing\Router;

/**
 * Kids Model
 *
 * @property \App\Model\Table\RelationshipsTable&\Cake\ORM\Association\BelongsTo $Relationships
 * @property \App\Model\Table\KidImagesTable&\Cake\ORM\Association\HasMany $KidImages
 * @property \App\Model\Table\KidLanguagesTable&\Cake\ORM\Association\HasMany $KidLanguages
 * @property \App\Model\Table\KidsEmergenciesTable&\Cake\ORM\Association\HasMany $KidsEmergencies
 *
 * @method \App\Model\Entity\Kid newEmptyEntity()
 * @method \App\Model\Entity\Kid newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Kid[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Kid get($primaryKey, $options = [])
 * @method \App\Model\Entity\Kid findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Kid patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Kid[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Kid|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Kid saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Kid[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Kid[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Kid[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Kid[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class KidsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('kids');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('CidcParents', [
            'foreignKey' => 'cidc_parent_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Relationships', [
            'foreignKey' => 'relationship_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('KidImages', [
            'foreignKey' => 'kid_id',
        ]);
        $this->hasMany('KidLanguages', [
            'foreignKey' => 'kid_id',
        ]);
        $this->hasMany('KidsEmergencies', [
            'foreignKey' => 'kid_id',
        ]);

        $this->hasMany('StudentRegisterClasses', [
            'foreignKey' => 'kid_id',
        ]);
        $this->hasMany('StudentAttendedClasses', [
            'foreignKey' => 'kid_id',
        ]);

        // belongto;
        $this->addBehavior('WhoDidIt');

        $this->addBehavior('MyCommonFunc');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created'           => 'new',
                    'modified'          => 'always',
                ],
            ]
        ]);

        $this->addBehavior('Audit');    // add Audit (BeforeSave id)
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->boolean('gender');

        // $validator
        //     ->integer('number_of_siblings');

        $validator
            ->scalar('caretaker')
            ->maxLength('caretaker', 191)
            ->allowEmptyString('caretaker');

        $validator
            ->scalar('special_attention_needed')
            ->allowEmptyString('special_attention_needed');

        $validator
            ->boolean('enabled')
            ->notEmptyString('enabled');

        $validator
            ->integer('created_by')
            ->allowEmptyString('created_by');

        $validator
            ->integer('modified_by')
            ->allowEmptyString('modified_by');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        // $rules->add($rules->existsIn(['cidc_parent_id'], 'CidcParents'), ['errorField' => 'cidc_parent_id']);
        // $rules->add($rules->existsIn(['relationship_id'], 'Relationships'), ['errorField' => 'relationship_id']);

        return $rules;
    }

    // $emergency_contacts = {"contact_name": "", "relationship_id": 1, "phone_number": "" }
    // $kid_profiles = [{"gender": 1, "dob": "2020/10/08", relationship_id: 1, "number_of_siblings": 1, "caretaker": "", "special_attention_needed": "", 
    //                  "zh_HK_name": "",  "en_US_name": "", "en_US_nick_name": "" }, {}, {}, ...]

    // noted: need to input emergency_contacts (REQUIRED)
    public function add($parent_id, $kid_profiles)
    { 
        $status = 200;
        $message = __('data_is_saved');
        $params = []; 
 
        $kids = json_decode($kid_profiles, true);    
        $obj_EmergencyContacts = TableRegistry::get('EmergencyContacts');

        if (isset($kid_profiles) && !empty($kid_profiles) && !is_null($kid_profiles) && $kid_profiles != 'null') {

            $db = $this->getConnection();
            $db->begin();
  
            $kids = json_decode($kid_profiles, true);  
            $kid_emergencies = [];

            foreach ($kids as $kid) {

                // add kids
                $kid_data = [
                    'cidc_parent_id'    =>  $parent_id,
                    'relationship_id'   =>  $kid["relationship_id"],
                    'gender'            =>  $kid["gender"],   // 1: male, 0: female
                    'dob'               =>  date('Y-m-d', strtotime($kid["dob"])),
                    'number_of_siblings'        =>  $kid["number_of_siblings"] ? $kid["number_of_siblings"] : 0,
                    'caretaker'                 =>  $kid["caretaker"] ? $kid["caretaker"] : '',
                    'special_attention_needed'  =>  $kid["special_attention_needed"] ? $kid["special_attention_needed"] : '',
                ];

                $entity_kid_data = $this->newEntity($kid_data);

                if ($model_kid = $this->save($entity_kid_data)) {
                    $kid_languages = [];
                    // save child language
                    $kid_languages[] = [
                        'kid_id'            => $model_kid->id,
                        'name'              => $kid["zh_HK_name"] ? $kid["zh_HK_name"] : '', 
                        'alias'             => 'zh_HK',
                    ]; 
                    $kid_languages[] = [
                        'kid_id'            => $model_kid->id,
                        'name'              => $kid["en_US_name"] ? $kid["en_US_name"] : '',   
                        'alias'             => 'en_US',
                    ];
 
                    $entities_kid_languages = $this->KidLanguages->newEntities($kid_languages);

                    if (!$this->KidLanguages->saveMany($entities_kid_languages)) {
                        $db->rollback();
                        $status = 500;
                        $message = __('data_is_not_saved') . ' Kid Languages';
                        goto return_data;
                    }

                    // save emergency contacts 
                   
                    $result_EmergencyContact = []; 

                    $emergency_contact = $kid['emergency_contact'];
                    $result_EmergencyContact = $obj_EmergencyContacts->add($emergency_contact);  

                    $emergency_contact_id = $result_EmergencyContact['params']['id'];
                    $kid_emergencies[] = [
                        'kid_id'                 => $model_kid->id,
                        'relationship_id'        => $emergency_contact['relationship_id'],
                        'emergency_contact_id'   => $emergency_contact_id
                    ];

                    $params = $model_kid;
                } else {
                    $db->rollback();
                    $status = 500;
                    $message = __('data_is_not_saved') . ' Kids';
                    goto return_data;
                }
            }


            if ($kid_emergencies) {

                $obj_KidsEmergencies = TableRegistry::get('KidsEmergencies');
                $entities_kid_emergencies = $obj_KidsEmergencies->newEntities($kid_emergencies);

                if (!$obj_KidsEmergencies->saveMany($entities_kid_emergencies)) {

                    $db->rollback();
                    $status = 500;
                    $message = __('data_is_not_saved') . ' Kids Emergencies';
                    goto return_data;
                }
            }

            $db->commit();
            $status = 200;
            $message = __('data_is_saved') . ' kids';
        }

        return_data:
        return [
            'status' => $status,
            'message' => $message,
            'params' => $params,
        ];
    }

    public function get_list($language)
    {
        $conditions = [
            'Kids.enabled' => true
        ];
        $kids = $this->find(
            'list',
            [
                'keyField' => 'id',
                'valueField' => function ($row) {
                    return  "[" .  $row->kid_languages[0]->kid_id . "] " . $row->kid_languages[0]->name . " (" . $row->cidc_parent->cidc_parent_languages[0]->name .  " - " . $this->format_phone_number($row->cidc_parent->user->phone_number) . ")";
                }
            ]
        )
            ->where([$conditions])
            ->contain(
                [
                    'KidLanguages' => [
                        'conditions' => ['KidLanguages.alias' => $language]
                    ],
                    'CidcParents' => [
                        'CidcParentLanguages' => [
                            'conditions' => [
                                'CidcParentLanguages.alias' => $language
                            ]
                        ],
                        'Users' => []
                    ]
                ]
            );
        return $kids;
    }
  
    public function get_detail($user_id, $language, $id)
    {
        $message = "RETRIEVE_DATA_SUCCESSFULLY";
        $url = MyHelper::getUrl();
        $kid  = $this->find('all', [
            'fields' => [
                'Kids.id',
                'Kids.cidc_parent_id',
                'Kids.relationship_id',
                'Kids.gender',
                'Kids.dob',
                'Kids.number_of_siblings',
                'Kids.caretaker',
                'Kids.special_attention_needed',
            ],
            'conditions' => [
                'Kids.id'       => $id,
                'Kids.enabled'  => true
            ],
            'contain' => [
                'KidImages' => [],
                'KidLanguages' => [
                    'fields' => [
                        'KidLanguages.id',
                        'KidLanguages.kid_id',
                        'KidLanguages.name', 
                        'KidLanguages.alias',
                    ], 
                ],
                'Relationships' => [
                    'fields' => [
                        'Relationships.id'
                    ],
                    'RelationshipLanguages' => [
                        'fields' => [
                            'RelationshipLanguages.relationship_id',
                            'RelationshipLanguages.name'
                        ],
                        'conditions' => [
                            'RelationshipLanguages.alias' => $language
                        ]
                    ]
                ],
                'KidsEmergencies' => [
                    'EmergencyContacts' => [
                        'EmergencyContactLanguages' => [
                            // 'conditions' => [
                            //     'EmergencyContactLanguages.alias' => $language
                            // ]
                        ]
                    ],
                    'Relationships' => [
                        'RelationshipLanguages' => [
                            'conditions' => [
                                'RelationshipLanguages.alias' => $language
                            ]
                        ]
                    ]
                ],
                'CidcParents' => [
                    'fields' => [
                        'CidcParents.id',
                        'CidcParents.user_id',
                    ],
                    'conditions' => [
                        'CidcParents.user_id' => $user_id
                    ]
                ],
                'StudentRegisterClasses' => [
                    'conditions' => [
                        'StudentRegisterClasses.status' => MyHelper::PAID
                    ],
                    'CidcClasses' => [
                        'conditions' => [
                            'CidcClasses.enabled' => true,
                            'CidcClasses.status' => TableRegistry::get('CidcClasses')->PUBLISHED
                        ],
                        'DateOfLessons' => [
                            'fields' => [
                                'DateOfLessons.day',
                                'DateOfLessons.cidc_class_id',
                            ],
                        ],
                        'Programs' => [
                            'ProgramLanguages' => [
                                'conditions' => [
                                    'ProgramLanguages.alias' => $language
                                ]
                            ]
                        ],
                        'Courses' => [
                            'CourseLanguages' => [
                                'conditions' => [
                                    'CourseLanguages.alias' => $language
                                ]
                            ]
                        ],
                        'Centers' => [
                            'CenterLanguages' => [
                                'conditions' => [
                                    'CenterLanguages.alias' => $language
                                ]
                            ],
                            'Districts' => [
                                'DistrictLanguages' => [
                                    'conditions' => [
                                        'DistrictLanguages.alias' => $language
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]

        ])->first();

        $registerd_classes = [
            'past'      => [],
            'upcoming'  => [],
            'current'   => []
        ];
        $obj_StudentAttendedClasses = TableRegistry::get('StudentAttendedClasses');
        if (isset($kid->student_register_classes)) {
            foreach ($kid->student_register_classes as $class) {
                $min_max_date = $obj_StudentAttendedClasses->get_min_max_date_class_and_kid($class->cidc_class_id, $id);
                if ($min_max_date) {
                    $now = date('Y-m-d');
                    $end_date = $min_max_date->max_date;
                    $start_date = $min_max_date->min_date;
                    $now = date('Y-m-d');
                    if ($end_date < $now) {
                        $registerd_classes['past'][] = $this->set_response('FORMAT_KID_CLASSES', $class);
                    } elseif ($start_date > $now) {
                        $registerd_classes['upcoming'][] = $this->set_response('FORMAT_KID_CLASSES', $class);
                    } elseif ($start_date <= $now && $now <= $end_date) {
                        $registerd_classes['current'][] = $this->set_response('FORMAT_KID_CLASSES', $class);
                    }
                }
            }
        }
        $contacts  = [];
        // debug(count($kid->kids_emergencies));
        // exit;
        if (isset($kid->kids_emergencies)) {
            foreach ($kid->kids_emergencies as $emer) {
                $contacts[] = $this->set_response('FORAMT_EMERGENCY_CONTACTS', $emer);
            }
        }
        $result = $this->set_response('FORMAT_KIDS', $kid);
        $result['emergency_contacts'] = $contacts;
        $result['classes'] = $registerd_classes;
        $result['relationship'] = $this->set_response('FORMAT_RELATIONSHIP', $kid);
        return $result;
    }

    public function edit($user_id, $data, $language)
    {
        $status = 200;
        $message = 'DATA_IS_SAVED';
        $db = $this->getConnection();
        $db->begin();
        $parent = $this->CidcParents->find('all', [
            'fields' => ['CidcParents.id'],
            'conditions' => [
                'CidcParents.user_id' => $user_id
            ]
        ])->first();

        $kid = $this->find('all', [
            'conditions' => [
                'Kids.id'               => $data['id'],
                'Kids.cidc_parent_id'   => $parent->id
            ],
            'contain' => [
                'KidLanguages',
                'KidImages',
                'KidsEmergencies' => [
                    'EmergencyContacts' => [
                        'EmergencyContactLanguages'
                    ]
                ]
            ]
        ])->first();
        if (!$kid) {
            $status = 999;
            $message = 'NOT_FOUND_KID';
            goto set_result;
        }

        foreach ($kid->kid_languages as &$item) {
            switch ($item->alias) {
                case 'en_US':
                    $item->name = isset($data['en_US_name']) && !empty($data['en_US_name']) ? $data['en_US_name'] : $item->name;
                    break;
                case 'zh_HK':
                    $item->name = isset($data['zh_HK_name']) && !empty($data['zh_HK_name']) ? $data['zh_HK_name'] : $item->name;
                    break; 
                default:
                    break;
            }
        }

        $kid = $this->patchEntity($kid, $data);
        if (!$this->save($kid)) {
            $db->rollback();
            $message = 'DATA_IS_NOT_SAVED';
            $status = 999;
            goto set_result;
        }

        if (!$this->KidLanguages->saveMany($kid->kid_languages)) {
            $db->rollback();
            $message = 'DATA__KID_LANGUAGE_IS_NOT_SAVED';
            $status = 999;
            goto set_result;
        }

        if (isset($data['emergency_contacts']) && !empty($data['emergency_contacts'])) {
            $input_contacts = json_decode($data['emergency_contacts']);
            if (count($input_contacts) > 5) {
                $message = 'OVER_CONTACTS';
                goto set_result;
            }
            $this->KidsEmergencies->deleteAll(
                [
                    'KidsEmergencies.kid_id' => $data['id']
                ]
            );

            foreach ($input_contacts as $contact) {
                $obj_EmergencyContacts = TableRegistry::get('EmergencyContacts');

                $contact_entity = $obj_EmergencyContacts->find('all', [
                    'conditions' => [
                        'EmergencyContacts.phone_number' => $contact->phone_number,
                        'EmergencyContacts.enabled'      => true
                    ],
                    'contain' => ['EmergencyContactLanguages']
                ])->first();
                if ($contact_entity) {
                    foreach ($contact_entity->emergency_contact_languages as &$contact_lang) {
                        switch ($contact_lang->alias) {
                            case 'en_US':
                                $contact_lang->name = isset($contact->en_US_name) && !empty($contact->en_US_name) ? $contact->en_US_name : $contact_lang->name;
                                break;
                            case 'zh_HK':
                                $contact_lang->name = isset($contact->zh_HK_name) && !empty($contact->zh_HK_name) ? $contact->zh_HK_name : $contact_lang->name;
                                break; 
                            default:
                                break;
                        }
                    }
                    if (!$obj_EmergencyContacts->EmergencyContactLanguages->saveMany($contact_entity->emergency_contact_languages)) {
                        $db->rollback();
                        $message = 'FAIL_SAVE_EMERGENCY_CONTACT';
                        goto set_result;
                    }
                    //save kid emer contact
                    $new_kid_emer_contact = $this->KidsEmergencies->newEmptyEntity();
                    $new_kid_emer_contact->kid_id = $data['id'];
                    $new_kid_emer_contact->emergency_contact_id = $contact_entity->id;
                    $new_kid_emer_contact->relationship_id = $contact->relationship_id;

                    // debug($new_kid_emer_contact);
                    // exit;
                    if (!$this->KidsEmergencies->save($new_kid_emer_contact)) {
                        $db->rollback();
                        $message = 'SAVE_KID_EMERCONTACT_FAIL';
                        goto set_result;
                    }
                } else {
                    $new_contact_entity = $obj_EmergencyContacts->newEmptyEntity();
                    $new_contact_entity->phone_number = $contact->phone_number;
                    if ($model = $obj_EmergencyContacts->save($new_contact_entity)) {
                        // save language emergency contact languages
                        $new_contact_languages = [
                            [
                                'emergency_contact_id'  => $model->id,
                                'alias'                 => 'en_US',
                                'name'                  => $contact->en_US_name
                            ],
                            [
                                'emergency_contact_id'  => $model->id,
                                'alias'                 => 'zh_HK',
                                'name'                  => $contact->zh_HK_name
                            ], 
                        ];
                        $obj_EmerContactLanguages = TableRegistry::get('EmergencyContactLanguages');
                        $new_contact_languages = $obj_EmerContactLanguages->newEntities($new_contact_languages);
                        if (!$obj_EmerContactLanguages->saveMany($new_contact_languages)) {
                            $db->rollback();
                            $message = 'SAVE_DATA_EMERCONTACT_LANGUAGES_FAIL';
                            goto set_result;
                        }

                        //save kid emer contact
                        $new_kid_emer_contact = $this->KidsEmergencies->newEmptyEntity();
                        $new_kid_emer_contact->kid_id = $data['id'];
                        $new_kid_emer_contact->emergency_contact_id = $model->id;
                        $new_kid_emer_contact->relationship_id = $contact->relationship_id;
                        // debug($new_kid_emer_contact);
                        // exit;
                        if (!$this->KidsEmergencies->save($new_kid_emer_contact)) {
                            $db->rollback();
                            $message = 'SAVE_KID_EMERCONTACT_FAIL';
                            goto set_result;
                        }
                    } else {
                        $db->rollback();
                        $message = 'SAVE_EMERCONTACT_FAIL';
                        goto set_result;
                    }
                }
            }
        }

        // 
        $db->commit();
        set_result:
        return [
            'status'    => $status,
            'message'   => $message,
        ];
    }

    public function remove($user_id, $kid_id)
    {
        $status = 200;
        $message = 'DATA_IS_DELETED';
        $params = null;
        $parent = $this->CidcParents->find('all', [
            'fields' => ['CidcParents.id'],
            'conditions' => [
                'CidcParents.user_id' => $user_id
            ]
        ])->first();
        $kid = $this->find('all', [
            'conditions' => [
                'Kids.id' => $kid_id,
                'Kids.cidc_parent_id' => $parent->id,
            ]
        ])->first();
        if (!$kid) {
            $status = 999;
            $message = 'NOT_FOUND_KID';
            goto set_result;
        }
        $this->KidsEmergencies->deleteAll([
            'kid_id' => $kid_id
        ]);
        $this->KidLanguages->deleteAll([
            'kid_id' => $kid_id
        ]);
        $this->KidImages->deleteAll([
            'kid_id' => $kid_id
        ]);
        $this->delete($kid);
        set_result:
        return [
            'status'  => $status,
            'message' => $message,
            'params'  => $params
        ];
    }

    public function add_kid_api($user_id, $data)
    {
        $status = 200;
        $message = __('data_is_saved');
        $params = null;
        $parent = $this->CidcParents->find('all', [
            'conditions' => [
                'CidcParents.user_id' => $user_id
            ]
        ])->first();
        // save kid
        $kid = $this->newEmptyEntity();
        $kid = $this->patchEntity($kid, $data);
        $kid->cidc_parent_id = $parent->id;
        $db = $this->getConnection();
        $db->begin();
        if (!$model = $this->save($kid)) {
            $db->rollback();
            $status = 999;
            $message = "DATA_KID_NOT_SAVED";
            goto set_result;
        }
        // save kid language
        $obj_Languages = TableRegistry::get('Languages');
        $languages = $obj_Languages->find('all');
        $kid_languages = [];
        foreach ($languages as $lang) {
            $kid_languages[] = [
                'kid_id'    => $model->id,
                'alias'     => $lang->alias,
                'name'      => $data["$lang->alias" . "_name"], 
            ];
        }
        $kid_languages = $this->KidLanguages->newEntities($kid_languages);
        if (!$this->KidLanguages->saveMany($kid_languages)) {
            $db->rollback();
            $status = 999;
            $message = 'DATA_KID_LANGUAGE_NOT_SAVED';
            goto set_result;
        }

        //save emer_contact
        $new_kid_emer_contact = [];
        $contacts = json_decode($data['emergency_contacts']);
        foreach ($contacts as $contact) {
            $obj_EmergencyContacts = TableRegistry::get('EmergencyContacts');
            $result_EmergencyContact = $obj_EmergencyContacts->add_with_object($contact);
            $new_kid_emer_contact[] = [
                'kid_id'                    => $model->id,
                'emergency_contact_id'      => $result_EmergencyContact['params']['id'],
                'relationship_id'           => $contact->relationship_id
            ];
        }
        $new_kid_emer_contact  = $this->KidsEmergencies->newEntities($new_kid_emer_contact);
        if (!$this->KidsEmergencies->saveMany($new_kid_emer_contact)) {
            $db->rollback();
            $status = 999;
            $message = 'DATA_KID_EMER_CONTACT_NOT_SAVED';
            goto set_result;
        }
        $db->commit();

        set_result:
        return [
            'status'  => $status,
            'message' => $message,
            'params'  => $model->id
        ];
    }

    public function get_kid_ids($conditions = ['Kids.enabled' => true])
    {
        $temp = $this->find('all', [
            'fields' => ['Kids.id'],
            'conditions' => $conditions,
            'join' => [
                [
                    'table'         => 'cidc_parents',
                    'alias'         => 'CidcParents',
                    'type'          => 'LEFT',
                    'conditions'    => ['Kids.cidc_parent_id = CidcParents.id']
                ],
                [
                    'table'         => 'users',
                    'alias'         => 'Users',
                    'type'          => 'LEFT',
                    'conditions'    => ['Users.id = CidcParents.user_id']
                ]
            ]
        ])->toArray();
        $result = array_map(function ($item) {
            return $item->id;
        }, $temp);
        return $result;
    }

    public function get_kids_classes($language = 'en_US', $kid_ids = [], $user_id = null, $type = 'CURRENT', $kid_id = null)
    {
        $conditions_class = [
            'CidcClasses.status' => TableRegistry::get('CidcClasses')->PUBLISHED,
            'CidcClasses.enabled' => true
        ];
        $conditions_kids = [
            'Kids.id IN'       => $kid_ids,
            'Kids.enabled'  => true
        ];
        if ($kid_id) {
            $conditions_kids['Kids.id'] = $kid_id;
        }
        $temp = $this->find('all', [
            'fields' => [
                'Kids.id',
                'Kids.cidc_parent_id',
                'Kids.gender',
                'Kids.dob',
                'Kids.number_of_siblings',
                'Kids.caretaker',
                'Kids.special_attention_needed',
            ],
            'conditions' => $conditions_kids,
            'contain' => [
                'KidImages' => [],
                'KidLanguages' => [
                    'fields' => [
                        'KidLanguages.id',
                        'KidLanguages.kid_id',
                        'KidLanguages.name', 
                        'KidLanguages.alias',

                    ],
                ],
                'StudentRegisterClasses' => [
                    'conditions' => [
                        'StudentRegisterClasses.status' => MyHelper::PAID
                    ],
                    'CidcClasses' => [
                        'conditions' => $conditions_class,
                        'DateOfLessons' => [
                            'fields' => [
                                'DateOfLessons.day',
                                'DateOfLessons.cidc_class_id',
                            ],
                        ],
                        'Programs' => [
                            'ProgramLanguages' => [
                                'conditions' => [
                                    'ProgramLanguages.alias' => $language
                                ]
                            ]
                        ],
                        'Courses' => [
                            'CourseLanguages' => [
                                'conditions' => [
                                    'CourseLanguages.alias' => $language
                                ]
                            ]
                        ],
                        'Centers' => [
                            'CenterLanguages' => [
                                'conditions' => [
                                    'CenterLanguages.alias' => $language
                                ]
                            ],
                            'Districts' => [
                                'DistrictLanguages' => [
                                    'conditions' => [
                                        'DistrictLanguages.alias' => $language
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]

        ])->toArray();
        $result = [];
        foreach ($temp as $kid) {
            $classes = [];
            if (isset($kid->student_register_classes)) {
                foreach ($kid->student_register_classes as $class) {
                    $min_max_date = TableRegistry::get('StudentAttendedClasses')->get_min_max_date_class_and_kid($class->cidc_class->id, $kid->id);
                    $now = date('Y-m-d');
                    if ($min_max_date) {
                        switch ($type) {
                            case 'CURRENT':
                                if ($now >= $min_max_date->min_date && $now <= $min_max_date->max_date) {
                                    $classes[] = $this->set_response('FORMAT_KID_CLASSES', $class);
                                }
                                break;
                            case 'UPCOMMING':
                                if ($now < $min_max_date->min_date) {
                                    $classes[] = $this->set_response('FORMAT_KID_CLASSES', $class);
                                }
                                break;
                            default;
                                break;
                        }
                    }
                }
            }
            if (!!$classes) {
                $result[] = [
                    'kid'       => $this->set_response('FORMAT_KIDS', $kid),
                    'classes'   => $classes
                ];
            }
        }
        return $result;
    }

    public function web_get_kids_classes($language = 'en_US', $kid_ids = [], $data, $type = 'CURRENT')
    {
        $conditions_class = [
            'CidcClasses.status' => TableRegistry::get('CidcClasses')->PUBLISHED,
            'CidcClasses.enabled' => true
        ];

        $conditions_kid = [
            'Kids.id IN'       => $kid_ids,
            'Kids.enabled'  => true
        ];

        if (isset($data['search']) && !empty($data['search'])) {
            $conditions_kid['LOWER(KidLanguages.name) LIKE'] = '%' . trim(strtolower($data['search'])) . '%';
        }
        $temp = $this->find('all', [
            'fields' => [
                'Kids.id',
                'Kids.cidc_parent_id',
                'Kids.gender',
                'Kids.dob',
                'Kids.number_of_siblings',
                'Kids.caretaker',
                'Kids.special_attention_needed',
            ],
            'conditions' => $conditions_kid,
            'contain' => [
                'KidImages' => [],
                'KidLanguages' => [
                    'fields' => [
                        'KidLanguages.id',
                        'KidLanguages.kid_id',
                        'KidLanguages.name', 
                        'KidLanguages.alias',

                    ],
                ],
                'StudentRegisterClasses' => [
                    'conditions' => [
                        'StudentRegisterClasses.status' => MyHelper::PAID
                    ],
                    'CidcClasses' => [
                        'conditions' => $conditions_class,
                        'DateOfLessons' => [
                            'fields' => [
                                'DateOfLessons.day',
                                'DateOfLessons.cidc_class_id',
                            ],
                        ],
                        'Programs' => [
                            'ProgramLanguages' => [
                                'conditions' => [
                                    'ProgramLanguages.alias' => $language
                                ]
                            ]
                        ],
                        'Courses' => [
                            'CourseLanguages' => [
                                'conditions' => [
                                    'CourseLanguages.alias' => $language
                                ]
                            ]
                        ],
                        'Centers' => [
                            'CenterLanguages' => [
                                'conditions' => [
                                    'CenterLanguages.alias' => $language
                                ]
                            ],
                            'Districts' => [
                                'DistrictLanguages' => [
                                    'conditions' => [
                                        'DistrictLanguages.alias' => $language
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'join' => [
                'table' => 'kid_languages',
                'alias' => 'KidLanguages',
                'type'  => 'LEFT',
                'conditions' => [
                    'Kids.id = KidLanguages.kid_id',
                    'KidLanguages.alias' => $language
                ]
            ]

        ])->toArray();
        $result = [];
        foreach ($temp as $kid) {
            $classes = [];
            if (isset($kid->student_register_classes)) {
                foreach ($kid->student_register_classes as $class) {
                    $min_max_date = TableRegistry::get('StudentAttendedClasses')->get_min_max_date_class_and_kid($class->cidc_class->id, $kid->id);
                    $now = date('Y-m-d');
                    if ($min_max_date) {
                        switch ($type) {
                            case 'CURRENT':
                                if ($now >= $min_max_date->min_date && $now <= $min_max_date->max_date) {
                                    $temp_class = $this->set_response('FORMAT_KID_CLASSES', $class);
                                    $temp_class['order_id'] = $class->order_id;
                                    $classes[] = $temp_class;
                                }
                                break;
                            case 'UPCOMMING':
                                if ($now < $min_max_date->min_date) {
                                    $temp_class = $this->set_response('FORMAT_KID_CLASSES', $class);
                                    $temp_class['order_id'] = $class->order_id;
                                    $classes[] = $temp_class;
                                }
                                break;
                            default;
                                break;
                        }
                    }
                }
            }
            if (!!$classes) {
                $result[] = [
                    'kid'       => $this->set_response('FORMAT_KIDS', $kid),
                    'classes'   => $classes
                ];
            }
        }
        $limit = (int)$data['limit'];
        $offset = ((int)$data['page'] - 1) * $limit;
        return [
            'items' => array_slice($result, $offset, $limit),
            'count' => count($result)
        ];
    }

    public function get_list_by_token($user_id, $language, $payload)
    { 
        $temp = $this->find('all', [
            'fields' => [
                'id'        => 'Kids.id',
                'gender'    => 'Kids.gender',
                'name'      => 'KidLanguages.name', 
                'avatar'    => 'KidImages.path',
            ],
            'conditions' => [],
            'join' => [
                [
                    'table'     => 'cidc_parents',
                    'alias'     => 'CidcParents',
                    'type'      => 'LEFT',
                    'conditions' => [
                        'Kids.cidc_parent_id = CidcParents.id'
                    ],
                ],
                [
                    'table'     => 'users',
                    'alias'     => 'Users',
                    'type'      => 'INNER',
                    'conditions' => [
                        'CidcParents.user_id = Users.id',
                        'Users.id' => $user_id
                    ]
                ],
                [
                    'table'     => 'kid_languages',
                    'alias'     => 'KidLanguages',
                    'type'      => 'LEFT',
                    'conditions' => [
                        'KidLanguages.kid_id = Kids.id',
                        'KidLanguages.alias' => $language
                    ]
                ], [
                    'table'     => 'kid_images',
                    'alias'     => 'KidImages',
                    'type'      => 'LEFT',
                    'conditions' => [
                        'KidImages.kid_id = Kids.id'
                    ]
                ]
            ],
            'page' => (int)$payload['page'],
            'limit' => $payload['limit']
        ]);

        $rel = $temp->toArray();
        $count = $temp->count();

        // format data
        $genders = MyHelper::getGenders();
        foreach ($rel as &$value) {

            // check is study? 
            $is_study = false;
            if (isset($payload['is_registered_class_id']) && !empty($payload['is_registered_class_id'])) {
                $obj_StudentRegisterClasses = TableRegistry::get('StudentRegisterClasses');
                $flag = $obj_StudentRegisterClasses->is_study_class($value->id, $payload['is_registered_class_id']);
                if ($flag) {
                    $is_study = true;
                }
            }

            $value->is_study  = $is_study;     

            if (!empty($value->avatar)) {
                $value->avatar = Router::url('/', true) . $value->avatar;
            }

            if ($value->avatar == null || empty($value->avatar)) {
                $value->avatar = ($value->gender == MyHelper::MALE) ?  Router::url('/', true) . 'img/cidckids/student/boy.svg' : Router::url('/', true) .  'img/cidckids/student/girl.svg';
            }

            $value->gender = $genders[$value->gender];
        } 

        return [
            'count' => $count,
            'items' => $rel
        ];
    }

    public function get_kids_no_register_class($class_id, $language, $current_kid_id = null)
    {
        // return list kids
        $kids = $this->get_list($language)->toArray();

        $obj_StudentRegisterClasses =  TableRegistry::get('StudentRegisterClasses');
        $kids_register = $obj_StudentRegisterClasses->get_kid_register_class($class_id);

        $result_kids = [];
        foreach ($kids as $key => $value) {
            if ($current_kid_id && $key == $current_kid_id) {
                $result_kids[$key] = $value;
            }

            if (!in_array($key,  $kids_register)) {    // not founded
                $result_kids[$key] = $value;
            }
        }

        return $result_kids;
    }

    public function get_kids_register_class($class_id, $language)
    {
        $kids = $this->get_list($language)->toArray();

        $obj_StudentRegisterClasses =  TableRegistry::get('StudentRegisterClasses');
        $kids_register = $obj_StudentRegisterClasses->get_kid_register_class_ui_sick_leave($class_id);

        $result_kids = [];
        foreach ($kids as $key => $value) {
            if (in_array($key,  $kids_register)) {    // not founded
                $result_kids[$key] = $value;
            }
        }

        return $result_kids;
    }

    public function get_list_kid_by_center_id($language = 'en_US', $data, $center_id)
    { 
        $ids = $this->get_kid_ids_by_center_id($center_id);
        if (count($ids) == 0) {
            return [
                'count' => 0,
                'items' => []
            ];
        }

        $order = [];
        $arr_sort = [
            'KidLanguages.name DESC',
            'CidcParentLanguages.name DESC',
            'Kids.dob DESC',
            'Kids.special_attention_needed DESC',
            'KidLanguages.name ASC',
            'CidcParentLanguages.name ASC',
            'Kids.dob ASC',
            'Kids.special_attention_needed ASC',
        ];
        if (isset($data['sort']) && !empty($data['sort'])) {
            $sorts = $data['sort'];
            foreach ($sorts as $sort) {
                if (in_array($sort, $arr_sort)) {
                    $order[] = $sort;
                }
            }
        }
        $conditions = [
            'Kids.id IN' => $ids,
            'Kids.enabled' => true
        ];
        if (isset($data['search']) && !empty($data['search'])) {
            $conditions['OR'] = [
                'LOWER(KidLanguages.name) LIKE' => '%' . trim(strtolower($data['search'])) . '%', 
                'LOWER(CidcParentLanguages.name) LIKE' => '%' . trim(strtolower($data['search'])) . '%',
            ];
        }
        $temp = $this->find('all', [
            'fields' => [
                'id'        => 'Kids.id',
                'gender'    => 'Kids.gender',
                'name'      => 'KidLanguages.name', 
                'dob'       => 'Kids.dob',
                'avatar'    => 'KidImages.path',
                'remark'    => 'Kids.special_attention_needed',
                'parent_name' => 'CidcParentLanguages.name'
            ],
            'conditions' => $conditions,
            'join' => [
                [
                    'table'     => 'kid_languages',
                    'alias'     => 'KidLanguages',
                    'type'      => 'LEFT',
                    'conditions' => [
                        'KidLanguages.kid_id = Kids.id',
                        'KidLanguages.alias' => $language
                    ]
                ],
                [
                    'table'     => 'kid_images',
                    'alias'     => 'KidImages',
                    'type'      => 'LEFT',
                    'conditions' => [
                        'KidImages.kid_id = Kids.id'
                    ]
                ],
                [
                    'table'     => 'cidc_parent_languages',
                    'alias'     => 'CidcParentLanguages',
                    'type'      => 'LEFT',
                    'conditions' => [
                        'CidcParentLanguages.cidc_parent_id = Kids.cidc_parent_id',
                        'CidcParentLanguages.alias' => $language
                    ]
                ]

            ],
            'order' => $order,
            'page' => (int)$data['page'],
            'limit' => $data['limit']
        ])->toArray();

        $count = $this->find('all', [
            'fields' => [
                'id'        => 'Kids.id',
                'gender'    => 'Kids.gender',
                'name'      => 'KidLanguages.name', 
                'dob'       => 'Kids.dob',
                'avatar'    => 'KidImages.path',
                'remark'    => 'Kids.special_attention_needed',
                'parent_name' => 'CidcParentLanguages.name'
            ],
            'conditions' => $conditions,
            'join' => [
                [
                    'table'     => 'kid_languages',
                    'alias'     => 'KidLanguages',
                    'type'      => 'LEFT',
                    'conditions' => [
                        'KidLanguages.kid_id = Kids.id',
                        'KidLanguages.alias' => $language
                    ]
                ],
                [
                    'table'     => 'kid_images',
                    'alias'     => 'KidImages',
                    'type'      => 'LEFT',
                    'conditions' => [
                        'KidImages.kid_id = Kids.id'
                    ]
                ],
                [
                    'table'     => 'cidc_parent_languages',
                    'alias'     => 'CidcParentLanguages',
                    'type'      => 'LEFT',
                    'conditions' => [
                        'CidcParentLanguages.cidc_parent_id = Kids.cidc_parent_id',
                        'CidcParentLanguages.alias' => $language
                    ]
                ]

            ],
        ])->count();

        // format data
        $genders = MyHelper::getGenders();
        foreach ($temp as &$value) {

            if (!empty($value->avatar)) {
                $value->avatar = Router::url('/', true) . $value->avatar;
            }

            if ($value->avatar == null || empty($value->avatar)) {
                $value->avatar = ($value->gender == MyHelper::MALE) ?  Router::url('/', true) . 'img/cidckids/student/boy.svg' : Router::url('/', true) .  'img/cidckids/student/girl.svg';
            }

            $value->gender = $genders[$value->gender];
            $value->dob = $value->dob->format('Y-m-d');
        }


        return [
            'count' => $count,
            'items' => $temp
        ];
    }

    public function staff_get_detail($language, $id)
    {
        $message = "RETRIEVE_DATA_SUCCESSFULLY";
        $url = MyHelper::getUrl();
        $kid  = $this->find('all', [
            'fields' => [
                'Kids.id',
                'Kids.cidc_parent_id',
                'Kids.relationship_id',
                'Kids.gender',
                'Kids.dob',
                'Kids.number_of_siblings',
                'Kids.caretaker',
                'Kids.special_attention_needed',
            ],
            'conditions' => [
                'Kids.id'       => $id,
                'Kids.enabled'  => true
            ],
            'contain' => [
                'KidImages' => [],
                'KidLanguages' => [
                    'fields' => [
                        'KidLanguages.id',
                        'KidLanguages.kid_id',
                        'KidLanguages.name', 
                        'KidLanguages.alias',
                    ], 
                ],
                'Relationships' => [
                    'fields' => [
                        'Relationships.id'
                    ],
                    'RelationshipLanguages' => [
                        'fields' => [
                            'RelationshipLanguages.relationship_id',
                            'RelationshipLanguages.name'
                        ],
                        'conditions' => [
                            'RelationshipLanguages.alias' => $language
                        ]
                    ]
                ],
                'KidsEmergencies' => [
                    'EmergencyContacts' => [
                        'EmergencyContactLanguages' => [
                            // 'conditions' => [
                            //     'EmergencyContactLanguages.alias' => $language
                            // ]
                        ]
                    ],
                    'Relationships' => [
                        'fields' => [
                            'Relationships.id'
                        ],
                        'RelationshipLanguages' => [
                            'fields' => [
                                'RelationshipLanguages.relationship_id',
                                'RelationshipLanguages.name'
                            ],
                            'conditions' => [
                                'RelationshipLanguages.alias' => $language
                            ]
                        ]
                    ]
                ],
                'StudentRegisterClasses' => [
                    'conditions' => [
                        'StudentRegisterClasses.status' => MyHelper::PAID
                    ],
                    'CidcClasses' => [
                        'conditions' => [
                            'CidcClasses.enabled' => true,
                            'CidcClasses.status' => TableRegistry::get('CidcClasses')->PUBLISHED
                        ],
                        'DateOfLessons' => [
                            'fields' => [
                                'DateOfLessons.day',
                                'DateOfLessons.cidc_class_id',
                            ],
                        ],
                        'Programs' => [
                            'ProgramLanguages' => [
                                'conditions' => [
                                    'ProgramLanguages.alias' => $language
                                ]
                            ]
                        ],
                        'Courses' => [
                            'CourseLanguages' => [
                                'conditions' => [
                                    'CourseLanguages.alias' => $language
                                ]
                            ]
                        ],
                        'Centers' => [
                            'CenterLanguages' => [
                                'conditions' => [
                                    'CenterLanguages.alias' => $language
                                ]
                            ],
                            'Districts' => [
                                'DistrictLanguages' => [
                                    'conditions' => [
                                        'DistrictLanguages.alias' => $language
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]

        ])->first();


        $registerd_classes = [
            'past'      => [],
            'upcoming'  => [],
            'current'   => []
        ];
        $obj_StudentAttendedClasses = TableRegistry::get('StudentAttendedClasses');
        if (isset($kid->student_register_classes)) {
            foreach ($kid->student_register_classes as $class) {
                $min_max_date = $obj_StudentAttendedClasses->get_min_max_date_class_and_kid($class->cidc_class_id, $id);
                if ($min_max_date) {
                    $now = date('Y-m-d');
                    $end_date = $min_max_date->max_date;
                    $start_date = $min_max_date->min_date;
                    $now = date('Y-m-d');
                    if ($end_date < $now) {
                        $registerd_classes['past'][] = $this->set_response('FORMAT_KID_CLASSES', $class);
                    } elseif ($start_date > $now) {
                        $registerd_classes['upcoming'][] = $this->set_response('FORMAT_KID_CLASSES', $class);
                    } elseif ($start_date <= $now && $now <= $end_date) {
                        $registerd_classes['current'][] = $this->set_response('FORMAT_KID_CLASSES', $class);
                    }
                }
            }
        }
        $contacts  = [];
        if (isset($kid->kids_emergencies)) {
            foreach ($kid->kids_emergencies as $emer) {
                $contacts[] = $this->set_response('FORAMT_EMERGENCY_CONTACTS', $emer);
            }
        }
        $result = $this->set_response('FORMAT_KIDS', $kid);
        $result['emergency_contacts'] = $contacts;
        $result['classes'] = $registerd_classes;
        $result['relationship'] = $this->set_response('FORMAT_RELATIONSHIP', $kid);
        return $result;
    }

    public function get_kid_ids_by_center_id($center_id)
    {
        $kid_ids  = $this->StudentRegisterClasses->find('all', [
            'fields' => [
                'id' => 'DISTINCT StudentRegisterClasses.kid_id'
            ],
            'conditions' => [
                'Centers.id' => $center_id,
                'StudentRegisterClasses.status' => MyHelper::PAID
            ],
            'join' => [
                [
                    'table' => 'cidc_classes',
                    'alias' => 'CidcClasses',
                    'type'  => 'INNER',
                    'conditions' => [
                        'CidcClasses.id = StudentRegisterClasses.cidc_class_id',
                        'CidcClasses.enabled' => true
                    ],
                ],
                [
                    'table' => 'centers',
                    'alias' => 'Centers',
                    'type'  => 'LEFT',
                    'conditions' => [
                        'Centers.id = CidcClasses.center_id',
                    ]
                ]
            ]
        ])->toArray();

        $ids = array_map(function ($item) {
            return $item->id;
        }, $kid_ids);
        return $ids;
    }

    public function get_kid_info($id, $language)
    {
        $temp = $this->find('all', [
            'conditions' => [
                'Kids.id' => $id,
            ],
            'fields' => [
                'Kids.id',
                'Kids.cidc_parent_id',
            ],
            'contain' => [
                'KidLanguages' => [
                    'fields' => [
                        'KidLanguages.kid_id',
                        'KidLanguages.name', 
                    ],
                    'conditions' => [
                        'KidLanguages.alias' => $language,
                    ]
                ],
            ]
        ])->first();

        return $temp;
    }

    public function format_kid_info($kid_infos)
    {
        return $kid_infos->kid_languages[0]->name;
    }

    public function staff_get_kids_attended($cidc_class_id, $date, $language = 'en_US')
    {
        $obj_Attended = TableRegistry::get('StudentAttendedClasses');
        $items = $obj_Attended->find('all', [
            'fields' => [
                'StudentAttendedClasses.kid_id'
            ],
            'conditions' => [
                'StudentAttendedClasses.cidc_class_id' => $cidc_class_id,
                'StudentAttendedClasses.date' => $date,
                'StudentAttendedClasses.is_completed' => 0,
            ]
        ])->toArray();

        $kid_ids = array_map(function ($class) {
            return $class->kid_id;
        }, $items);

        $_results = $this->find('all', [
            'fields' => [
                'id'        => 'Kids.id',
                'gender'    => 'Kids.gender',
                'name'      => 'KidLanguages.name',
                'path'      => 'KidImages.path'
            ],
            'conditions' => [
                'Kids.id IN' => $kid_ids,
                'Kids.enabled' => true
            ],
            'join' => [
                [
                    'table' => 'kid_languages',
                    'alias' => 'KidLanguages',
                    'type'  => 'LEFT',
                    'conditions' => [
                        'KidLanguages.kid_id = Kids.id',
                        'KidLanguages.alias' => $language,
                    ]
                ],
                [
                    'table' => 'kid_images',
                    'alias' => 'KidImages',
                    'type'  => 'LEFT',
                    'conditions' => [
                        'KidImages.kid_id = Kids.id'
                    ]
                ]
            ]
        ])->toArray();

        $result = [];
        $url = MyHelper::getUrl();
        foreach ($_results as $item) {
            $avatar = "";
            if (!empty($item->path)) {
                $avatar = $url . $item->path;
            }

            if (empty($item->path) || !isset($item->path)) {
                $avatar = ($item->gender == MyHelper::MALE) ?  $url . 'img/cidckids/student/boy.svg' : $url .  'img/cidckids/student/girl.svg';
            }
            $result[] = [
                'id' => $item->id,
                'name' => $item->name,
                'avatar' => $avatar
            ];
        }
        return $result;
    }

    public function get_detail_kid_by_id($id, $language) {
 
        $temp = $this->find('all', [
            'conditions' => [
                'Kids.id' => $id, 
            ], 
            'contain' => [
                'KidLanguages' => [
                    'fields' => [
                        'KidLanguages.kid_id', 
                        'KidLanguages.name',
                    ],
                    'conditions' => [
                        'KidLanguages.alias' => $language,
                    ],
                ],
                'KidImages' => [
                    'fields' => [
                        'KidImages.kid_id',  
                        'KidImages.path',  
                    ],
                ],
            ], 
        ])->first();


        return $temp;
    }

    /** 
     * @param number $gender gender of kids
     * @param string $path path of image kids
     * @return string $path full path avatar of kids
    */
    public function get_default_avatar($gender, $path) {
        if (!$path) {   // = null => show default by gender
            if ($gender == MyHelper::MALE) {
                $path = Router::url('/', true) . 'img/cidckids/student/boy.svg';

            } elseif ($gender == MyHelper::FEMALE) {
                $path = Router::url('/', true) . 'img/cidckids/student/girl.svg';

            }
        } 
        return $path; 
    }
 
    public function get_kid_belong_to_parent($parent_id) { 
        $temp = $this->find('all', [
            'conditions' => [
                'Kids.cidc_parent_id' => $parent_id,
            ],
        ])->toArray();

        $kids = [];
        foreach ($temp as $kid) {
            $kids[] = $kid->id;
        }
        return $kids;
    }
}

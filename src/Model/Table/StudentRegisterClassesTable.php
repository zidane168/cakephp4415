<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\MyHelper\MyHelper;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Routing\Router;

/**
 * StudentRegisterClasses Model
 *
 * @property \App\Model\Table\CidcClassesTable&\Cake\ORM\Association\BelongsTo $CidcClasses
 * @property \App\Model\Table\KidsTable&\Cake\ORM\Association\BelongsTo $Kids
 *
 * @method \App\Model\Entity\StudentRegisterClass newEmptyEntity()
 * @method \App\Model\Entity\StudentRegisterClass newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\StudentRegisterClass[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\StudentRegisterClass get($primaryKey, $options = [])
 * @method \App\Model\Entity\StudentRegisterClass findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\StudentRegisterClass patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\StudentRegisterClass[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\StudentRegisterClass|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StudentRegisterClass saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StudentRegisterClass[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\StudentRegisterClass[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\StudentRegisterClass[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\StudentRegisterClass[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class StudentRegisterClassesTable extends Table
{
    public const PAID = 1;
    public const UNPAID = 0;
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('student_register_classes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('CidcClasses', [
            'foreignKey' => 'cidc_class_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Kids', [
            'foreignKey' => 'kid_id',
            'joinType' => 'INNER',
        ]);   
        $this->belongsTo('Orders', [
            'foreignKey' => 'order_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('StudentRegisterClassReceipts', [
            'foreignKey' => 'student_register_class_id'
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
        $rules->add($rules->existsIn(['cidc_class_id'], 'CidcClasses'), ['errorField' => 'cidc_class_id']);
        $rules->add($rules->existsIn(['kid_id'], 'Kids'), ['errorField' => 'kid_id']);

        return $rules;
    }

    public function get_status()
    {
        return [
            self::PAID => __d('cidcclass', 'paid'),
            self::UNPAID => __d('cidcclass', 'un_paid'),
        ];
    }

    public function set_order($payload, $language = 'en_US')
    { 
        $db = $this->getConnection();
        $db->begin();

        $cidc_class_id  = $payload['cidc_class_id'];
        $kid_id         = $payload['kid_id'];

        $obj_CidcClasses = TableRegistry::get('CidcClasses');
        $result_CidcClasses = $obj_CidcClasses->get_info_by_id($cidc_class_id);

        if (!$result_CidcClasses) {
            return [
                'status' => 500,
                'message' => __d('cidcclass', 'cannot_get_class'),
                'params' => null
            ];
        }

        // check duplicate
        $query = $this->find('all', [
            'conditions' => [
                'StudentRegisterClasses.kid_id'        => $kid_id,
                'StudentRegisterClasses.cidc_class_id' => $cidc_class_id,
            ],
        ]);
        if ($query->count() > 0) {
            // exist
            return [
                'status' => 500,
                'message' => __d('cidcclass', 'this_kid_had_registered_this_class'),
                'params' => null
            ];
        }

        $obj_CidcClasses = TableRegistry::get('CidcClasses');
        $data_CidcClasses = $obj_CidcClasses->get($cidc_class_id);
        if (   $data_CidcClasses->number_of_register >= $data_CidcClasses->maximum_of_students  ) {
            return [
                'status' => 500,
                'message' => __d('cidcclass', 'full_slot_already'),
                'params' => null
            ];
        }

        $data = [
            'kid_id'        => $kid_id,
            'cidc_class_id' => $cidc_class_id,
            'fee'           => $result_CidcClasses->fee,
            'order_id'      => $this->gen_order_id($cidc_class_id, $kid_id)
        ];

        $entity = $this->newEntity($data);
        if ($this->save($entity)) {
            // update number_of_register to Cidc Classes
            $data_CidcClasses->number_of_register = $data_CidcClasses->number_of_register + 1;
            if (!$obj_CidcClasses->save($data_CidcClasses)) {
                $db->rollback();
                return [
                    'status' => 500,
                    'message' => __('data_is_not_saved') . ' CidcClasses',
                    'params' => null
                ];
            }

            $db->commit();
            $class = $this->CidcClasses->get_detail_class_by_id($cidc_class_id, $language);
            unset($class['kids']);
            $obj_Kids = TableRegistry::get('Kids');
            $kid = $obj_Kids->find('all', [
                'fields' => [
                    'id'        => 'Kids.id',
                    'gender'    => 'Kids.gender',
                    'name'      => 'KidLanguages.name', 
                    'avatar'    => 'KidImages.path'

                ],
                'conditions' => [
                    'Kids.id' => $kid_id
                ],
                'join' => [
                    [
                        'table'         => 'kid_languages',
                        'alias'         => 'KidLanguages',
                        'type'          => 'LEFT',
                        'conditions'    => [
                            'KidLanguages.kid_id = Kids.id',
                            'KidLanguages.alias' => $language
                        ]
                    ],
                    [
                        'table'         => 'kid_images',
                        'alias'         => 'KidImages',
                        'type'          => 'LEFT',
                        'conditions'    => ['KidImages.kid_id = Kids.id']
                    ]
                ]
            ])->first();
            $url = MyHelper::getUrl();
            $genders = MyHelper::getGenders();
            $kid['gender'] = $genders[$kid['gender']];
            if ($kid['avatar'] == null  || empty($kid['avatar'])) {
                $kid['avatar'] = $kid['gender'] == MyHelper::MALE ? $url . 'img/cidckids/student/boy.svg' : $url . 'img/cidckids/student/girl.svg';
            } else {
                $kid['avatar'] = $url . $kid['avatar'];
            }

            return [
                'status' => 200,    // note: just 200 will succeed, another will failed
                'message' => __('data_is_saved'),
                'params'  => [
                    'class'     => $class,
                    'kid'       => $kid,
                    'order_id'  => $data['order_id']
                ]
            ];
        } else {

            $db->rollback();
            return [
                'status' => 500,
                'message' => __('data_is_not_saved') . ' Student Register Classes',
                'params' => null
            ];
        }
    }

    public function get_kids_by_class_id($id)
    {
        return $this->find('all', [
            'conditions' => [
                'StudentRegisterClasses.cidc_class_id' => $id,
                'StudentRegisterClasses.is_attended' => 0,      // get kids not yet attended this class
            ],
            'fields' => [
                'StudentRegisterClasses.id',
                'StudentRegisterClasses.kid_id',
                'StudentRegisterClasses.is_attended',
                'StudentRegisterClasses.created',
            ],
        ])->toArray();
    }

    public function gen_order_id($class_id, $kid_id)
    {
        return $class_id . "-" . $kid_id . "-" . rand(0000000000, 9999999999);
    }

    public function get_detail_order($order_id, $language = 'en_US')
    {
        $order = $this->find('all', [
            'fields' => [
                'StudentRegisterClasses.cidc_class_id',
                'StudentRegisterClasses.kid_id',
                'StudentRegisterClasses.created',
            ],
            'conditions' => [
                'StudentRegisterClasses.order_id' => $order_id
            ]
        ])->first();
        if (!$order) {
            return [
                'status'    => 500,
                'message'   => __('not_found'),
                'params'    => null
            ];
        }
        $class = $this->CidcClasses->get_detail_class_by_id($order->cidc_class_id, $language);
        unset($class['kids']);

        $obj_Kids = TableRegistry::get('Kids');
        $kid = $obj_Kids->find('all', [
            'fields' => [
                'id'        => 'Kids.id',
                'gender'    => 'Kids.gender',
                'name'      => 'KidLanguages.name', 
                'avatar'    => 'KidImages.path'

            ],
            'conditions' => [
                'Kids.id' => $order->kid_id
            ],
            'join' => [
                [
                    'table'         => 'kid_languages',
                    'alias'         => 'KidLanguages',
                    'type'          => 'LEFT',
                    'conditions'    => [
                        'KidLanguages.kid_id = Kids.id',
                        'KidLanguages.alias' => $language
                    ]
                ],
                [
                    'table'         => 'kid_images',
                    'alias'         => 'KidImages',
                    'type'          => 'LEFT',
                    'conditions'    => ['KidImages.kid_id = Kids.id']
                ]
            ]
        ])->first();
        $url = MyHelper::getUrl();
        $genders = MyHelper::getGenders();
        $kid['gender'] = $genders[$kid['gender']];
        if ($kid['avatar'] == null  || empty($kid['avatar'])) {
            $kid['avatar'] = $kid['gender'] == MyHelper::MALE ? $url . 'img/cidckids/student/boy.svg' : $url . 'img/cidckids/student/girl.svg';
        } else {
            $kid['avatar'] = $url . $kid['avatar'];
        }
        return [
            'status' => 200,    // note: just 200 will succeed, another will failed
            'message' => __('retrieve_data_successfully'),
            'params'  => [
                'class'     => $class,
                'kid'       => $kid,
                'order_id'  => $order_id,
                'date'      => $order->created->format('Y-m-d')
            ]
        ];
    }

    public function get_detail_date_order($order_id, $language = 'en_US')
    {
        $order = $this->find('all', [
            'fields' => [
                'StudentRegisterClasses.cidc_class_id',
                'StudentRegisterClasses.kid_id',
                'StudentRegisterClasses.created',
            ],
            'conditions' => [
                'StudentRegisterClasses.order_id' => $order_id
            ]
        ])->first();
        if (!$order) {
            return [
                'status'    => 500,
                'message'   => __('not_found'),
                'params'    => null
            ];
        }
        $class = $this->CidcClasses->get_detail_class_by_id($order->cidc_class_id, $language);
        $list_dates = $this->CidcClasses->get_list_date_by_kid_class($order['kid_id'], $order['cidc_class_id']);

        $class['list_dates'] = null;
        if ($list_dates) {
            $class['list_dates'] = $list_dates['dates'];
        }
        unset($class['kids']);

        $obj_Kids = TableRegistry::get('Kids');
        $kid = $obj_Kids->find('all', [
            'fields' => [
                'id'        => 'Kids.id',
                'gender'    => 'Kids.gender',
                'name'      => 'KidLanguages.name', 
                'avatar'    => 'KidImages.path'

            ],
            'conditions' => [
                'Kids.id' => $order->kid_id
            ],
            'join' => [
                [
                    'table'         => 'kid_languages',
                    'alias'         => 'KidLanguages',
                    'type'          => 'LEFT',
                    'conditions'    => [
                        'KidLanguages.kid_id = Kids.id',
                        'KidLanguages.alias' => $language
                    ]
                ],
                [
                    'table'         => 'kid_images',
                    'alias'         => 'KidImages',
                    'type'          => 'LEFT',
                    'conditions'    => ['KidImages.kid_id = Kids.id']
                ]
            ]
        ])->first();
        $url = MyHelper::getUrl();
        $genders = MyHelper::getGenders();
        $kid['gender'] = $genders[$kid['gender']];
        if ($kid['avatar'] == null  || empty($kid['avatar'])) {
            $kid['avatar'] = $kid['gender'] == MyHelper::MALE ? $url . 'img/cidckids/student/boy.svg' : $url . 'img/cidckids/student/girl.svg';
        } else {
            $kid['avatar'] = $url . $kid['avatar'];
        }
        return [
            'status' => 200,    // note: just 200 will succeed, another will failed
            'message' => __('retrieve_data_successfully'),
            'params'  => [
                'class'     => $class,
                'kid'       => $kid,
                'order_id'  => $order_id,
                'date'      => $order->created->format('Y-m-d')
            ]
        ];
    }

    public function get_list_student_by_class($payload, $language = 'en_US')
    {
        $cidc_class = TableRegistry::get('CidcClasses')->get_detail_class_by_id($payload['cidc_class_id'], $language);
        $class_detail = [
            'id' => $cidc_class['id'],
            'number_of_register'        => $cidc_class['number_of_register'],
            'code'                      => $cidc_class['code'],
            'name'                      => $cidc_class['name'],
            'class_type'                => $cidc_class['class_type_id'] === MyHelper::CIRCULAR ? __d('cidcclass', 'circular')  : __d('cidcclass', 'non_circular'),

            'start_date'    => $cidc_class['start_date'],
            'end_date'      => $cidc_class['end_date'],
            'start_time'    => $cidc_class['start_time'],
            'end_time'      => $cidc_class['end_time'],
        ];
        $_kid_ids = $this->find('all', [
            'fields' => ['StudentRegisterClasses.kid_id'],
            'conditions' => [
                'StudentRegisterClasses.enabled' => true,
                'StudentRegisterClasses.cidc_class_id' => $payload['cidc_class_id']
            ],
        ])->toArray();
        $kid_ids = array_map(function ($item) {
            return $item->kid_id;
        }, $_kid_ids);

        $result = [];
        if (!$kid_ids) {
            return [
                'class_detail' => $class_detail,
                'list_dates' => [],
                'kid_info'  => [],
            ];
        }
        $obj_Kids = TableRegistry::get('Kids');
        $kids = $obj_Kids->find('all', [
            'conditions' => [
                'Kids.id IN' => $kid_ids,
                'Kids.enabled' => true
            ],
            'contain' => [
                'StudentAttendedClasses' => [
                    'conditions' => [
                        'StudentAttendedClasses.is_completed' => 0,
                        'StudentAttendedClasses.cidc_class_id' => $payload['cidc_class_id']
                    ],
                ],
                'KidLanguages' => [
                    'conditions' => [
                        'KidLanguages.alias' => $language,
                    ]
                ],
                'KidImages' => []
            ],

        ])->toArray();

        $list_dates = [];
        $first = true;
        $url = MyHelper::getUrl();
        foreach ($kids as $kid) {

            if (isset($kid->student_attended_classes) && !empty($kid->student_attended_classes)) {

                if ($first) {

                    $first  = false;
                }
                $avatar = "";
                if (!empty($kid->kid_images)) {
                    $avatar = $url . $kid->kid_images[0]->path;
                }

                if (empty($kid->kid_images) || !isset($kid->kid_images)) {
                    $avatar = ($kid->gender == MyHelper::MALE) ?  $url . 'img/cidckids/student/boy.svg' : $url .  'img/cidckids/student/girl.svg';
                }
                $kid_info = [
                    'kid' => [
                        'id'    => $kid->id,
                        'name'  => $kid->kid_languages[0]->name,
                        'avatar' => $avatar
                    ],
                    'attendance' => []
                ];

                foreach ($kid->student_attended_classes as $date) {
                    $kid_info['attendance'][] = [
                        'date'   => $date->date->format('Y-m-d'),
                        'status' => $date->status
                    ];
                    $list_dates[] = $date->date->format('Y-m-d');
                }
                $result[] = $kid_info;
            }
        }
        $list_dates = array_unique($list_dates);
        asort($list_dates);

        $result_list_dates = array_values($list_dates);
        return [
            'class_detail' => $class_detail,
            'list_dates' => $result_list_dates,
            'kid_info'  => $result,
        ];
    }

    public function get_kid_register_class($class_id)
    {
        $temp =  $this->find("all", [
            'fields' => [
                'StudentRegisterClasses.id',
                'StudentRegisterClasses.kid_id',
            ],
        ])->where([
            'StudentRegisterClasses.cidc_class_id' => $class_id,
        ])->toArray();

        // format
        $result = [];
        foreach ($temp as $value) {
            $result[] = $value->kid_id;
        }
        return $result;
    }

    public function get_kid_register_class_ui_sick_leave($class_id)
    {
        $temp =  $this->find("all", [
            'fields' => [
                'StudentRegisterClasses.id',
                'StudentRegisterClasses.kid_id',
            ],
        ])->where([
            'StudentRegisterClasses.cidc_class_id' => $class_id,
            'StudentRegisterClasses.status' => MyHelper::PAID
        ])->toArray();

        // format
        $result = [];
        foreach ($temp as $value) {
            $result[] = $value->kid_id;
        }
        return $result;
    }

    public function set_cart_order($cart_json, $language)
    {   
        $status = 0;
        $message = ""; 
        $params = (object)array();

        $obj_CidcClasses = TableRegistry::get('CidcClasses'); 
        
        $total_fee = 0;
        $carts = json_decode($cart_json, true);

        foreach ($carts as &$value) {
            $cidc_class_id  = $value['cidc_class_id'];
            $kid_id         = $value['kid_id'];

            $result_CidcClasses = $obj_CidcClasses->get_info_by_id($cidc_class_id);

            if (!$result_CidcClasses) { 
                $status = 500;
                $message = __d('cidcclass', 'invalid_class_name', $cidc_class_id);
                goto return_api;
            } 

            // check duplicate
            $query = $this->find('all', [
                'fields' => [
                    'StudentRegisterClasses.cidc_class_id', 
                ],
                'conditions' => [
                    'StudentRegisterClasses.kid_id'        => $kid_id,
                    'StudentRegisterClasses.cidc_class_id' => $cidc_class_id,
                ],
                'contain' => [
                    'Kids' => [
                        'fields' => [
                            'Kids.id',
                        ],  
                        'KidLanguages' => [
                            'fields' => [
                                'KidLanguages.kid_id',
                                'KidLanguages.name',
                            ],  
                            'conditions' => [
                                'KidLanguages.alias' => $language, 
                            ],  
                        ],
                    ],
                    'CidcClasses' => [
                        'fields' => [
                            'CidcClasses.name', 
                        ],  
                    ],
                ],
            ]);
            if ($query->count() > 0) {  // exist 
                $status = 500;
                $rel = $query->first();
                $message = __d('cidcclass', 'exist_kid_had_registered_class', $rel->kid->kid_languages[0]->name, $rel->cidc_class->name);
                goto return_api; 
            } 

            $data_CidcClasses = $obj_CidcClasses->get($cidc_class_id);
            if ($data_CidcClasses->number_of_register >= $data_CidcClasses->maximum_of_students) { 
                $status = 500;
                $message = __d('cidcclass', 'full_slot_already_current_slot_status', $data_CidcClasses->number_of_register, $data_CidcClasses->maximum_of_students);
                goto return_api; 
            } 
 
            $data_CidcClasses->number_of_register = $data_CidcClasses->number_of_register + 1; 
            if (!$obj_CidcClasses->save($data_CidcClasses)) { 
                $status = 500;
                $message = __('data_is_not_saved') . ' CidcClasses';
                goto return_api;  
            } 

            $value['fee'] = $result_CidcClasses->fee; 
            $total_fee += $value['fee'];
        } 

        $obj_Order = TableRegistry::get('Orders');
        $result = $obj_Order->create_order($carts, $total_fee);
        
        $status = $result['status'];
        $message = $result['message'];
        $params = $result['params'];
        
        return_api:
        return [
            'status'    => $status,
            'message'   => $message,
            'params'    => $params, 
        ];
    }
 
    /**
     * validate cart when user click on cart, will show the error message when this kid had already register this class or full class information
     */
    public function is_validate_cart($cart_json, $language)
    {   
        $status = 200; 
        $message = __('retrieve_data_successfully');
        $params = (object)array();

        $obj_Kids           = TableRegistry::get('Kids'); 
        $obj_CidcClasses    = TableRegistry::get('CidcClasses'); 
        
        $total_fee_decimal = $total_selected_quantity = 0;
        $carts = json_decode($cart_json, true);
       
        $items = []; 
  
        foreach ($carts as $value) { 
            
            if (!isset($value['selected'])) {   
                $status = 500;
                $message = __('missing_parameter') . ' selected';
                goto return_api;
            }

            $error_message = "";
            $cidc_class_id  = $value['cidc_class_id'];
            $kid_id         = $value['kid_id'];

            $result_CidcClasses = $obj_CidcClasses->get_info_by_id($cidc_class_id);

            if (!$result_CidcClasses) {   
                $status = 500;
                $message = __d('cidcclass', 'invalid_class_name', $cidc_class_id);
                goto return_api;
            }  

            // Check duplicate
            $query = $this->find('all', [
                'fields' => [
                    'StudentRegisterClasses.cidc_class_id', 
                ],
                'conditions' => [
                    'StudentRegisterClasses.kid_id'        => $kid_id,
                    'StudentRegisterClasses.cidc_class_id' => $cidc_class_id,
                ],
                'contain' => [
                    'Kids' => [
                        'fields' => [
                            'Kids.id',
                            'Kids.gender',
                        ],  
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
                    'CidcClasses' => [
                        'fields' => [
                            'CidcClasses.id', 
                            'CidcClasses.fee', 
                            'CidcClasses.name', 
                            'CidcClasses.center_id', 
                            'CidcClasses.program_id', 
                            'CidcClasses.course_id',
                            'CidcClasses.class_type_id',
                        ],  
                        'ClassTypes' => [
                            'fields' => [
                                'ClassTypes.id', 
                            ],  
                            'ClassTypeLanguages' => [
                                'fields' => [
                                    'ClassTypeLanguages.class_type_id', 
                                    'ClassTypeLanguages.name',
                                ],  
                                'conditions' => [
                                    'ClassTypeLanguages.alias' => $language, 
                                ],  
                            ],
                        ],
                        'Centers' => [
                            'fields' => [
                                'Centers.id', 
                            ],  
                            'CenterLanguages' => [
                                'fields' => [
                                    'CenterLanguages.center_id',
                                    'CenterLanguages.name',
                                ],
                                'conditions' => [
                                    'CenterLanguages.alias' => $language, 
                                ],  
                            ],
                        ],
                        'Programs' => [
                            'fields' => [
                                'Programs.id', 
                            ],  
                            'ProgramLanguages' => [
                                'fields' => [
                                    'ProgramLanguages.program_id',
                                    'ProgramLanguages.name',
                                ],
                                'conditions' => [
                                    'ProgramLanguages.alias' => $language, 
                                ],  
                            ],
                            'ProgramImages' => [
                                'fields' => [
                                    'ProgramImages.program_id',
                                    'ProgramImages.path'
                                ],
                            ],
                        ],
                        'Courses' => [
                            'fields' => [
                                'Courses.id', 
                            ],   
                            'CourseLanguages' => [
                                'fields' => [
                                    'CourseLanguages.course_id',
                                    'CourseLanguages.name',
                                ],
                                'conditions' => [
                                    'CourseLanguages.alias' => $language, 
                                ],  
                            ] 
                        ],
                    ],
                ],
            ]);
            if ($query->count() > 0) {  // exist 
                $status = 500;
                $rel = $query->first();
                $error_message = __d('cidcclass', 'exist_kid_had_registered_class', $rel->kid->kid_languages[0]->name, $rel->cidc_class->name);
            }   

            $data_CidcClasses = $obj_CidcClasses->get($cidc_class_id);
            if ($data_CidcClasses->number_of_register >= $data_CidcClasses->maximum_of_students) { 
                $status = 500;
                $message = __d('cidcclass', 'full_slot_already_current_slot_status', $data_CidcClasses->name, $data_CidcClasses->number_of_register, $data_CidcClasses->maximum_of_students);
            }    

            $rel = $query->first(); 

            $k_id = $k_name = $k_avatar = null;
            $class_id = $class_fee_decimal = $class_name = null; 
            $center_id = $center_name = null; 
            $program_id = $program_name = $program_image = null; 
            $course_id = $course_name = null; 
            $class_type_id = $class_type_name = null;
            $selected = $value['selected']; 

            if (!$rel) {    // don't exist
                // get kid, get class info 

                $result_Kid     = $obj_Kids->get_detail_kid_by_id($kid_id, $language); 
                $result_Class   = $obj_CidcClasses->get_detail_class_by_id($cidc_class_id, $language);  
            
                if (!$result_Kid || !$result_Class) {
                    continue;
                }

                $path = !empty($result_Kid->kid_images) ? $result_Kid->kid_images[0]->path : null;
                $avatar = $obj_Kids->get_default_avatar($result_Kid->gender, $path);  
  
                $k_id = $result_Kid->id; 
                $k_name = $result_Kid->kid_languages[0]->name;
                $k_avatar = $avatar; 
 
                $class_id = $result_Class['id'];
                $class_fee_decimal = $result_Class['fee_decimal'];
                $class_name = $result_Class['name'];

                $center_id = $result_Class['center']['id'];
                $center_name = $result_Class['center']['name'];

                $program_id = $result_Class['program']['id'];
                $program_name =  $result_Class['program']['name']; 
                $program_image =  $result_Class['program']['image']; 

                $course_id = $result_Class['course']['id'];
                $course_name = $result_Class['course']['name']; 

                $class_type_id = $result_Class['class_type']['id'];
                $class_type_name = $result_Class['class_type']['name']; 

 
            } else {

                $path = !empty($rel->kid->kid_images) ? $rel->kid->kid_images[0]->path : null;
                $avatar = $obj_Kids->get_default_avatar($rel->kid->gender, $path); 
               
                $k_id = $rel->kid->id;
                $k_name = $rel->kid->kid_languages[0]->name;
                $k_avatar = $obj_Kids->get_default_avatar($rel->kid->gender, $path); 

                $class_id = $rel->cidc_class->id;
                $class_fee_decimal = $rel->cidc_class->fee;
                $class_name =  $rel->cidc_class->name;

                $center_id = $rel->cidc_class->center_id;
                $center_name = $rel->cidc_class->center->center_languages[0]->name;

                $program_id = $rel->cidc_class->program_id;
                $program_name = $rel->cidc_class->program->program_languages[0]->name;
                $program_image = !empty($rel->cidc_class->program->program_images) ? Router::url('/', true) . $rel->cidc_class->program->program_images[0]->path : null;

                $course_id = $rel->cidc_class->course_id;
                $course_name = $rel->cidc_class->course->course_languages[0]->name;

                $class_type_id = $rel->cidc_class->class_type_id;
                $class_type_name = ($rel->cidc_class->class_type_id == MyHelper::CIRCULAR) ?  __d('cidcclass', 'circular') : __d('cidcclass', 'non_circular');
            } 
 
            if (!empty($error_message)) { // show message error for this child had already registered
                $selected = 0;  // disabled = 0;
                $message = "";
            } 

            // just sum when checked
            if ($selected != 0) {
                $total_fee_decimal += $result_CidcClasses->fee;
                $total_selected_quantity += $selected;    
            }  
            
            $items[$k_id]['kid'] = [
                'id'    => $k_id,
                'name'  => $k_name,
                'avatar' => $k_avatar,
            ]; 

            $items[$k_id]['classes'][] = [
                'id'            => $class_id,
                'fee_decimal'   => $class_fee_decimal,
                'name'          => $class_name,
                'center' => [
                    'id' => $center_id,
                    'name' => $center_name,
                ],
                'program' => [
                    'id' => $program_id,
                    'name' => $program_name,
                    'image' => $program_image, 
                ],
                'course' => [
                    'id' => $course_id,
                    'name' => $course_name,
                ],
                'class_type' => [
                    'id' => $class_type_id,
                    'name' => $class_type_name,
                ],
                'selected' => $selected,
                'error_message' => $error_message,
            ];  
        }  

        $final_result = [
            'total_fee_decimal' => $total_fee_decimal,
            'total_quantity' => count($carts),
            'total_selected_quantity' => $total_selected_quantity,
        ];     

        foreach ($items as $value) {
            $final_result['items'][] = $value;
        }  
        $params = $final_result;

        return_api:
        return [
            'status'    => $status,
            'message'   => $message,
            'params'    => $params,
        ];
    }

    public function set_order_v2($payload)
    {    
        $cidc_class_id  = $payload['cidc_class_id'];
        $kid_id         = $payload['kid_id'];

        $params = (object)array();
        $obj_CidcClasses = TableRegistry::get('CidcClasses');
        $result_CidcClasses = $obj_CidcClasses->get_info_by_id($cidc_class_id);

        if (!$result_CidcClasses) { 
            $status = 500;
            $message =  __d('cidcclass', 'cannot_get_class');
            goto return_api;   
        }

        // check duplicate
        $query = $this->find('all', [
            'conditions' => [
                'StudentRegisterClasses.kid_id'        => $kid_id,
                'StudentRegisterClasses.cidc_class_id' => $cidc_class_id,
            ],
        ]);
        if ($query->count() > 0) {
            // exist 
            $status = 500;
            $message =  __d('cidcclass', 'this_kid_had_registered_this_class');
            goto return_api;  
        }

        $obj_CidcClasses = TableRegistry::get('CidcClasses');
        $data_CidcClasses = $obj_CidcClasses->get($cidc_class_id);
        if ($data_CidcClasses->number_of_register >= $data_CidcClasses->maximum_of_students) { 
            $status = 500;
            $message = __d('cidcclass', 'full_slot_already');
            goto return_api;   
        }  

        $data_CidcClasses->number_of_register = $data_CidcClasses->number_of_register + 1; 
        if (!$obj_CidcClasses->save($data_CidcClasses)) {  
            $status = 500;
            $message = __('data_is_not_saved') . ' CidcClasses';
            goto return_api;  
        } 
 
        $total_fee = $data_CidcClasses->fee;

        $carts[] = [
            'kid_id'        => $payload['kid_id'],  
            'cidc_class_id' => $payload['cidc_class_id'],
            'fee'           => $data_CidcClasses->fee,
        ];

        $obj_Order = TableRegistry::get('Orders');
        $result = $obj_Order->create_order($carts, $total_fee);
         
        $status = $result['status'];
        $message = $result['message'];
        $params = $result['params'];
        
        return_api:
        return [
            'status'    => $status,
            'message'   => $message,
            'params'    => $params, 
        ];
    }

    public function is_kid_belong_to_order($order_id, $kid_id) {
        $temp = $this->find('all', [
            'conditions' => [
                'StudentRegisterClasses.order_id' => $order_id,
                'StudentRegisterClasses.kid_id IN' => $kid_id,
            ],
        ]);

        if (!$temp) {
            return false;
        }
        return true;
    }

    public function is_study_class($kid_id, $cidc_class_id) {
        $result = $this->find('all', [
            'conditions' => [
                'StudentRegisterClasses.kid_id'         => $kid_id,
                'StudentRegisterClasses.cidc_class_id'  => $cidc_class_id,
            ],
        ])->first();

        return $result ? true : false;
    }

}

<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\MyHelper\MyHelper;
use Cake\Routing\Router;

/**
 * Orders Model
 *
 * @property \App\Model\Table\StudentRegisterClassesTable&\Cake\ORM\Association\HasMany $StudentRegisterClasses
 *
 * @method \App\Model\Entity\Order newEmptyEntity()
 * @method \App\Model\Entity\Order newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Order[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Order get($primaryKey, $options = [])
 * @method \App\Model\Entity\Order findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Order patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Order[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Order|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Order saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Order[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Order[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Order[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Order[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class OrdersTable extends Table
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

        $this->setTable('orders');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('StudentRegisterClasses', [
            'foreignKey' => 'order_id',
        ]);

        $this->hasMany('OrderReceipts', [
            'foreignKey' => 'order_id',
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
            ->scalar('order_number')
            ->maxLength('order_number', 20)
            ->requirePresence('order_number', 'create')
            ->notEmptyString('order_number');

        $validator
            ->decimal('total_fee')
            ->notEmptyString('total_fee');

        $validator
            ->notEmptyString('status');

        $validator
            ->boolean('enabled')
            ->notEmptyString('enabled');
 

        return $validator;
    }

    // $carts = [
    //     'kid_id' => 1, 
    //     'cidc_class_id' => 20,
    // ]

    // TODO: save order details
    public function create_order($carts, $total_fee) {
        
        $db = $this->getConnection();
        $db->begin();

        $status = 0;
        $message = ""; 
        $params = null;

        $data_Order = [
            'order_number' => uniqid(),
            'total_fee' => $total_fee,
        ];

         
        $entity_Order = $this->newEntity($data_Order);
        $obj_StudentRegisterClasses = TableRegistry::get('StudentRegisterClasses');
        if ($order = $this->save($entity_Order)) {
            $data_StudentRegisterClass = [];
            foreach ($carts as $value) { 
 
                $data_StudentRegisterClass[] = [
                    'kid_id'        => $value['kid_id'],
                    'cidc_class_id' => $value['cidc_class_id'],
                    'fee'           => $value['fee'],
                    'order_id'      => $order->id, 
                ];  
            } 

            $entities_StudentRegisterClass = $obj_StudentRegisterClasses->newEntities($data_StudentRegisterClass);
            if (!$obj_StudentRegisterClasses->saveMany($entities_StudentRegisterClass)) {
                $status = 500;
                $message = __('data_is_not_saved') . ' StudentRegisterClass';
            }

            $db->commit();

            $params['order_id'] = $order->id;
 
            $status = 200;
            $message = __('data_is_saved');

        } else {
            $db->rollback();
            $status = 500;
            $message = __('data_is_not_saved') . json_encode($entity_Order->getErrors());
        }

        return [
            'status'    => $status,
            'message'   => $message, 
            'params'    => $params,
        ];
    }

    // TODO: get order after save succeed order;
    public function get_order($order_id, $belong_parent_id, $language) {
        // check validate kid from parent_id
        $kids = TableRegistry::get('Kids')->get_kid_belong_to_parent($belong_parent_id); 
      
        $result = []; 
        $is_validate = TableRegistry::get('StudentRegisterClasses')->is_kid_belong_to_order($order_id, $kids); 
        if (!$is_validate) {
            return $result;
        }
        
        if (!$kids) {
            return $result;
        } 

        $temp = $this->find('all', [
            'conditions' => [
                'Orders.id' => $order_id, 
            ], 
            'fields' => [
                'Orders.id',
                'Orders.order_number',
                'Orders.total_fee',
                'Orders.created',
            ],
            'contain' => [
                'StudentRegisterClasses' => [
                    'fields' => [
                        'StudentRegisterClasses.order_id',
                        'StudentRegisterClasses.cidc_class_id',
                        'StudentRegisterClasses.kid_id',
                        'StudentRegisterClasses.fee',
                    ], 
                    'Kids' => [
                        'fields' => [
                            'Kids.id', 
                            'Kids.cidc_parent_id',
                            'Kids.relationship_id',
                            'Kids.gender',
                            'Kids.dob',
                        ],
                        'KidImages' => [
                            'fields' => [
                                'KidImages.kid_id', 
                                'KidImages.path', 
                            ],
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
                            'CidcClasses.fee',
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
                                    'ProgramImages.path', 
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
                    ],
                ],
            ],
        ])->first(); 
 
        if (!$temp) {
            return $result;
        }
 
        // This children not belong parent
        if (empty($temp->student_register_classes)) {
            return $result;
        }

        $result = [
            'order_id'      => intVal($order_id),
            'order_date'    => $temp->created->format('Y-m-d'), 
            'order_number'  => $temp->order_number, 
        ]; 

        $student_register_classes = $temp->student_register_classes;
        $list = [];
        foreach ($student_register_classes as $value) {

            $default_avatar = Router::url('/', true) . ($value->kid->gender == MyHelper::MALE ? 'cidckids/student/boy.svg' : 'cidckids/student/girl.svg'); 
            $list[]        = [ 
                'kid_id'  => $value->kid->id,
                'gender'  => $value->kid->gender,
                'name'    => !empty($value->kid->kid_languages) ? $value->kid->kid_languages[0]->name : null, 
                'avatar'  => !empty($value->kid->kid_images) ? $value->kid->kid_images[0]->path  : $default_avatar, 
                'class_name'    => $value->cidc_class->name,
                'program_name'  => $value->cidc_class->program->program_languages[0]->name,
                'program_image' => !empty($value->cidc_class->program->program_images) ? Router::url('/', true) . $value->cidc_class->program->program_images[0]->path : null,
                'course_name'   => $value->cidc_class->course->course_languages[0]->name,
                'center_name'   => $value->cidc_class->center->center_languages[0]->name,
                'fee'           => $value->cidc_class->fee,
            ];
        } 

        // format data; // group by kids id
        $lst_kids = [];
        foreach ($list as $value) {
            $lst_kids[$value['kid_id']][] = $value;
        }
 
        $rel = [];
        foreach ($lst_kids as $kids) {
            $temp = [
                'id'     => $kids[0]['kid_id'],
                'gender_id' => intval($kids[0]['gender']),
                'name'   => $kids[0]['name'],
                'avatar' => $kids[0]['avatar'],
            ];
            $classes = [];
            foreach ($kids as $c) {

                $classes[] = [
                    'name'      => $c['class_name'],
                    'program'   => $c['program_name'],
                    'program_image'   => $c['program_image'],
                    'course'    => $c['course_name'],
                    'center'    => $c['center_name'],
                    'fee'       => $c['fee'],
                    'fee_decimal'       => floatVal($c['fee']),
                ];
            }
            $temp['classes'] = $classes; 

            array_push($rel, $temp);
        }

        $result['details'] = $rel;
        return $result;
    }
}

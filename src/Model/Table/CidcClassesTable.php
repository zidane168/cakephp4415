<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\MyHelper\MyHelper;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\I18n\FrozenTime;
use Cake\Routing\Router;

/**
 * CidcClasses Model
 *
 * @property \App\Model\Table\ProgramsTable&\Cake\ORM\Association\BelongsTo $Programs
 * @property \App\Model\Table\CoursesTable&\Cake\ORM\Association\BelongsTo $Courses
 * @property \App\Model\Table\CentersTable&\Cake\ORM\Association\BelongsTo $Centers
 * @property \App\Model\Table\ClassTypesTable&\Cake\ORM\Association\BelongsTo $ClassTypes
 * @property \App\Model\Table\CidcClassLanguagesTable&\Cake\ORM\Association\HasMany $CidcClassLanguages
 *
 * @method \App\Model\Entity\CidcClass newEmptyEntity()
 * @method \App\Model\Entity\CidcClass newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\CidcClass[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CidcClass get($primaryKey, $options = [])
 * @method \App\Model\Entity\CidcClass findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\CidcClass patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CidcClass[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\CidcClass|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CidcClass saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CidcClass[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CidcClass[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\CidcClass[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CidcClass[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CidcClassesTable extends Table
{
    public $PENDING         = 1;
    public $PUBLISHED       = 2;
    public $UNPUBLISHED     = 0;
    public $COMPLETED       = 3;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('cidc_classes');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Courses', [
            'foreignKey' => 'course_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Centers', [
            'foreignKey' => 'center_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ClassTypes', [
            'foreignKey' => 'class_type_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('CidcClassLanguages', [
            'foreignKey' => 'cidc_class_id',
        ]);
        $this->hasMany('DateOfLessons', [
            'foreignKey' => 'cidc_class_id',
        ]);

        $this->hasMany('Albums', [
            'foreignKey' => 'cidc_class_id',
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
            ->scalar('name')
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->decimal('fee')
            ->requirePresence('fee', 'create')
            ->notEmptyString('fee');

        $validator
            ->scalar('code')
            ->maxLength('code', 10)
            ->requirePresence('code', 'create')
            ->notEmptyString('code');

        $validator
            ->integer('target_audience_from')
            ->requirePresence('target_audience_from', 'create')
            ->notEmptyString('target_audience_from');

        $validator
            ->integer('target_audience_to')
            ->requirePresence('target_audience_to', 'create')
            ->notEmptyString('target_audience_to');

        $validator
            ->integer('target_unit')
            ->requirePresence('target_unit', 'create')
            ->notEmptyString('target_unit');

        $validator
            ->date('start_date')
            ->notEmptyString('start_date');

        $validator
            ->date('end_date')
            ->notEmptyString('end_date');

        $validator
            ->time('start_time')
            ->notEmptyString('start_time');

        $validator
            ->time('end_time')
            ->notEmptyString('end_time');

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
        $rules->add($rules->existsIn(['program_id'], 'Programs'), ['errorField' => 'program_id']);
        $rules->add($rules->existsIn(['course_id'], 'Courses'), ['errorField' => 'course_id']);
        $rules->add($rules->existsIn(['center_id'], 'Centers'), ['errorField' => 'center_id']);
        $rules->add($rules->existsIn(['class_type_id'], 'ClassTypes'), ['errorField' => 'class_type_id']);

        return $rules;
    }

    public function get_status()
    {
        return [
            1 => __d('cidcclass', 'pending'),
            2 => __d('cidcclass', 'published'),
            0 => __d('cidcclass', 'unpublished'),
            3 => __d('cidcclass', 'completed'),
        ];
    }

    public function get_status_by_id($id)
    {

        $statuses = $this->get_status();
        foreach ($statuses as $key => $value) {
            if ($key === $id) {
                return $value;
            }
        }
        return '';
    }
  
    public function get_target_unit()
    {
        return [
            1 => __('month'),
            2 => __('year'),
        ];
    }

    public function gen_code($center_id, $class_type_id)
    {

        $obj_Centers = TableRegistry::get('Centers');
        $result_Center = $obj_Centers->find('all', [
            'conditions' => [
                'Centers.id' => $center_id
            ],
            'fields' => [
                'Centers.code',
            ],
        ])->first();

        $center_code = $result_Center ? $result_Center->code : '';

        // $class_type_id = 'R';
        // if ($class_type_id === 1) {
        //     $class_type = 'R';      // repeat
        // } elseif ($class_type_id === 2) {
        //     $class_type = 'U';     // unrepeat
        // } else {
        //     $class_type = 'T';      // Trial
        // }

        // $code = $center_code . $class_type  . $this->generate_code(6);
        $code = $center_code .  $this->generate_code(6);

        $query = $this->find('all', [
            'conditions' => [
                'CidcClasses.code' => $code,
            ],
        ]);

        while ($query->count() > 0) {
            $code = $center_code . $this->generate_code(6);

            $query = $this->find('all', [
                'conditions' => [
                    'CidcClasses.code' => $code,
                ],
            ]);
        }

        return $code;
    }

    public function get_info_by_id($id)
    { 
        return $this->find('all', [
            'conditions' => [
                'CidcClasses.id' => $id,
                'CidcClasses.enabled' => true,
            ], 
        ])->First();
    }

    // conditions: 
    //   + Choose enabled
    //   + Choose not full: 'CidcClasses.number_of_register < CidcClasses.maximum_of_students'
    public function get_all($language)
    {
        $classes = $this->find('all', [
            'fields' => [
                'id',
                'code',
                'program_id',
                'course_id',
                'center_id',
                'status',
                'number_of_register',
                'fee',
                'class_type_id',
                'code',
                'target_audience_from',
                'target_audience_to',
                'target_unit',
                'minimum_of_students',
                'maximum_of_students',
                'start_date',
                'end_date',
                'start_time',
                'end_time',
            ],
            'conditions' => [
                'CidcClasses.enabled' => true,
                'OR' => [
                    'AND' => [
                        'CidcClasses.number_of_register < CidcClasses.maximum_of_students',
                        'CidcClasses.class_type_id = 2',
                    ],
                    ['CidcClasses.class_type_id = 1'],
                    ['CidcClasses.class_type_id = 3'],
                ],

            ],
            'contain' => [
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
                            'ProgramLanguages.name'
                        ],
                        'conditions' => [
                            'ProgramLanguages.alias' => $language,
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
                            'CourseLanguages.name'
                        ],
                        'conditions' => [
                            'CourseLanguages.alias' => $language,
                        ],
                    ],
                ],

            ],
        ]);


        $format_datas = [];
        foreach ($classes as $class) {
            $format_datas[] = [
                'id'        => $class->id,
                'code'      => $class->code,
                'program'   => $class->program->program_languages[0]->name,
                'course'    => $class->course->course_languages[0]->name,
                'center'    => $class->center->center_languages[0]->name,
                'date'      => $class->start_date->format('Y M d')  . ' - ' . $class->end_date->format('Y M d'),
                'time'      => $class->start_time->format('h:i(A)')  . ' - ' . $class->end_time->format('h:i(A)'),
                'fee'       => $class->fee,
            ];
        }

        return $format_datas;
    }

    public function get_weekend_day()
    {
        return [
            '2' => __d('cidcclass', 'each_monday'),
            '3' => __d('cidcclass', 'each_tuesday'),
            '4' => __d('cidcclass', 'each_wednesday'),
            '5' => __d('cidcclass', 'each_thursday'),
            '6' => __d('cidcclass', 'each_friday'),
            '7' => __d('cidcclass', 'each_saturday'),
            '8' => __d('cidcclass', 'each_sunday'),
        ];
    }

    // + Choose enabled
    // + Choose class type
    // + Choose not full
    public function get_classes_by_class_types_v1($language, $payload = array())
    {

        $conditions = [
            'CidcClasses.enabled' => true,
            'CidcClasses.class_type_id' => $payload['class_type_id'],
            'OR' => [
                'AND' => [
                    'CidcClasses.number_of_register < CidcClasses.maximum_of_students',
                    'CidcClasses.class_type_id = 2',
                ],
                ['CidcClasses.class_type_id = 1'],
                ['CidcClasses.class_type_id = 3'],
            ],
        ];

        $temp = $this->find('all', [
            'fields' => [
                'CidcClasses.id',
                'CidcClasses.program_id',
                'CidcClasses.course_id',
                'CidcClasses.code',
                'CidcClasses.class_type_id',

                'Programs.id',
                'Programs.title_color',
                'Programs.background_color',
                'ProgramLanguages.name',

                'Courses.id',
                'Courses.age_range_from',
                'Courses.age_range_to',
                'Courses.unit',
                'CourseLanguages.name',
            ],
            'conditions' => $conditions,
            'contain' => [
                'ClassTypes' => [
                    'fields' => [
                        'ClassTypes.id',    // no need check enabled of class types
                    ],
                    'ClassTypeLanguages' => [
                        'fields' => [
                            'ClassTypeLanguages.class_type_id',
                            'ClassTypeLanguages.id',
                            'ClassTypeLanguages.name',
                        ],
                        'conditions' => [
                            'ClassTypeLanguages.alias' => $language,
                        ],
                    ],
                ],
            ],
            'join' => [
                [
                    'table' => 'programs',
                    'alias' => 'Programs',
                    'type' => 'INNER',
                    'conditions' => [
                        'CidcClasses.program_id = Programs.id', // no need check enable from program, we will check relationship when disabled/enable on program, course, class
                    ],
                ],
                [
                    'table' => 'program_languages',
                    'alias' => 'ProgramLanguages',
                    'type' => 'INNER',
                    'conditions' => [
                        'Programs.id = ProgramLanguages.program_id',
                        'ProgramLanguages.alias' => $language,
                    ],
                ],
                [
                    'table' => 'courses',
                    'alias' => 'Courses',
                    'type' => 'INNER',
                    'conditions' => [
                        'CidcClasses.course_id = Courses.id', // no need check enable from program, we will check relationship when disabled/enable on program, course, class
                    ],
                ],
                [
                    'table' => 'course_languages',
                    'alias' => 'CourseLanguages',
                    'type' => 'INNER',
                    'conditions' => [
                        'Courses.id = CourseLanguages.course_id',
                        'CourseLanguages.alias' => $language,
                    ],
                ],
            ],
        ]);

        if (!$temp->toArray()) {
            return null;
        }

        $class_types = null;
        $temp = $temp->toArray();
        foreach ($temp as $class) {
            $program[] = [
                'id'            => $class->program_id,
                'name'          => $class->ProgramLanguages['name'],
                'title_color'   => $class->Programs['title_color'],
                'background_color'  => $class->Programs['background_color'],
                'course_id'         => $class->course_id,
                'course_name'       => $class->CourseLanguages['name'],
                'age_range_from'    => $class->Courses['age_range_from'],
                'age_range_to'      => $class->Courses['age_range_to'],
                'unit'              => $class->Courses['unit'],
            ];
            $class_types = [
                'id'        => $class->class_type_id,
                'name'      => $class->class_type->class_type_languages[0]->name,
                'programs'  => $program,
            ];
        }

        $courses = [];

        foreach ($class_types['programs'] as $program) {

            $courses[$program['course_name']][] = [
                'id'            => $program['course_id'],
                'name'          => $program['course_name'],
                'list_courses_' => $program,
            ];
        }

        $class_types['courses'] = ($courses);
        unset($program['programs']);

        // format items  
        foreach ($class_types['courses'] as $key => $value) {

            $lst_programs = [];
            foreach ($value as $val) {
                $c = [
                    'id' => $val['id'],
                    'name' => $val['name'],
                ];

                $lst_programs[] = [
                    'id'                => $val['list_courses_']['id'],
                    'name'              => $val['list_courses_']['name'],
                    'title_color'       => $val['list_courses_']['title_color'],
                    'background_color'  => $val['list_courses_']['background_color'],
                    'age_range_from'    => $val['list_courses_']['age_range_from'],
                    'age_range_to'      => $val['list_courses_']['age_range_to'],
                    'unit'              => $val['list_courses_']['unit'],
                ];
            }
            $c['programs'] = $lst_programs;

            $class_types['list_courses'][] = $c;
        }

        unset($class_types['programs']);
        unset($class_types['courses']);

        return   $class_types;
    }

    public function get_classes_by_class_types($language, $payload = array())
    {
        $conditions = [
            'CidcClasses.enabled' => true,
            'OR' => [
                'CidcClasses.status' => $this->PENDING,
                'AND' => [
                    'CidcClasses.status' => $this->PUBLISHED,
                    'CidcClasses.class_type_id' => MyHelper::CIRCULAR,
                ]
            ],
        ];

        if (isset($payload['class_type_id']) && !empty($payload['class_type_id'])) {
            $conditions['CidcClasses.class_type_id'] = $payload['class_type_id'];
        } 

        if (isset($payload['center_id']) && !empty($payload['center_id'])) {   
            if (is_array($payload['center_id'])) {
                $conditions['CidcClasses.center_id IN'] = $payload['center_id'];
            } else {
                $conditions['CidcClasses.center_id'] = $payload['center_id'];
            } 
        }
        if (isset($payload['program_id']) && !empty($payload['program_id'])) {
            $conditions['CidcClasses.program_id'] = $payload['program_id'];
        }
        if (isset($payload['course_id']) && !empty($payload['course_id'])) {
            $conditions['CidcClasses.course_id'] = $payload['course_id'];
        }
        if (
            isset($payload['unit']) &&
            !empty($payload['unit']) &&
            isset($payload['age_from']) &&
            !empty($payload['age_from']) &&
            isset($payload['age_to']) &&
            !empty($payload['age_to'])
        ) {
            $obj_Courses = TableRegistry::get('Courses');
            $couses = $obj_Courses->find('all', [
                'fields' => ['Courses.id'],
                'conditions' => [
                    'NOT' => [
                        'OR' => [
                            [
                                'Courses.age_range_to <' => $payload['age_from'],
                                'Courses.unit' => $payload['unit'],
                            ],
                            [
                                'Courses.age_range_from >' => $payload['age_to'],
                                'Courses.unit' => $payload['unit'],
                            ]
                        ]
                    ],
                    'Courses.unit' => $payload['unit'],
                    'Courses.enabled' => true
                ]
            ])->toArray();
            $course_ids = array_map(function ($item) {
                return $item->id;
            }, $couses);
            if (!!$course_ids) {
                $conditions['CidcClasses.course_id IN'] = $course_ids;
            } else {
                return [
                    'items' => [],
                    'count' => 0
                ];
            }
        }
        $result = [];
        $temp = $this->find('all', [
            'conditions' => $conditions,
            'contain' => [
                'CidcClassLanguages' => [
                    'fields' => [
                        'CidcClassLanguages.description',
                        'CidcClassLanguages.cidc_class_id',
                    ],
                    'conditions' => [
                        'CidcClassLanguages.alias' => $language,
                    ],
                ],
                'DateOfLessons' => [
                    'fields' => [
                        'DateOfLessons.day',
                        'DateOfLessons.cidc_class_id',
                    ],
                ],
                'Programs' => [
                    'conditions' => [
                        'Programs.enabled' => true
                    ],
                    'ProgramLanguages' => [
                        'conditions' => [
                            'ProgramLanguages.alias' => $language
                        ]
                    ],
                    'ProgramImages' => [
                        'fields' => [
                            'ProgramImages.program_id',
                            'ProgramImages.path',
                        ],
                    ],
                ],
                'Courses' => [
                    'conditions' => [
                        'Courses.enabled' => true
                    ],
                    'CourseLanguages' => [
                        'conditions' => [
                            'CourseLanguages.alias' => $language
                        ]
                    ]
                ],
                'Centers' => [
                    'conditions' => [
                        'Centers.enabled' => true
                    ],
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
            ],
            'page'  => (int)$payload['page'],
            'limit' => $payload['limit']

        ]);
        $classes = $temp->toArray();
        foreach ($classes as $class) {
            $result[] = $this->set_response('FORMAT_CLASSES', $class);
        } 
        return [
            'items' => $result,
            'count' => $temp->count(),
        ];
    }

    public function get_by_id_student_attend_class($id)
    {
        $result = $this->find('all', [
            'fields' => [
                'CidcClasses.id',
                'CidcClasses.name',
                'CidcClasses.start_date',
                'CidcClasses.end_date',
                'CidcClasses.start_time',
                'CidcClasses.end_time',
                'CidcClasses.status',
                'CidcClasses.fee',
                'CidcClasses.number_of_register',
                'CidcClasses.number_of_lessons',
                'CidcClasses.target_audience_from',
                'CidcClasses.target_audience_to',
                'CidcClasses.target_unit',
                'CidcClasses.minimum_of_students',
                'CidcClasses.maximum_of_students',
            ],
            'conditions' => [
                'CidcClasses.id' => $id,
            ],
            'contain' => [
                'DateOfLessons' => [
                    'fields' => [
                        'DateOfLessons.id',
                        'DateOfLessons.cidc_class_id',
                        'DateOfLessons.day'
                    ],
                ],
            ],
        ])->first();

        $array_date_of_lessons = [];
        $lessons = $result->date_of_lessons;
        foreach ($lessons as $value) {

            if ($value->day == 2) {
                $array_date_of_lessons[] = __d('cidcclass', 'each_monday');
            } elseif ($value->day == 3) {
                $array_date_of_lessons[] = __d('cidcclass', 'each_tuesday');
            } elseif ($value->day == 4) {
                $array_date_of_lessons[] = __d('cidcclass', 'each_wednesday');
            } elseif ($value->day == 5) {
                $array_date_of_lessons[] = __d('cidcclass', 'each_thursday');
            } elseif ($value->day == 6) {
                $array_date_of_lessons[] = __d('cidcclass', 'each_friday');
            } elseif ($value->day == 7) {
                $array_date_of_lessons[] = __d('cidcclass', 'each_saturday');
            } elseif ($value->day == 8) {
                $array_date_of_lessons[] = __d('cidcclass', 'each_sunday');
            }
        }

        $date_of_lessons    = implode(', ', $array_date_of_lessons);
        $unit               = $result->target_unit === 1 ? __('month') : __('year');
        $target_audience    =  $result->target_audience_from . $unit . ' - ' . $result->target_audience_to . $unit;
        $mm_students        = $result->minimum_of_students . ' - ' . $result->maximum_of_students;
        $status             = $this->get_status_by_id($result->status);

        return [
            'name'              => $result->name,
            'date_of_lessons'   => $date_of_lessons,
            'time'              => $result->start_time->format('H:i') . ' - ' . $result->end_time->format('H:i'),
            'date'              => $result->start_date->format('Y-m-d') . ' - ' . $result->end_date->format('Y-m-d'),
            'number_of_lessons' => $result->number_of_lessons,
            'target_audience'   => $target_audience,
            'number_of_register' => $result->number_of_register,
            'min_max_students'  => $mm_students,
            'status'            => $status
        ];
    }

    public function get_list($language = 'en_US', $conditions =  ['CidcClasses.enabled' =>  true])
    {
        $result = $this->find(
            'list',
            [
                'keyField' => 'id',
                'valueField' => function ($row) {
                    return $row->name . " ($row->code) " . $row->class_type->class_type_languages[0]->name;
                }
            ]
        )
            ->where($conditions)
            ->contain([
                'ClassTypes' => [
                    'ClassTypeLanguages' => [
                        'conditions' => [
                            'ClassTypeLanguages.alias' => $language
                        ]
                    ]
                ]
            ]);
        return $result;
    }

    public function get_list_name_fee($conditions =  ['CidcClasses.enabled' =>  true])
    {
        $result = $this->find(
            'list',
            [
                'keyField' => 'id',
                'valueField' => function ($row) {
                    return $row->fee;
                }
            ]
        )->where($conditions);
        return $result;
    }

    public function get_list_by_center_id($center_id, $data, $language)
    {
        $conditions_program = [
            'ProgramLanguages.alias' => $language,
        ];
        $conditions_course = [
            'CourseLanguages.alias' => $language,
        ];
        if (isset($data['search']) && !empty($data['search'])) {
            $conditions_program['ProgramLanguages.name LIKE'] = '%' . $data['search'] . '%';
            $conditions_course['CourseLanguages.name LIKE'] = '%' . $data['search'] . '%';
        }
        $conditions = [
            'CidcClasses.enabled' => true,
            'CidcClasses.center_id' => $center_id,
            'OR' => [
                array('CidcClasses.status' => $this->PUBLISHED),
                array('CidcClasses.status' => $this->COMPLETED),
            ]
        ];
        if (
            isset($data['unit']) &&
            !empty($data['unit']) &&
            isset($data['age_from']) &&
            !empty($data['age_from']) &&
            isset($data['age_to']) &&
            !empty($data['age_to'])
        ) {
            $obj_Courses = TableRegistry::get('Courses');
            $couses = $obj_Courses->find('all', [
                'fields' => ['Courses.id'],
                'conditions' => [
                    'NOT' => [
                        'OR' => [
                            [
                                'Courses.age_range_to <' => $data['age_from'],
                                'Courses.unit' => $data['unit'],
                            ],
                            [
                                'Courses.age_range_from >' => $data['age_to'],
                                'Courses.unit' => $data['unit'],
                            ]
                        ]
                    ],
                    'Courses.unit' => $data['unit'],
                    'Courses.enabled' => true
                ]
            ])->toArray();
            $course_ids = array_map(function ($item) {
                return $item->id;
            }, $couses);
            if (!!$course_ids) {
                $conditions['CidcClasses.course_id IN'] = $course_ids;
            } else {
                return [
                    'items' => [],
                    'count' => 0
                ];
            }
        }

        $items = [];
        $classes = $this->find('all', [
            'conditions' => $conditions,
            'contain' => [
                'Programs' => [
                    'ProgramLanguages' => [
                        'conditions' =>  $conditions_program
                    ]
                ],
                'Courses' => [
                    'CourseLanguages' => [
                        'conditions' => $conditions_course,
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
            ],
            'limit' => $data['limit'],
            'page' => intval($data['page']),
        ]);

        $count =  $classes->count();
        if ($count === 0) {
            return [
                'count' => $count,
                'items' => $items,
            ];
        }

        $skip = 0;
        foreach ($classes->toArray() as $class) {

            if (empty($class->program->program_languages) && empty($class->course->course_languages)) {
                $skip++;
                continue;
            }
            $items[] = $this->set_response('FORMAT_CLASSES', $class);
        }

        return [
            'count' => $count - $skip,
            'items' => $items,
        ];
    }

    public function get_detail_class_by_id($id, $language)
    {
        $temp = $this->find('all', [
            'conditions' => [
                'CidcClasses.id' => $id,
            ],
            'fields' => [
                'CidcClasses.id',
                'CidcClasses.code',
                'CidcClasses.name',
                'CidcClasses.class_type_id',
                'CidcClasses.start_date',
                'CidcClasses.end_date',
                'CidcClasses.start_time',
                'CidcClasses.end_time',
                'CidcClasses.number_of_lessons',
                'CidcClasses.fee',
                'CidcClasses.course_id',
                'CidcClasses.program_id',
                'CidcClasses.status',
                'CidcClasses.number_of_register',
                'CidcClasses.target_audience_from',
                'CidcClasses.target_audience_to',
                'CidcClasses.target_unit',
                'CidcClasses.minimum_of_students',
                'CidcClasses.maximum_of_students',
            ],
            'contain' => [
                'DateOfLessons' => [
                    'fields' => [
                        'DateOfLessons.day',
                        'DateOfLessons.cidc_class_id',
                    ],
                ],
                'Programs' => [
                    'fields' => [
                        'Programs.id',
                        'Programs.background_color',
                        'Programs.title_color',
                    ],
                    'ProgramLanguages' => [
                        'fields' => [
                            'ProgramLanguages.program_id',
                            'ProgramLanguages.name',
                            'ProgramLanguages.description',
                        ],
                        'conditions' => [
                            'ProgramLanguages.alias' => $language
                        ]
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
                        'Courses.age_range_from',
                        'Courses.age_range_to',
                        'Courses.unit',
                    ],
                    'CourseLanguages' => [
                        'fields' => [
                            'CourseLanguages.course_id',
                            'CourseLanguages.name',
                        ],
                        'conditions' => [
                            'CourseLanguages.alias' => $language
                        ]
                    ]
                ],
                'Centers' => [
                    'fields' => [
                        'Centers.id',
                    ],
                    'CenterLanguages' => [
                        'fields' => [
                            'CenterLanguages.name', 
                            'CenterLanguages.center_id',
                        ],
                        'conditions' => [
                            'CenterLanguages.alias' => $language
                        ]
                    ],
                    'Districts' => [
                        'fields' => [
                            'Districts.id',
                        ],
                        'DistrictLanguages' => [
                            'fields' => [
                                'DistrictLanguages.name',
                                'DistrictLanguages.district_id',
                            ],
                            'conditions' => [
                                'DistrictLanguages.alias' => $language
                            ]
                        ]
                    ]
                ],
                'CidcClassLanguages' => [
                    'fields' => [
                        'CidcClassLanguages.description',
                        'CidcClassLanguages.cidc_class_id',
                    ],
                    'conditions' => [
                        'CidcClassLanguages.alias' => $language,
                    ],
                ],
            ],
        ])->first();
 
        $result = [];
        if ($temp) {
            $result = $this->set_response('FORMAT_CLASSES', $temp);
        }

        $url = MyHelper::getUrl();
        $obj_StudentRegisterClasses = TableRegistry::get('StudentRegisterClasses');
        $kids = $obj_StudentRegisterClasses->find('all', [
            'fields' => [
                'id'        => 'DISTINCT Kids.id',
                'avatar'    => "CONCAT('$url', KidImages.path)",
                'name'      => 'KidLanguages.name', 
            ],
            'conditions' => [
                'StudentRegisterClasses.cidc_class_id' => $id,
                // 'Users.id' => $user_id,
            ],
            'join' => [
                [
                    'table' => 'kids',
                    'alias' => 'Kids',
                    'type'  => 'LEFT',
                    'conditions' => ['Kids.id = StudentRegisterClasses.kid_id']
                ],
                [
                    'table' => 'cidc_parents',
                    'alias' => 'CidcParents',
                    'type'  => 'LEFT',
                    'conditions' => ['CidcParents.id = Kids.cidc_parent_id']
                ],
                [
                    'table' => 'users',
                    'alias' => 'Users',
                    'type'  => 'LEFT',
                    'conditions' => ['Users.id = CidcParents.user_id']
                ],
                [
                    'table' => 'kid_languages',
                    'alias' => 'KidLanguages',
                    'type'  => 'LEFT',
                    'conditions' => [
                        'Kids.id = KidLanguages.kid_id',
                        'KidLanguages.alias' => $language
                    ]
                ],
                [
                    'table' => 'kid_images',
                    'alias' => 'KidImages',
                    'type'  => 'LEFT',
                    'conditions' => ['Kids.id = KidImages.kid_id']
                ]
            ]
        ])->toArray();
        $result['kids'] = $kids;

        $kids_names = [];
        foreach ($result['kids'] as $kids) {
            $kids_names[] = $kids['name'];
        }
        $result['kids_description'] = implode(", ", $kids_names) . " " . __d('cidcclass', 'are_attended');
        return $result;
    }

    public function get_list_date_by_class($current_cidc_class_id, $cidc_class_id)
    {

        $conditions = [
            'CidcClasses.id'        => $cidc_class_id,
            'CidcClasses.status'    => $this->PUBLISHED,
            'CidcClasses.enabled'   => true,
        ];
        if ($current_cidc_class_id != $cidc_class_id) {
            $conditions = [
                'CidcClasses.id'        => $cidc_class_id,
                'CidcClasses.status'    => $this->PUBLISHED,
                'CidcClasses.enabled'   => true,
                'CidcClasses.number_of_register < CidcClasses.maximum_of_students'
            ];
        }

        $temp = $this->find('all', [
            'conditions' => $conditions,
            'fields' => [
                'CidcClasses.id',
                'CidcClasses.class_type_id',
                'CidcClasses.status',
                'CidcClasses.number_of_register',
                'CidcClasses.maximum_of_students',
                'CidcClasses.number_of_lessons',
                'CidcClasses.start_time',
                'CidcClasses.end_time',
                'CidcClasses.start_date',
            ],
            'contain' => [
                'DateOfLessons' => [
                    'fields' => [
                        'DateOfLessons.id',
                        'DateOfLessons.day',
                        'DateOfLessons.cidc_class_id',
                    ],
                ],
            ],
        ])->first();

        $date_of_lessons = [];
        $dates = [];
        if (!$temp) {
            return $dates;
        }

        $date_of_lessons = [];
        if (!$temp) {
            return null;
        }
        foreach ($temp->date_of_lessons as $value) {
            $date_of_lessons[] = $value['day'];
        }

        $number_of_lessons  = $temp->number_of_lessons;
        $holidays           = TableRegistry::get('CidcHolidays')->get_all_list();

        if ($temp->class_type_id == MyHelper::NONCIRCULAR) {            // non-circular class
            $current_date       = $temp->start_date->format('Y-m-d');
            $dates = TableRegistry::get('StudentAttendedClasses')->get_date_by_class($cidc_class_id);
        } elseif ($temp->class_type_id == MyHelper::CIRCULAR) {      // circular class
            $current_date       = date('Y-m-d');
            $dates = $this->search_dates_with_conditions($current_date, $date_of_lessons, $number_of_lessons, $holidays);
        }

        return [
            'dates'         => $dates,
            'start_time'    => $temp->start_time->format('H:i'),
            'end_time'      => $temp->end_time->format('H:i')
        ];
    }

    public function get_list_date_by_kid_class($kid_id, $cidc_class_id)
    {

        $conditions = [
            'CidcClasses.id'        => $cidc_class_id,
            'CidcClasses.status'    => $this->PUBLISHED,
            'CidcClasses.enabled'   => true,
        ];

        $temp = $this->find('all', [
            'conditions' => $conditions,
            'fields' => [
                'start_time' => 'CidcClasses.start_time',
                'end_time'   => 'CidcClasses.end_time',
                'date'       => 'StudentAttendedClasses.date'
            ],
            'join' => [
                'table' => 'student_attended_classes',
                'alias' => 'StudentAttendedClasses',
                'type'  => 'LEFT',
                'conditions' => [
                    'StudentAttendedClasses.kid_id' => $kid_id,
                    'StudentAttendedClasses.cidc_class_id' => $cidc_class_id,
                    'StudentAttendedClasses.date >=' => date('Y-m-d'),
                ]
            ]
        ])->toArray();

        $dates = [];
        if (!$temp) {
            return null;
        }

        foreach ($temp as $item) {
            $dates[] = $item->date;
        }

        return [
            'dates'         => $dates,
            'start_time'    => $temp[0]->start_time->format('H:i'),
            'end_time'      => $temp[0]->end_time->format('H:i')
        ];
    }

    public function get_list_by_kid_id($language = 'en_US', $kid_id = null)
    {
        $temp = $this->find('all', [
            'fields' => [
                'id' => 'CidcClasses.id',
                'class_type_languages' => 'ClassTypeLanguages.name',
                'name' => 'CidcClasses.name',
                'code' => 'CidcClasses.code'
            ],
            'conditions' => [
                'CidcClasses.enabled' => true,
                'CidcClasses.status' => $this->PUBLISHED
            ],
            'join' => [
                [
                    'table' => 'class_type_languages',
                    'alias' => 'ClassTypeLanguages',
                    'type'  => 'LEFT',
                    'conditions'  => [
                        'ClassTypeLanguages.alias' => $language,
                        'ClassTypeLanguages.class_type_id = CidcClasses.class_type_id'
                    ]
                ],
                [
                    'table' => 'student_register_classes',
                    'alias' => 'StudentRegisterClasses',
                    'type'  => 'INNER',
                    'conditions'  => [
                        'StudentRegisterClasses.cidc_class_id = CidcClasses.id',
                        'StudentRegisterClasses.kid_id' => $kid_id,
                        'StudentRegisterClasses.status' => MyHelper::PAID,
                    ]
                ]
            ]
        ])->toArray();
        $result = [];
        foreach ($temp as $item) {
            $result[$item->id] = $item->name . " ($item->code) " . $item->class_type_languages;
        }
        return $result;
    }

    public function get_classes_by_from_class($language, $payload = array(), $conditions = [])
    {
        $result = [];
        $classes = $this->find('all', [
            'conditions' => $conditions,
            'contain' => [
                'CidcClassLanguages' => [
                    'fields' => [
                        'CidcClassLanguages.description',
                        'CidcClassLanguages.cidc_class_id',
                    ],
                    'conditions' => [
                        'CidcClassLanguages.alias' => $language,
                    ],
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
            ],
            'page'  => (int)$payload['page'],
            'limit' => $payload['limit']

        ])->toArray();
        foreach ($classes as $class) {
            $result[] = $this->set_response('FORMAT_CLASSES', $class);
        }
        $count = $this->find('all', ['conditions' => $conditions])->count();
        return [
            'items' => $result,
            'count' => $count
        ];
    }

    public function get_classes_dates_by_from_class($language, $payload = array(), $conditions = [])
    {

        $result = [];
        $classes = $this->find('all', [
            'conditions' => $conditions,
            'contain' => [
                'CidcClassLanguages' => [
                    'fields' => [
                        'CidcClassLanguages.description',
                        'CidcClassLanguages.cidc_class_id',
                    ],
                    'conditions' => [
                        'CidcClassLanguages.alias' => $language,
                    ],
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
            ],
            'page'  => (int)$payload['page'],
            'limit' => $payload['limit']

        ])->toArray();
        foreach ($classes as $class) {
            $temp_class = $this->set_response('FORMAT_CLASSES', $class);

            $list_dates = $this->get_list_date_by_class($class->id, $class->id);
            !$list_dates ? $temp_class['list_dates'] = null : $temp_class['list_dates'] = $list_dates['dates'];
            $result[] = $temp_class;
        }
        $count = $this->find('all', ['conditions' => $conditions])->count();
        return [
            'items' => $result,
            'count' => $count
        ];
    }

    public function get_attended_by_class($language = 'en_US', $data)
    {
        $conditions = [
            'StudentAttendedClasses.date' => $data['date'],
            'StudentAttendedClasses.cidc_class_id' => $data['cidc_class_id'],
        ];
        if ($data['status'] == MyHelper::TBD) {
            $conditions['StudentAttendedClasses.status IS'] = null;
        } else {
            $conditions['StudentAttendedClasses.status'] = $data['status'];
        }
        $obj_StudentAttendedClasses = TableRegistry::get('StudentAttendedClasses');
        $kid_ids = $obj_StudentAttendedClasses->find('all', [
            'fields' => [
                'id' => 'DISTINCT StudentAttendedClasses.kid_id'
            ],
            'conditions' => $conditions
        ])->toArray();
        $ids = array_map(function ($item) {
            return $item->id;
        }, $kid_ids);
        $result = [
            [
                'name'   => __('TBD'),
                'status' => MyHelper::TBD,
                'number' => 0
            ],
            [
                'name'   => __('ATTENDED'),
                'status' => MyHelper::ATTENDED,
                'number' => 0
            ],
            [
                'name'   => __('ABSENT'),
                'status' => MyHelper::ABSENT,
                'number' => 0
            ],
            [
                'name'   => __('ON_LEAVE'),
                'status' => MyHelper::ON_LEAVE,
                'number' => 0
            ],
        ];
        $attended = $obj_StudentAttendedClasses->find('all', [
            'fields' => [
                'status' => 'StudentAttendedClasses.status',
                'number' => 'COUNT(StudentAttendedClasses.kid_id)'
            ],
            'conditions' => [
                'StudentAttendedClasses.cidc_class_id' => $data['cidc_class_id'],
                'StudentAttendedClasses.date' => $data['date'],
            ],
            'group' => [
                'StudentAttendedClasses.status'
            ]
        ])->toArray();

        foreach ($attended as $item) {
            switch ($item->status) {
                case null:
                    $result[0]['number'] = (int)$item->number;
                    break;
                case MyHelper::ATTENDED:
                    $result[1]['number'] = (int)$item->number;
                    break;
                case MyHelper::ABSENT:
                    $result[2]['number'] = (int)$item->number;
                    break;
                case MyHelper::ON_LEAVE:
                    $result[3]['number'] = (int)$item->number;
                    break;
                default:
                    break;
            }
        }
        if (!$ids) {
            return [
                'count' => 0,
                'items' => [],
                'attended' => $result
            ];
        }
 
        $obj_Kids  = TableRegistry::get('Kids');

        $temp = $obj_Kids->find('all', [
            'fields' => [
                'id'        => 'Kids.id',
                'gender'    => 'Kids.gender',
                'name'      => 'KidLanguages.name', 
                'avatar'    => 'KidImages.path',
            ],
            'conditions' => [
                'Kids.id IN' => $ids,
                'Kids.enabled' => true
            ],
            'join' => [
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
            'page' => (int)$data['page'],
            'limit' => $data['limit']
        ])->toArray();
        $count = $obj_Kids->find('all', [
            'fields' => [
                'id'        => 'Kids.id',
                'name'      => 'KidLanguages.name', 
                'avatar'    => 'KidImages.path'
            ],
            'conditions' => [
                'Kids.id IN' => $ids,
                'Kids.enabled' => true
            ],
            'join' => [
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
            ]
        ])->count();
        $genders = MyHelper::getGenders();
        foreach ($temp as &$value) {

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
            'items' => $temp,
            'attended' => $result
        ];
    }

    public function get_classes_register_by_kid($language = 'en_US', $kid_id, $payload)
    {
        $obj_StudentRegisterClasses = TableRegistry::get('StudentRegisterClasses');
        $class_ids = $obj_StudentRegisterClasses->find('all', [
            'fields' => [
                'id' => 'DISTINCT StudentRegisterClasses.cidc_class_id'
            ],
            'conditions' => [
                'StudentRegisterClasses.kid_id' => $kid_id,
                'StudentRegisterClasses.enabled' => true,
            ],
        ])->toArray();
        $ids = array_map(function ($item) {
            return $item->id;
        }, $class_ids);

        $classes = $this->find('all', [
            'conditions' => [
                'CidcClasses.id IN' => $ids,
                'CidcClasses.enabled' => true
            ],
            'contain' => [
                'CidcClassLanguages' => [
                    'fields' => [
                        'CidcClassLanguages.description',
                        'CidcClassLanguages.cidc_class_id',
                    ],
                    'conditions' => [
                        'CidcClassLanguages.alias' => $language,
                    ],
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
            ],
            'page'  => (int)$payload['page'],
            'limit' => $payload['limit']

        ])->toArray();
        foreach ($classes as $class) {
            $result[] = $this->set_response('FORMAT_CLASSES', $class);
        }
        $count = $this->find('all', ['conditions' => [
            'CidcClasses.id IN' => $ids,
            'CidcClasses.enabled' => true
        ]])->count();
        return [
            'items' => $result,
            'count' => $count
        ];
    }

    public function get_class_ids_by_kid_ids($kid_ids = null)
    {
        $temp = $this->find('all', [
            'fields' => [
                'id' => 'DISTINCT CidcClasses.id',

            ],
            'conditions' => [
                'CidcClasses.enabled' => true,
                'CidcClasses.status' => $this->PUBLISHED
            ],
            'join' => [
                'table' => 'student_register_classes',
                'alias' => 'StudentRegisterClasses',
                'type'  => 'INNER',
                'conditions'  => [
                    'StudentRegisterClasses.cidc_class_id = CidcClasses.id',
                    'StudentRegisterClasses.kid_id IN' => $kid_ids,
                    'StudentRegisterClasses.status' => MyHelper::PAID,
                ]
            ]
        ])->toArray();
        if (!$temp) {
            return null;
        }
        $result = array_map(function ($item) {
            return $item->id;
        }, $temp);

        return $result;
    }

    public function get_album_by_class_ids($class_ids, $data)
    {
        $classes  = $this->find(
            'all',
            [
                'conditions' => [
                    'CidcClasses.enabled' => true,
                    'CidcClasses.id IN' => $class_ids
                ],
                'contain' => [
                    'Albums'
                ],
            ]
        )->toArray();
        $result = [];
        $url = MyHelper::getUrl();
        foreach ($classes as $class) {
            if (isset($class->albums[0]->path)) {
                $result[] = [
                    'id'   => $class->id,
                    'name' => $class->name,
                    'code' => $class->code,
                    'album' => $url . $class->albums[0]->path
                ];
            }
        }
        $offset = ((int)$data['page'] - 1) * (int)$data['limit'];
        $limit = (int)$data['limit'];
        return [
            'items' => array_slice($result, $offset, $limit),
            'count' => count($result)
        ];
    }

    // get list available with 
    // number of register < max student (non circular class) 
    public function get_list_available($language = 'en_US')
    {
        $conditions = [
            'CidcClasses.enabled' => true,
            'CidcClasses.number_of_register < CidcClasses.maximum_of_students'
        ];

        $result = $this->find(
            'list',
            [
                'keyField' => 'id',
                'valueField' => function ($row) {
                    return $row->name . " ($row->code) " . $row->class_type->class_type_languages[0]->name;
                }
            ]
        )
            ->where($conditions)
            ->contain([
                'ClassTypes' => [
                    'ClassTypeLanguages' => [
                        'conditions' => [
                            'ClassTypeLanguages.alias' => $language
                        ]
                    ]
                ]
            ]);
        return $result;
    }

    // fill in combobox
    public function get_list_belong_center($language, $center_ids) // add product admin page
    {      
        $cidcClasses = $this->find('all', [
            'fields' => [
                'CidcClasses.id', 
                'CidcClasses.name',
            ],
            'conditions' => [ 
                'CidcClasses.enabled' => true,
                'CidcClasses.center_id IN' => $center_ids,
            ], 
        ])->toArray();
 

        $list = [];
        foreach ($cidcClasses as $class) {
            $list[$class->id] = $class->name;
        }

        return $list;
    } 

    public function get_list_id_belong_center($language, $center_ids) // add product admin page
    {      
        $cidcClasses = $this->find('all', [
            'fields' => [
                'CidcClasses.id', 
                'CidcClasses.name',
            ],
            'conditions' => [ 
                'CidcClasses.enabled' => true,
                'CidcClasses.center_id IN' => $center_ids,
            ], 
        ])->toArray();
 

        $list = [];
        foreach ($cidcClasses as $class) {
            $list[] = $class->id;
        }

        return $list;
    } 
}

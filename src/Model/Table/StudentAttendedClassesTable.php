<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * StudentAttendedClasses Model
 *
 * @property \App\Model\Table\CidcClassesTable&\Cake\ORM\Association\BelongsTo $CidcClasses
 * @property \App\Model\Table\KidsTable&\Cake\ORM\Association\BelongsTo $Kids
 *
 * @method \App\Model\Entity\StudentAttendedClass newEmptyEntity()
 * @method \App\Model\Entity\StudentAttendedClass newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\StudentAttendedClass[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\StudentAttendedClass get($primaryKey, $options = [])
 * @method \App\Model\Entity\StudentAttendedClass findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\StudentAttendedClass patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\StudentAttendedClass[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\StudentAttendedClass|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StudentAttendedClass saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StudentAttendedClass[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\StudentAttendedClass[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\StudentAttendedClass[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\StudentAttendedClass[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class StudentAttendedClassesTable extends Table
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

        $this->setTable('student_attended_classes');
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
            ->date('date')
            ->allowEmptyDate('date');

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
            1 => __d('cidcclass', 'attended'),
            2 => __d('cidcclass', 'absent'),
            3 => __d('cidcclass', 'on_leave'),
        ];
    }

    public function update_status_attended($cidc_class_id, $attandence)
    {
        // $entities = [];
        $now_date =  date('Y-m-d');
        $kid_ids = array_map(function ($item) {
            return $item->kid_id;
        }, $attandence);
        $entities = $this->find('all', [
            'conditions' => [
                'StudentAttendedClasses.enabled'            =>  true,
                'StudentAttendedClasses.date'               => date('Y-m-d'),
                'StudentAttendedClasses.cidc_class_id'      => $cidc_class_id,
                'StudentAttendedClasses.kid_id IN'          => $kid_ids,
            ]
        ])->toArray();
        foreach ($entities as &$entity) {
            $status = $this->get_status_attendence_by_kid_id($attandence, $entity->kid_id);
            $entity->status =  $status ? $status : null;
        }
        if (!$this->saveMany($entities)) {
            return false;
        }
        return true;
    }

    private function get_status_attendence_by_kid_id($attendence, $kid_id)
    {
        foreach ($attendence as $data) {
            if ($data->kid_id == $kid_id) {
                return $data->status;
            }
        }
        return false;
    }

    public function get_dates_by_cidc_class_id($class_id, $language = 'en_US')
    {
        $obj_Class = TableRegistry::get('CidcClasses');
        $class = $obj_Class->find('all', [
            'fields' => [
                'CidcClasses.id',
                'CidcClasses.program_id',
                'CidcClasses.center_id',
                'CidcClasses.number_of_register',
                'CidcClasses.number_of_lessons',
                'CidcClasses.course_id',
                'CidcClasses.class_type_id',
                'CidcClasses.status',
                'CidcClasses.fee',
                'CidcClasses.name',
                'CidcClasses.code',
                'CidcClasses.target_audience_from',
                'CidcClasses.target_audience_to',
                'CidcClasses.target_unit',
                'CidcClasses.start_date',
                'CidcClasses.end_date',
                'CidcClasses.start_time',
                'CidcClasses.end_time',
                'CidcClasses.minimum_of_students',
                'CidcClasses.maximum_of_students',
            ],
            'conditions' => [
                'CidcClasses.enabled' => true,
                'CidcClasses.id' => $class_id,
            ],
            'contain' => [
                'Programs' => [
                    'fields' => [
                        'Programs.id'
                    ],
                    'conditions' => [
                        'Programs.enabled' => true
                    ],
                    'ProgramLanguages' => [
                        'fields' => [
                            'ProgramLanguages.program_id',
                            'ProgramLanguages.name'
                        ],
                        'conditions' => [
                            'ProgramLanguages.alias' => $language
                        ]
                    ]
                ],
                'Courses' => [
                    'fields' => [
                        'Courses.id'
                    ],
                    'conditions' => [
                        'Courses.enabled' => true
                    ],
                    'CourseLanguages' => [
                        'fields' => [
                            'CourseLanguages.course_id',
                            'CourseLanguages.name'
                        ],
                        'conditions' => [
                            'CourseLanguages.alias' => $language
                        ]
                    ]
                ],
                'Centers' => [
                    'fields' => [
                        'Centers.id'
                    ],
                    'conditions' => [
                        'Centers.enabled' => true
                    ],
                    'CenterLanguages' => [
                        'fields' => [
                            'CenterLanguages.center_id',
                            'CenterLanguages.name'
                        ],
                        'conditions' => [
                            'CenterLanguages.alias' => $language
                        ]
                    ]
                ],
                'ClassTypes' => [
                    'fields' => [
                        'ClassTypes.id'
                    ],
                    'conditions' => [
                        'ClassTypes.enabled' => true
                    ],
                    'ClassTypeLanguages' => [
                        'fields' => [
                            'ClassTypeLanguages.class_type_id',
                            'ClassTypeLanguages.name'
                        ],
                        'conditions' => [
                            'ClassTypeLanguages.alias' => $language
                        ]
                    ]
                ]
            ]
        ])->first();
        // get date of lessons
        $obj_DateOfLessons = TableRegistry::get('DateOfLessons');
        $result_DateOfLessons = $obj_DateOfLessons->get_list($class->id); // [2,4,6]: Mon, Wed, Fri

        // get holidays
        $obj_Holidays = TableRegistry::get('CidcHolidays');
        $holidays = $obj_Holidays->get_all_list();
        $dates = $obj_Class->get_date_of_week_in_range($class->start_date, $class->end_date, $result_DateOfLessons, $class->number_of_lessons, $holidays);
        $unit               = $class->target_unit === 1 ? __('month') : __('year');
        $target_audience    =  $class->target_audience_from . $unit . ' - ' . $class->target_audience_to . $unit;
        $mm_students        = $class->minimum_of_students . ' - ' . $class->maximum_of_students;
        return [
            'id'                            => $class->id,
            'name'                          => $class->name,
            'code'                          => $class->code,
            'number_of_register'            => $class->number_of_register,
            'number_of_lessons'             => $class->number_of_lessons,
            'target_audience'               => $target_audience,
            'time'                          => $class->start_time->format('H:i') . ' - ' . $class->end_time->format('H:i'),
            'date'                          => $class->start_date->format('Y-m-d') . ' - ' . $class->end_date->format('Y-m-d'),
            'min_max_students'              => $mm_students,
            'program'                       => $class->program->program_languages[0]->name,
            'course'                        => $class->course->course_languages[0]->name,
            'center'                        => $class->center->center_languages[0]->name,
            'class_type'                    => $class->class_type->class_type_languages[0]->name,
            'dates'                         => $dates
        ];
    }

    public function get_date_by_class($cidc_class_id)
    {
        $today = date('Y-m-d');
        $temp = $this->find('all', [
            'conditions' => [
                'cidc_class_id' => $cidc_class_id,
                'date >='       => $today
            ],
            'fields' => [
                'date'        => 'DISTINCT date',
            ],
            'order' => [
                'date ASC'
            ],

        ])->toArray();

        $dates = [];
        foreach ($temp as $value) {
            $dates[] = $value->date->format('Y-m-d');
        }

        return $dates;
    }

    public function get_min_max_date_class_and_kid($cidc_class_id, $kid_id)
    {
        $temp = $this->find('all', [
            'fields' => [
                'min_date' => 'MIN(StudentAttendedClasses.date)',
                'max_date' => 'MAX(StudentAttendedClasses.date)',
            ],

            'conditions' => [
                'StudentAttendedClasses.cidc_class_id' => $cidc_class_id,
                'StudentAttendedClasses.kid_id'         => $kid_id
            ]
        ])->first();
        return $temp;
    }
}

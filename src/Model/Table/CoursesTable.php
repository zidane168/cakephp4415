<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\MyHelper\MyHelper;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Courses Model
 *
 * @property \App\Model\Table\ProgramsTable&\Cake\ORM\Association\BelongsTo $Programs
 * @property \App\Model\Table\ClassesTable&\Cake\ORM\Association\HasMany $Classes
 * @property \App\Model\Table\CourseLanguagesTable&\Cake\ORM\Association\HasMany $CourseLanguages
 *
 * @method \App\Model\Entity\Course newEmptyEntity()
 * @method \App\Model\Entity\Course newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Course[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Course get($primaryKey, $options = [])
 * @method \App\Model\Entity\Course findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Course patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Course[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Course|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Course saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Course[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Course[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Course[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Course[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CoursesTable extends Table
{
    public function joinLanguage($language)
    {
        return [
            'table' => 'course_languages',
            'alias' => 'CourseLanguages',
            'type' => 'INNER',
            'conditions' => [
                'CourseLanguages.course_id = Courses.id',
                'CourseLanguages.alias' => $language,
            ],
        ];
    }
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('courses');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Classes', [
            'foreignKey' => 'course_id',
        ]);
        $this->hasMany('CourseLanguages', [
            'foreignKey' => 'course_id',
            'dependent'     => true
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
            ->integer('age_range_from')
            ->requirePresence('age_range_from', 'create')
            ->notEmptyString('age_range_from');

        $validator
            ->integer('age_range_to')
            ->requirePresence('age_range_to', 'create')
            ->notEmptyString('age_range_to');

        $validator
            ->notEmptyString('unit');

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

        return $rules;
    }

    public function get_list($language, $conditions = array()) // add product admin page
    {
        $courses = $this->find(
            'list',
            [
                'keyField' => 'id',
                'valueField' => function ($row) {
                    return $row->course_languages[0] ? $row->course_languages[0]->name : '';
                }
            ]
        )
            ->where([$conditions])
            ->contain(
                [
                    'CourseLanguages' => [
                        'conditions' => ['CourseLanguages.alias' => $language]
                    ]
                ]
            );
        return $courses;
    }


    public function get_unit()
    {
        return [
            1 => __d('center', 'months'),
            2 => __d('center', 'years')
        ];
    }

    public function get_list_pagination($language, $payload)
    {
        $conditions = [
            'Courses.enabled' => true,
        ];

        if (isset($payload['program_id']) && !empty($payload['program_id'])) {
            $conditions['Programs.id'] = $payload['program_id'];
        }
        $total = $this->find('all', [
            'conditions' => $conditions,
            'join' => [
                [
                    'table' => 'course_languages',
                    'alias' => 'CourseLanguages',
                    'type' => 'INNER',
                    'conditions' => [
                        'CourseLanguages.course_id = Courses.id',
                        'CourseLanguages.alias' => $language,
                    ],
                ],
                [
                    'table' => 'programs',
                    'alias' => 'Programs',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Programs.id = Courses.program_id',
                    ],
                ],
            ],
        ])->count();
        $result = [];

        if (!$total) {
            goto set_result;
        }

        if (isset($payload['search']) && !empty($payload['search'])) {
            $conditions['LOWER(CourseLanguages.name) LIKE'] = '%' . strtolower($payload['search']) . '%';
        }
        $result = $this->find('all', [
            'fields' => [
                'id'    => 'Courses.id',
                'name'  => 'CourseLanguages.name',
            ],
            'conditions' => $conditions,
            'join' => [
                [
                    'table' => 'course_languages',
                    'alias' => 'CourseLanguages',
                    'type' => 'INNER',
                    'conditions' => [
                        'CourseLanguages.course_id = Courses.id',
                        'CourseLanguages.alias' => $language,
                    ],
                ],
                [
                    'table' => 'programs',
                    'alias' => 'Programs',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Programs.id = Courses.program_id',
                    ],
                ],
            ],
            'limit' => $payload['limit'],
            'page'  => (int)$payload['page'],

        ]);

        set_result:
        return [
            'count' => $total,
            'items' => $result,
        ];
    }

    public function get_by_id($id, $language = 'en_US')
    {
        return $this->find('all', [
            'fields' => [
                'id'    => 'Courses.id',
                'Courses.program_id',
                'Courses.age_range_from',
                'Courses.age_range_to',
                'Courses.unit',
                'name'  => 'CourseLanguages.name'
            ],
            'conditions' => [
                'Courses.id'        => $id,
                'Courses.enabled'   => true
            ],
            'join'  => $this->joinLanguage($language)
        ]);
    }

    public function create_course($data)
    {
        $message = "";
        $params = [];
        $_course = $this->newEntity($data);
        if ($_course->hasErrors()) {
            $message = "DATA_IS_NOT_SAVE";
            goto set_result;
        }
        $course = $this->save($_course);
        if (!$course) {
            $message = "DATA_IS_NOT_SAVE";
            goto set_result;
        }

        $callbackLanguages = function ($courseLanguages) use ($course) {
            return [
                'course_id' => $course['id'],
                'name'      => $courseLanguages['name'],
                'alias'     => $courseLanguages['alias']
            ];
        };
        $courseLanguages = $this->CourseLanguages->newEntities(array_map($callbackLanguages, $data['name']));
        if (!$this->CourseLanguages->saveMany($courseLanguages)) {
            $message = "DATA_LANGUAGES_IS_NOT_SAVED";
            goto set_result;
        }
        $message = "DATA_IS_SAVED";

        set_result:
        return [
            'message'   => $message,
            'params'    => $course
        ];
    }

    public function edit_course($data)
    {
        $message = ""; 

        $_course = $this->get($data['course_id']);
        $_course = $this->patchEntity($_course, $data);
        if ($_course->hasErrors()) {
            $message = "DATA_IS_NOT_SAVE";
            goto set_result;
        }
        $course = $this->save($_course);
        if (!$course) {
            $message = "DATA_IS_NOT_SAVE";
            goto set_result;
        }

        // delete all language of course
        $this->CourseLanguages->deleteAll([
            'CourseLanguages.course_id' => $data['course_id']
        ]);
        $callbackLanguages = function ($courseLanguages) use ($course) {
            return [
                'course_id' => $course['id'],
                'name'      => $courseLanguages['name'],
                'alias'     => $courseLanguages['alias']
            ];
        };

        //save language
        $courseLanguages = $this->CourseLanguages->newEntities(array_map($callbackLanguages, $data['name']));
        if (!$this->CourseLanguages->saveMany($courseLanguages)) {
            $message = "DATA_LANGUAGES_IS_NOT_SAVED";
            goto set_result;
        }
        $message = "DATA_IS_SAVED";

        set_result:
        return [
            'message'   => $message,
            'params'    => $course
        ];
    }

    public function delete_by_id($id)
    {
        $course = $this->get($id);
        if ($this->delete($course)) {
            return "DATA_IS_DELETED";
        }
        return "DATA_IS_NOT_DELETED";
    }

    public function filter($language)
    {
        $result  = $this->find()
            ->distinct([
                'Courses.age_range_from',
                'Courses.age_range_to',
                'Courses.unit'
            ])->select([
                'Courses.age_range_from',
                'Courses.age_range_to',
                'Courses.unit'
            ])->toArray();
        $unit = MyHelper::getUnits();
        $temp  = array_map(function ($item) use ($unit) {
            return [
                'name' => $item->age_range_from . ' - ' . $item->age_range_to . ' ' . $unit[$item->unit],
                'age_from' => $item->age_range_from,
                'age_to'   => $item->age_range_to,
                'unit'     => $item->unit
            ];
        }, $result);
        return $temp;
    }

    public function convert_course_age_range_to_string($age_range_from, $age_range_to, $unit)
    {
        $unit = $unit == 1 ?  __('month') : __('year');
        return $age_range_from . '-' . $age_range_to . $unit;
    }
 
}

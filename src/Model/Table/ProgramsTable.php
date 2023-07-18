<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\MyHelper\MyHelper;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Programs Model
 *
 * @property \App\Model\Table\ClassesTable&\Cake\ORM\Association\HasMany $Classes
 * @property \App\Model\Table\CoursesTable&\Cake\ORM\Association\HasMany $Courses
 * @property \App\Model\Table\ProgramImagesTable&\Cake\ORM\Association\HasMany $ProgramImages
 * @property \App\Model\Table\ProgramLanguagesTable&\Cake\ORM\Association\HasMany $ProgramLanguages
 *
 * @method \App\Model\Entity\Program newEmptyEntity()
 * @method \App\Model\Entity\Program newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Program[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Program get($primaryKey, $options = [])
 * @method \App\Model\Entity\Program findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Program patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Program[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Program|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Program saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Program[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Program[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Program[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Program[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ProgramsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */

    public function joinLanguage($language)
    {
        return [
            'table' => 'program_languages',
            'alias' => 'ProgramLanguages',
            'type' => 'INNER',
            'conditions' => [
                'ProgramLanguages.program_id = Programs.id',
                'ProgramLanguages.alias' => $language,
            ],
        ];
    }
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('programs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Classes', [
            'foreignKey' => 'program_id',
        ]);
        $this->hasMany('Courses', [
            'foreignKey' => 'program_id',

        ]);
        $this->hasMany('ProgramImages', [
            'foreignKey' => 'program_id',
            'dependent' => true,
        ]);
        $this->hasMany('ProgramLanguages', [
            'foreignKey' => 'program_id',
            'dependent' => true,
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
            ->scalar('title_color')
            ->maxLength('title_color', 10)
            ->allowEmptyString('title_color');

        $validator
            ->scalar('background_color')
            ->maxLength('background_color', 10)
            ->allowEmptyString('background_color');

        $validator
            ->boolean('enabled')
            ->notEmptyString('enabled');

        $validator
            ->integer('created_by')
            ->allowEmptyString('created_by');

        $validator
            ->dateTime('modified_by')
            ->allowEmptyDateTime('modified_by');

        return $validator;
    }

    public function get_list($language, $conditions = array()) // add product admin page
    {
        $programs = $this->find(
            'list',
            [
                'keyField' => 'id',
                'valueField' => function ($row) {
                    return $row->program_languages[0]->name;
                }
            ]
        )
            ->where([$conditions])
            ->contain(
                [
                    'ProgramLanguages' => [
                        'conditions' => ['ProgramLanguages.alias' => $language]
                    ]
                ]
            );
        return $programs;
    }

    public function get_list_pagination($language, $payload)
    {

        $conditions = [
            'Programs.enabled' => true,
        ];
        $total = $this->find('all', [
            'conditions' => [
                'Programs.enabled' => true
            ],
        ])->count();
        $result = [];
        if (!$total) {
            goto set_result;
        }

        if (isset($payload['search']) && !empty($payload['search'])) {
            $conditions['LOWER(ProgramLanguages.name) LIKE'] = '%' . strtolower($payload['search']) . '%';
        }

        $result = $this->find('all', [
            'fields' => [
                'id'   => 'Programs.id',
                'name' => 'ProgramLanguages.name',
            ],
            'conditions' => $conditions,
            'join' => $this->joinLanguage($language),
            'limit' => $payload['limit'],
            'page'  => (int)$payload['page'],

        ]);

        set_result:
        return [
            'count' => $total,
            'items' => $result,
        ];
    }

    public function get_by_id($id, $language = "en_US")
    {
        $temp =  $this->find('all', [
            'fields' => [
                'Programs.id',
                'Programs.title_color',
                'Programs.background_color',
                'name'  => 'ProgramLanguages.name',
                'description'  => 'ProgramLanguages.description',
                'path'         => 'ProgramImages.path'
            ],
            'conditions' => [
                'Programs.id'        => $id,
                'Programs.enabled'   => true
            ],
            'join'          => [
                [
                    'table' => 'program_languages',
                    'alias' => 'ProgramLanguages',
                    'type' => 'INNER',
                    'conditions' => [
                        'ProgramLanguages.program_id = Programs.id',
                        'ProgramLanguages.alias' => $language,
                    ],
                ],
                [
                    'table' => 'program_images',
                    'alias' => 'ProgramImages',
                    'type' => 'LEFT',
                    'conditions' => [
                        'ProgramImages.program_id = Programs.id',
                    ],
                ]
            ],
            'contain'       => [
                'Courses'   => [
                    'CourseLanguages' => [
                        'conditions' => [
                            'CourseLanguages.alias' => $language
                        ]
                    ]
                ]
            ]
        ])->first();
        if (!$temp) {
            return null;
        }
        $courses = [];
        foreach ($temp->courses as $item) {
            $courses[] = [
                'id'    => $item->id,
                'name'  => $item->course_languages[0]->name
            ];
        }
        return [
            'id'                    => $temp->id,
            'title_color'           => $temp->title_color,
            'background_color'      => $temp->background_color,
            'name'                  => $temp->name,
            'description'           => $temp->description,
            'image'                 => $temp->path ?  MyHelper::getUrl() . $temp->path : null,
            'courses'               => $courses
        ];
    }

    public function create_program($data)
    {
        $message = "";
        $params = [];
        $_program = $this->newEntity($data);
        if ($_program->hasErrors()) {
            $message = "DATA_IS_NOT_SAVE";
            goto set_result;
        }
        $program = $this->save($_program);
        if (!$program) {
            $message = "DATA_IS_NOT_SAVE";
            goto set_result;
        }

        $callbackLanguages = function ($programLanguages) use ($program) {
            return [
                'program_id'    => $program['id'],
                'name'          => $programLanguages['name'],
                'alias'         => $programLanguages['alias'],
                'description'   => $programLanguages['description']
            ];
        };
        $programLanguages = $this->ProgramLanguages->newEntities(array_map($callbackLanguages, $data['name']));
        if (!$this->ProgramLanguages->saveMany($programLanguages)) {
            $message = "DATA_LANGUAGES_IS_NOT_SAVED";
            goto set_result;
        }
        $message = "DATA_IS_SAVED";

        set_result:
        return [
            'message'   => $message,
            'params'    => $program
        ];
    }

    public function edit_program($data)
    {
        $message = "";
        $params = [];

        $_program = $this->get($data['id']);
        $_program = $this->patchEntity($_program, $data);
        if ($_program->hasErrors()) {
            $message = "DATA_IS_NOT_SAVE";
            goto set_result;
        }
        $program = $this->save($_program);
        if (!$program) {
            $message = "DATA_IS_NOT_SAVE";
            goto set_result;
        }

        // delete all language of program
        $this->ProgramLanguages->deleteAll([
            'ProgramLanguages.program_id' => $data['id']
        ]);
        $callbackLanguages = function ($programLanguages) use ($program) {
            return [
                'program_id'    => $program['id'],
                'name'          => $programLanguages['name'],
                'alias'         => $programLanguages['alias'],
                'description'   => $programLanguages['description']
            ];
        };

        //save language
        $programLanguages = $this->ProgramLanguages->newEntities(array_map($callbackLanguages, $data['name']));
        if (!$this->ProgramLanguages->saveMany($programLanguages)) {
            $message = "DATA_LANGUAGES_IS_NOT_SAVED";
            goto set_result;
        }
        $message = "DATA_IS_SAVED";

        set_result:
        return [
            'message'   => $message,
            'params'    => $program
        ];
    }

    public function delete_by_id($id)
    {
        $program = $this->get($id);
        if ($this->delete($program)) {
            return "DATA_IS_DELETED";
        }
        return "DATA_IS_NOT_DELETED";
    }

    public function discover($language, $payload)
    { 
        $conditions = [
            'Programs.enabled' => true,
        ];
        $limit = null;
        if (isset($payload['limit']) && !empty($payload['limit'])) {
            $limit = $payload['limit'];
        }

        $temp = $this->find('all', [
            'fields' => [
                'Programs.id',
                'Programs.title_color',
                'Programs.background_color',
            ],
            'limit' => $limit,
            'conditions' => $conditions,
            'contain' => [
                'ProgramLanguages' => [
                    'fields' => [
                        'ProgramLanguages.id',
                        'ProgramLanguages.program_id',
                        'ProgramLanguages.name',
                    ],
                    'conditions' => [
                        'ProgramLanguages.alias' => $language,
                    ],
                ],
                'Courses' => [
                    'fields' => [
                        'Courses.id',
                        'Courses.program_id',
                        'Courses.age_range_from',
                        'Courses.age_range_to',
                        'Courses.unit',
                    ],
                    'conditions' => [
                        'Courses.enabled' => true,
                    ],
                    'CourseLanguages' => [
                        'fields' => [
                            'CourseLanguages.id',
                            'CourseLanguages.course_id',
                            'CourseLanguages.name',
                        ],
                        'conditions' => [
                            'CourseLanguages.alias' => $language,
                        ],
                    ],
                ]
            ],
        ]);

        $programs = [];
        $units = MyHelper::getUnits();
        foreach ($temp->toList() as $prog) {
            $program = [
                'id'   => $prog->id,
                'name' => $prog->program_languages[0]->name,
                'title_color'       => $prog->title_color,
                'background_color'  => $prog->background_color,
                'courses'           => []
            ];

            foreach ($prog->courses as $cous) {

                $program['courses'][] = [
                    'id'   => $cous->id,
                    'name' => $cous->course_languages[0]->name,
                    'age_range_from'    => $cous->age_range_from,
                    'age_range_to'      => $cous->age_range_to,
                    'unit'              => $units[$cous->unit]
                ];
            }

            $programs[] = $program;
        }

        return $programs;
    }
}

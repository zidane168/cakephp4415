<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\MyHelper\MyHelper;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Svg\Tag\UseTag;

/**
 * CidcParents Model
 *
 * @method \App\Model\Entity\CidcParent newEmptyEntity()
 * @method \App\Model\Entity\CidcParent newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\CidcParent[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CidcParent get($primaryKey, $options = [])
 * @method \App\Model\Entity\CidcParent findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\CidcParent patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CidcParent[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\CidcParent|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CidcParent saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CidcParent[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CidcParent[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\CidcParent[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CidcParent[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CidcParentsTable extends Table
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

        $this->setTable('cidc_parents');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('CidcParentImages', [
            'foreignKey' => 'cidc_parent_id',
        ]);
        $this->hasMany('Kids', [
            'foreignKey' => 'cidc_parent_id',
        ]);
        $this->hasMany('CidcParentLanguages', [
            'foreignKey' => 'cidc_parent_id',
            'dependents'    => true
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
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
            ->boolean('gender')
            ->notEmptyString('gender');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }

    public function get_list($language, $conditions = array()) // add product admin page
    {
        $cidcparents = $this->find(
            'list',
            [
                'keyField' => 'id',
                'valueField' => function ($row) {
                    return $row->cidc_parent_languages[0]->name . " (" . __d('parent', 'phone_number') . ": " . $this->format_phone_number($row->user->phone_number) . ")";
                }
            ]
        )
            ->where([$conditions])
            ->contain(
                [
                    'Users' => [
                        'fields' => ['Users.phone_number']
                    ],
                    'CidcParentLanguages' => [
                        'conditions' => ['CidcParentLanguages.alias' => $language]
                    ]
                ]
            );

        return $cidcparents;
    }

    public function add($gender, $user_id, $zh_HK_name, $en_US_name, $address = null)
    {

        $db = $this->getConnection();
        $db->begin();

        $status = 200;
        $message = __('data_is_saved');
        $params = [];
        $parent = [
            'gender' => $gender,
            'user_id' => $user_id,
            'address' => $address
        ];

        $parent = $this->newEntity($parent);
        if ($model = $this->save($parent)) {

            $parent_languages[] = [
                'cidc_parent_id' => $model->id,
                'alias' => 'zh_HK',
                'name' => $zh_HK_name,
            ]; 
            $parent_languages[] = [
                'cidc_parent_id' => $model->id,
                'alias' => 'en_US',
                'name' => $en_US_name,
            ];

            $parent_languages = $this->CidcParentLanguages->newEntities($parent_languages);

            if (!$this->CidcParentLanguages->saveMany($parent_languages)) {
                $db->rollback();
                $status = 500;
                $message = __('data_is_not_saved') . ' Cidc Parents Languages';
                goto return_data;
            }

            $db->commit();
            return [
                'status' => $status,
                'message' => $message,
                'params' => $model,
            ];
        }

        return_data:
        return [
            'status' => $status,
            'message' => $message,
            'params' => $params,
        ];
    }

    public function get_by_user_id($id, $language)
    {
        return $this->find('all', [
            'conditions' => [
                'CidcParents.user_id' => $id,
            ],
            'fields' => [
                'CidcParents.id',
                'CidcParents.gender',
                'CidcParents.address'
            ],
            'contain' => [
                'CidcParentLanguages' => [
                    'fields' => [
                        'CidcParentLanguages.cidc_parent_id',
                        'CidcParentLanguages.name',
                        'CidcParentLanguages.alias',
                    ],
                ],
                'CidcParentImages' => [
                    'fields' => [
                        'CidcParentImages.cidc_parent_id',
                        'CidcParentImages.path',
                    ],
                ],
                'Kids' => [
                    'fields' => [
                        'Kids.id',
                        'Kids.cidc_parent_id',
                        'Kids.relationship_id',
                        'Kids.gender',
                        'Kids.dob',
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
                                'RelationshipLanguages.alias' => $language
                            ],
                        ],
                    ],
                    'KidLanguages' => [
                        'fields' => [
                            'KidLanguages.kid_id',
                            'KidLanguages.name', 
                        ],
                        'conditions' => [
                            'KidLanguages.alias' => $language
                        ],
                    ],
                    'KidImages' => [
                        'fields' => [
                            'KidImages.kid_id',
                            'KidImages.path',
                        ],
                    ],
                ],
            ],
        ])->first();
    }

    public function edit_profile($user_id, $data)
    {
        $status = 200;
        $message = 'DATA_IS_SAVED';
        $params = [];
        $parent = $this->find('all', [
            'conditions' => [
                'CidcParents.user_id' => $user_id,
            ],
            'contain' => [
                'CidcParentLanguages',
                'CidcParentImages'
            ]
        ])->first();
        foreach ($parent->cidc_parent_languages as &$item) {
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
        unset($data['phone_number']);
        unset($data['email']);
        if (isset($data['gender']) && !empty($data['gender'])) {
            $data['gender'] = (int)$data['gender'];
        }
        $parent = $this->patchEntity($parent, $data);
        if (isset($data['gender_id']) && !empty($data['gender_id'])) {
            $parent->gender = $data['gender_id'];
        }
        $db = $this->getConnection();
        $db->begin();
        if (!$this->save($parent)) {
            $db->rollback();
            $message = 'DATA_IS_NOT_SAVED' .  json_encode($parent->getErrors());
            $status = 999;
            goto set_result;
        }
        if (!$this->CidcParentLanguages->saveMany($parent->cidc_parent_languages)) {
            $db->rollback();
            $message = 'DATA_LANGUAGES_IS_NOT_SAVED';
            $status = 999;
            goto set_result;
        }
        $db->commit();
        set_result:
        return [
            'status'    => $status,
            'message'   => $message,
            'params'    => $parent->id
        ];
    }

    public function get_id_by_user($user_id)
    {
        $temp = $this->find('all', [
            'conditions' => [
                'CidcParents.user_id' => $user_id,
            ],
            'fields' => [
                'CidcParents.id'
            ],
        ])->first();
        return $temp ? $temp->id : null;
    }
}

<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use App\MyHelper\MyHelper;

/**
 * Professionals Model
 *
 * @property \App\Model\Table\ProfessionalLanguagesTable&\Cake\ORM\Association\HasMany $ProfessionalLanguages
 * @property \App\Model\Table\ProfessionalsCertificationsTable&\Cake\ORM\Association\HasMany $ProfessionalsCertifications
 *
 * @method \App\Model\Entity\Professional newEmptyEntity()
 * @method \App\Model\Entity\Professional newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Professional[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Professional get($primaryKey, $options = [])
 * @method \App\Model\Entity\Professional findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Professional patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Professional[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Professional|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Professional saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Professional[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Professional[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Professional[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Professional[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ProfessionalsTable extends Table
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

        $this->setTable('professionals');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('ProfessionalLanguages', [
            'foreignKey' => 'professional_id',
            'dependent'  => true
        ]);
        $this->hasMany('ProfessionalsCertifications', [
            'foreignKey' => 'professional_id',
            'dependent'  => true
        ]);

        $this->hasMany('ProfessionalImages', [
            'foreignKey' => 'professional_id',
            'dependent'  => true
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

        // $validator
        //     ->boolean('type')
        //     ->notEmptyString('type');

        $validator
            ->integer('created_by')
            ->allowEmptyString('created_by');

        $validator
            ->integer('modified_by')
            ->allowEmptyString('modified_by');

        return $validator;
    }


    public function get_list_pagination($language, $payload)
    {
        $conditions  = ['Professionals.enabled' => true];

        $result = [];
        $temp = [];
        if (isset($payload['search']) && !empty($payload['search'])) {
            $conditions['LOWER(ProfessionalLanguages.name) LIKE'] = '%' . strtolower($payload['search']) . '%';
        }

        if (isset($payload['type']) && !empty($payload['type'])) {
            $conditions['Professionals.type'] = $payload['type'];
        }
        $total = $this->find('all', [
            'conditions' => $conditions,
            'join'  => [
                [
                    'table' => 'professional_languages',
                    'alias' => 'ProfessionalLanguages',
                    'type' => 'LEFT',
                    'conditions' => [
                        'ProfessionalLanguages.professional_id = Professionals.id',
                        'ProfessionalLanguages.alias' => $language,
                    ],
                ],
                [
                    'table' => 'professional_images',
                    'alias' => 'ProfessionalImages',
                    'type' => 'LEFT',
                    'conditions' => [
                        'ProfessionalImages.professional_id = Professionals.id',
                    ],
                ]
            ],
        ])->count();

        if (!$total) {
            goto set_result;
        }

        $url = MyHelper::getUrl();
        $result = $this->find('all', [
            'fields' => [
                'id'        => 'Professionals.id',
                'type'      => 'Professionals.type',
                'gender'    => 'Professionals.gender',
                'image'     => "ProfessionalImages.path",
                'name'      => 'ProfessionalLanguages.name',
                'title'     => 'ProfessionalLanguages.title',
                'nick_name' => 'ProfessionalLanguages.nick_name',
            ],
            'conditions' => $conditions,
            'join'  => [
                [
                    'table' => 'professional_languages',
                    'alias' => 'ProfessionalLanguages',
                    'type' => 'LEFT',
                    'conditions' => [
                        'ProfessionalLanguages.professional_id = Professionals.id',
                        'ProfessionalLanguages.alias' => $language,
                    ],
                ],
                [
                    'table' => 'professional_images',
                    'alias' => 'ProfessionalImages',
                    'type' => 'LEFT',
                    'conditions' => [
                        'ProfessionalImages.professional_id = Professionals.id',
                    ],
                ]
            ],
            'limit' => $payload['limit'],
            'page' => (int)$payload['page']
        ]);


        $genders  = MyHelper::getGenders();
        foreach ($result as $pro) {
            $temp[] = [
                'id'        => $pro->id,
                'type'      => $pro->type,
                'gender'    => $genders[$pro->gender],
                'gender_id' => (int)$pro->gender,
                'image'     => $pro->image ? $url . $pro->image : null,
                'name'      => $pro->name,
                'title'     => $pro->title,
                'nick_name' => $pro->nick_name,
            ];
        }

        set_result:
        return [
            'count' => $total,
            'items' => $temp,
        ];
    }

    public function get_by_id($language, $payload)
    {
        $conditions  = [
            'Professionals.enabled' => true,
            'Professionals.id'      => $payload['id']
        ];
        $url = MyHelper::getUrl();
        $result = $this->find('all', [
            'fields' => [
                'Professionals.id',
                'type'      => 'Professionals.type',
                'gender'    => 'Professionals.gender',
                'image'     => "CONCAT('$url', ProfessionalImages.path)",
                'name'      => 'ProfessionalLanguages.name',
                'title'     => 'ProfessionalLanguages.title',
                'nick_name' => 'ProfessionalLanguages.nick_name',
                'description' => 'ProfessionalLanguages.description',
            ],
            'conditions' => $conditions,
            'join'  => [
                [
                    'table' => 'professional_languages',
                    'alias' => 'ProfessionalLanguages',
                    'type' => 'LEFT',
                    'conditions' => [
                        'ProfessionalLanguages.professional_id = Professionals.id',
                        'ProfessionalLanguages.alias' => $language,
                    ],
                ],
                [
                    'table' => 'professional_images',
                    'alias' => 'ProfessionalImages',
                    'type' => 'INNER',
                    'conditions' => [
                        'ProfessionalImages.professional_id = Professionals.id',
                    ],
                ]
            ],
            'contain' => [
                'ProfessionalsCertifications' => [
                    'fields' => [
                        'ProfessionalsCertifications.id',
                        'ProfessionalsCertifications.professional_id'
                    ],
                    'ProfessionalCertificationLanguages' => [
                        'fields' => [
                            'ProfessionalCertificationLanguages.id',
                            'ProfessionalCertificationLanguages.professional_certification_id',
                            'ProfessionalCertificationLanguages.name'
                        ],
                        'conditions' => [
                            'ProfessionalCertificationLanguages.alias' => $language
                        ]

                    ]
                ]
            ]
        ])->first();
        if (!$result) {
            return null;
        }
        $func = function ($_item) {
            return [
                'id' => $_item['id'],
                'name' => $_item['professional_certification_languages'][0]['name']
            ];
        };
        $result['professionals_certifications'] = array_map($func, $result['professionals_certifications']);
        $genders = MyHelper::getGenders();
        $result['gender'] = $genders[$result['gender']];
        return $result;
    }
}

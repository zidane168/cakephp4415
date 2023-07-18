<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Districts Model
 *
 * @property \App\Model\Table\RegionsTable&\Cake\ORM\Association\BelongsTo $Regions
 * @property \App\Model\Table\DistrictLanguagesTable&\Cake\ORM\Association\HasMany $DistrictLanguages
 * @property \App\Model\Table\ParkingRequirementsTable&\Cake\ORM\Association\HasMany $ParkingRequirements
 * @property \App\Model\Table\ProductsTable&\Cake\ORM\Association\HasMany $Products
 * @property \App\Model\Table\SubdistrictsTable&\Cake\ORM\Association\HasMany $Subdistricts
 * @property \App\Model\Table\TransactionsTable&\Cake\ORM\Association\HasMany $Transactions
 *
 * @method \App\Model\Entity\District newEmptyEntity()
 * @method \App\Model\Entity\District newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\District[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\District get($primaryKey, $options = [])
 * @method \App\Model\Entity\District findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\District patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\District[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\District|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\District saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\District[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\District[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\District[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\District[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DistrictsTable extends Table
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

        $this->setTable('districts');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('DistrictLanguages', [
            'dependent' => true,  /* Add this line for remove all */
            'foreignKey' => 'district_id',
        ]);
        $this->hasMany('Subdistricts', [
            'foreignKey' => 'district_id',
        ]);


        // belongto;
        $this->addBehavior('WhoDidIt');

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
            ->scalar('slug')
            ->maxLength('slug', 191)
            ->allowEmptyString('slug');

        $validator
            ->boolean('enabled')
            ->allowEmptyString('enabled');

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
        return $rules;
    }

    public function get_list($language, $conditions = array()) // add product admin page
    {
        $districts = $this->find(
            'list',
            [
                'keyField' => 'id',
                'valueField' => function ($row) {
                    return $row->district_languages[0]->name;
                }
            ]
        )
            ->where([$conditions])
            ->contain(
                [
                    'DistrictLanguages' => [
                        'conditions' => ['DistrictLanguages.alias' => $language]
                    ]
                ]
            );

        return $districts;
    }

    public function get_list_pagination($payload)
    {
        $conditions = [
            'Districts.region_id' => $payload['region_id']
        ];
        if (isset($payload['search']) && !empty($payload['search'])) {
            $conditions['LOWER(DistrictLanguages.name) LIKE'] = '%' . strtolower($payload['search']) . '%';
        }

        $join = [
            'table' => 'district_languages',
            'alias' => 'DistrictLanguages',
            'type' => 'INNER',
            'conditions' => [
                'DistrictLanguages.district_id = Districts.id',
                'DistrictLanguages.alias' => $payload['language'],
            ],
        ];

        $result = $this->find('all', [
            'conditions' => $conditions,
            'fields' => [
                'Districts.id',
                'DistrictLanguages.name'
            ],
            'join' => $join,
            'limit' => $payload['limit'],
            'page'  => (int)$payload['page'],
        ]);

        $total = $this->find('all', [
            'conditions' => [
                'Districts.region_id' => $payload['region_id']
            ],
        ])->count();

        $items = [];
        foreach ($result as $val) {
            $items[] = [
                'id'                    => $val->id,
                'name'                  => $val['DistrictLanguages']['name'],
            ];
        }

        return [
            'count' => $total,
            'items' => $items,
        ];
    }

    public function get_by_id($id, $language)
    {
        $status = 500;
        $message = "";
        $params = (object)[];

        if ($id == null) goto set_result;
        $district = $this->find(
            'all',
            [
                'fields' => [
                    'Districts.id',
                    'DistrictLanguages.name'
                ],
                'conditions' => [
                    'Districts.enabled' => true
                ],
                'join' => [
                    'table' => 'district_languages',
                    'alias' => 'DistrictLanguages',
                    'conditions' => [
                        'DistrictLanguages.district_id' => $id,
                        'DistrictLanguages.alias'    => $language
                    ]
                ]
            ]
        )->toArray();

        if (!$district) {
            $message = __('invalid_id');
            goto set_result;
        }

        $status = 200;
        $message = __('retrieve_data_successfully');
        $params = [
            'id'        => $id,
            'name'      => $district[0]['DistrictLanguages']['name']
        ];

        set_result:
        return [
            'status'    => $status,
            'message'   => $message,
            'params'    => $params
        ];
    }
}

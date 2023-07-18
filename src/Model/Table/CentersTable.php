<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Routing\Router;

/**
 * Centers Model
 *
 * @property \App\Model\Table\DistrictsTable&\Cake\ORM\Association\BelongsTo $Districts
 * @property \App\Model\Table\AdministratorsTable&\Cake\ORM\Association\HasMany $Administrators
 * @property \App\Model\Table\CenterFilesTable&\Cake\ORM\Association\HasMany $CenterFiles
 * @property \App\Model\Table\CenterLanguagesTable&\Cake\ORM\Association\HasMany $CenterLanguages
 *
 * @method \App\Model\Entity\Center newEmptyEntity()
 * @method \App\Model\Entity\Center newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Center[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Center get($primaryKey, $options = [])
 * @method \App\Model\Entity\Center findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Center patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Center[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Center|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Center saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Center[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Center[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Center[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Center[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CentersTable extends Table
{
    public function joinLanguage($language)
    {
        return [
            [
                'table' => 'center_languages',
                'alias' => 'CenterLanguages',
                'type' => 'LEFT',
                'conditions' => [
                    'CenterLanguages.center_id = Centers.id',
                    'CenterLanguages.alias' => $language,
                ],
            ],
            [
                'table' => 'center_files',
                'alias' => 'CenterFiles',
                'type' => 'LEFT',
                'conditions' => [
                    'CenterFiles.center_id = Centers.id',
                ],
            ]

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

        $this->setTable('centers');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Districts', [
            'foreignKey' => 'district_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Administrators', [
            'foreignKey' => 'center_id',
        ]);
        $this->hasMany('CenterFiles', [
            'foreignKey' => 'center_id',
            'dependent'  => true
        ]);
        $this->hasMany('CenterLanguages', [
            'foreignKey' => 'center_id',
            'dependent'  => true
        ]);
        $this->hasMany('Staffs', [
            'foreignKey' => 'center_id',
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
            ->scalar('code')
            ->maxLength('code', 10)
            ->allowEmptyString('code');

        $validator
            ->scalar('account')
            ->maxLength('account', 191)
            ->allowEmptyString('account');

        $validator
            ->scalar('username')
            ->maxLength('username', 191)
            ->allowEmptyString('username');

        $validator
            ->scalar('bank_name')
            ->maxLength('bank_name', 191)
            ->allowEmptyString('bank_name');

        $validator
            ->decimal('latitude')
            ->notEmptyString('latitude');

        $validator
            ->decimal('longitude')
            ->notEmptyString('longitude');

        $validator
            ->scalar('telephone')
            ->maxLength('telephone', 20)
            ->allowEmptyString('telephone');

        $validator
            ->scalar('mobile_phone')
            ->maxLength('mobile_phone', 20)
            ->allowEmptyString('mobile_phone');

        $validator
            ->email('email')
            ->allowEmptyString('email');

        $validator
            ->scalar('whatsapp')
            ->maxLength('whatsapp', 20)
            ->allowEmptyString('whatsapp');

        $validator
            ->notEmptyString('sort');

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
        $rules->add($rules->isUnique(['username']), ['errorField' => 'username']);
        $rules->add($rules->existsIn(['district_id'], 'Districts'), ['errorField' => 'district_id']);

        return $rules;
    }

    public function get_list($language, $conditions = array()) // add product admin page
    {
        $centers = $this->find(
            'list',
            [
                'keyField' => 'id',
                'valueField' => function ($row) {
                    return $row->center_languages[0]->name;
                }
            ]
        )
            ->where([$conditions])
            ->contain(
                [
                    'CenterLanguages' => [
                        'conditions' => ['CenterLanguages.alias' => $language]
                    ]
                ]
            );
        return $centers;
    }

    // fill in combobox
    public function get_list_belong_administrator($language, $administrator_id) // add product admin page
    {
        $centers = $this->find('all', [
            'fields' => [
                'Centers.id',
                'AdministratorManageCenters.administrator_id',
            ],
            'conditions' => [ 
                'Centers.enabled' => true 
            ],
            'contain' => [
                'CenterLanguages' => [
                    'conditions' => ['CenterLanguages.alias' => $language],
                    'fields' => [
                        'CenterLanguages.center_id',
                        'CenterLanguages.name',
                    ],
                ],
            ],
            'join'  => [
                'table' => 'administrator_manage_centers',
                'alias' => 'AdministratorManageCenters',
                'type' => 'INNER',
                'conditions' => [
                    'AdministratorManageCenters.center_id = Centers.id', 
                ],
            ]
        ])->toArray();
 
        $list = [];
        foreach ($centers as $center) {
            $list[$center->id] = $center->center_languages[0]->name;
        }

        return $list;
    } 

    public function get_list_pagination($language, $payload)
    { 
        // $total = $this->find('all', [
        //     'conditions' => [
        //         'Centers.enabled' => true
        //     ],
        // ])->count();
        $result = []; 
        
        if (isset($payload['search']) && !empty($payload['search'])) {
            $conditions['LOWER(CenterLanguages.name) LIKE'] = '%' . strtolower($payload['search']) . '%';
        }
        $result = $this->find('all', [
            'fields' => [
                'id'            => 'Centers.id',
                'sort'          => 'Centers.sort',
                'longitude'     => 'Centers.longitude',
                'latitude'      => 'Centers.latitude',
                'name'          => 'CenterLanguages.name', 
                'phone_us'      => 'Centers.phone_us',
                'fax_us'        => 'Centers.fax_us',
                'visit_us'      => 'Centers.visit_us',
                'mail_us'       => 'Centers.mail_us',
            ],
            'conditions' => [
                'Centers.enabled' => true,
            ],
            'join'  => $this->joinLanguage($language),
            'limit' => $payload['limit'],
            'page'  => (int)$payload['page'],
            'order'  => [
                'Centers.sort ASC'
            ]
        ]);
        set_result:
        return [
            'count' => $result->count(),
            'items' => $result->toArray(),
        ];
    }

    public function get_by_id($id, $language = "en_US")
    {
        $url = Router::url('/', true);
        return $this->find('all', [
            'fields' => [
                'id'    => 'Centers.id',
                'Centers.code',
                'Centers.account',
                'Centers.username',
                'Centers.bank_name',
                'Centers.latitude',
                'Centers.longitude',
                'Centers.phone_us',
                'Centers.fax_us',
                'Centers.visit_us',
                'Centers.mail_us',
                'Centers.district_id',
                'Centers.sort',
                'name'      => 'CenterLanguages.name', 
                'file'      => "CONCAT('$url', CenterFiles.path)"
            ],
            'conditions' => [
                'Centers.id'        => $id,
                'Centers.enabled'   => true
            ],
            'join'  => $this->joinLanguage($language)
        ])->first();
    }
}

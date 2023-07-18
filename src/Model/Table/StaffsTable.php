<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Staffs Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\StaffLanguagesTable&\Cake\ORM\Association\HasMany $StaffLanguages
 * @property \App\Model\Table\StudentJoinClassesTable&\Cake\ORM\Association\HasMany $StudentJoinClasses
 *
 * @method \App\Model\Entity\Staff newEmptyEntity()
 * @method \App\Model\Entity\Staff newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Staff[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Staff get($primaryKey, $options = [])
 * @method \App\Model\Entity\Staff findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Staff patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Staff[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Staff|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Staff saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Staff[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Staff[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Staff[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Staff[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class StaffsTable extends Table
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

        $this->setTable('staffs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Centers', [
            'foreignKey' => 'center_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('StaffLanguages', [
            'foreignKey' => 'staff_id',
            'dependent'  => true
        ]);
        $this->hasMany('StudentJoinClasses', [
            'foreignKey' => 'staff_id',
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
            ->requirePresence('gender', 'create')
            ->notEmptyString('gender');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }

    function get_center_id_by_user($user_id)
    {
        $staff = $this->find('all', [
            'fields' => [
                'Staffs.center_id'
            ],
            'conditions' => [
                'Staffs.user_id' => $user_id,
            ]
        ])->first();
        return $staff->center_id;
    }

    function get_by_user_id($user_id, $language)  {
        $staff = $this->find('all', [ 
            'conditions' => [
                'Staffs.user_id' => $user_id,
            ],
            'fields' => [
                'Staffs.id',
                'Staffs.center_id',
                'Staffs.gender',
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
            ],
        ])->first();
        return $staff;
    }
}

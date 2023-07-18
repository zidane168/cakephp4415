<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PrivacyPolicies Model
 *
 * @property \App\Model\Table\PrivacyPolicyLanguagesTable&\Cake\ORM\Association\HasMany $PrivacyPolicyLanguages
 *
 * @method \App\Model\Entity\PrivacyPolicy newEmptyEntity()
 * @method \App\Model\Entity\PrivacyPolicy newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\PrivacyPolicy[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PrivacyPolicy get($primaryKey, $options = [])
 * @method \App\Model\Entity\PrivacyPolicy findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\PrivacyPolicy patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PrivacyPolicy[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\PrivacyPolicy|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PrivacyPolicy saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PrivacyPolicy[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\PrivacyPolicy[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\PrivacyPolicy[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\PrivacyPolicy[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PrivacyPoliciesTable extends Table
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

        $this->setTable('privacy_policies');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('PrivacyPolicyLanguages', [
            'foreignKey' => 'privacy_policy_id',
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
            ->integer('created_by')
            ->allowEmptyString('created_by');

        $validator
            ->integer('modified_by')
            ->allowEmptyString('modified_by');

        return $validator;
    }

    public function get_detail($languages)
    {
        $conditions = [
            'PrivacyPolicies.enabled' => true,
        ];
        $result = $this->find('all', [
            'fields' => [
                'id'        => 'PrivacyPolicies.id',
                'title'     => 'PrivacyPolicyLanguages.title',
                'content'     => 'PrivacyPolicyLanguages.content',
            ],
            'conditions' => $conditions,
            'join' => [
                'table' => 'privacy_policy_languages',
                'alias' => 'PrivacyPolicyLanguages',
                'type' => 'LEFT',
                'conditions' => [
                    'PrivacyPolicyLanguages.privacy_policy_id = PrivacyPolicies.id',
                    'PrivacyPolicyLanguages.alias' => $languages,
                ],
            ]
        ])->first();
        return $result;
    }
}

<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PhoneTokens Model
 *
 * @method \App\Model\Entity\PhoneToken newEmptyEntity()
 * @method \App\Model\Entity\PhoneToken newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\PhoneToken[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PhoneToken get($primaryKey, $options = [])
 * @method \App\Model\Entity\PhoneToken findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\PhoneToken patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PhoneToken[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\PhoneToken|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PhoneToken saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PhoneToken[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\PhoneToken[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\PhoneToken[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\PhoneToken[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PhoneTokensTable extends Table
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

        $this->setTable('phone_tokens');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

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
            ->integer('phone')
            ->requirePresence('phone', 'create')
            ->notEmptyString('phone');

        $validator
            ->scalar('token')
            ->maxLength('token', 512)
            ->requirePresence('token', 'create')
            ->notEmptyString('token');

        $validator
            ->boolean('is_used')
            ->allowEmptyString('is_used');

        return $validator;
    }

    public function update_is_used($phone, $token)
    {
        $this->updateAll([
            'is_used' => 1
        ], [
            'phone_number' => $phone,
            'token' => $token
        ]);
    }
}

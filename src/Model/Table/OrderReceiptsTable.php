<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * OrderReceipts Model
 *
 * @property \App\Model\Table\OrdersTable&\Cake\ORM\Association\BelongsTo $Orders
 *
 * @method \App\Model\Entity\OrderReceipt newEmptyEntity()
 * @method \App\Model\Entity\OrderReceipt newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\OrderReceipt[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\OrderReceipt get($primaryKey, $options = [])
 * @method \App\Model\Entity\OrderReceipt findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\OrderReceipt patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\OrderReceipt[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\OrderReceipt|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\OrderReceipt saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\OrderReceipt[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\OrderReceipt[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\OrderReceipt[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\OrderReceipt[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class OrderReceiptsTable extends Table
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

        $this->setTable('order_receipts');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Orders', [
            'foreignKey' => 'order_id',
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
            ->scalar('file_name')
            ->maxLength('file_name', 100)
            ->requirePresence('file_name', 'create')
            ->notEmptyFile('file_name');

        $validator
            ->scalar('path')
            ->allowEmptyString('path');

        $validator
            ->scalar('ext')
            ->maxLength('ext', 10)
            ->allowEmptyString('ext');

        $validator
            ->integer('size')
            ->allowEmptyString('size');

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
        $rules->add($rules->existsIn(['order_id'], 'Orders'), ['errorField' => 'order_id']);

        return $rules;
    }
}

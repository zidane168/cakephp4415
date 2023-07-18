<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RescheduleHistories Model
 *
 * @property \App\Model\Table\FromCidcClassesTable&\Cake\ORM\Association\BelongsTo $FromCidcClasses
 * @property \App\Model\Table\ToCidcClassesTable&\Cake\ORM\Association\BelongsTo $ToCidcClasses
 * @property \App\Model\Table\KidsTable&\Cake\ORM\Association\BelongsTo $Kids
 *
 * @method \App\Model\Entity\RescheduleHistory newEmptyEntity()
 * @method \App\Model\Entity\RescheduleHistory newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\RescheduleHistory[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\RescheduleHistory get($primaryKey, $options = [])
 * @method \App\Model\Entity\RescheduleHistory findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\RescheduleHistory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\RescheduleHistory[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\RescheduleHistory|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RescheduleHistory saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RescheduleHistory[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\RescheduleHistory[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\RescheduleHistory[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\RescheduleHistory[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RescheduleHistoriesTable extends Table
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

        $this->setTable('reschedule_histories');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('RescheduleHistoryFiles', [
            'foreignKey' => 'reschedule_history_id',
            'dependent'  => true
        ]);

        $this->belongsTo('Kids', [
            'foreignKey' => 'kid_id',
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
            ->date('date_from')
            ->allowEmptyDateTime('date_from');

        $validator
            ->date('date_to')
            ->allowEmptyDateTime('date_to');

        $validator
            ->integer('status')
            ->notEmptyString('status');

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
        $rules->add($rules->existsIn(['kid_id'], 'Kids'), ['errorField' => 'kid_id']);

        return $rules;
    }
}

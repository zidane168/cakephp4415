<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RescheduleHistoryFiles Model
 *
 * @property \App\Model\Table\RescheduleHistoriesTable&\Cake\ORM\Association\BelongsTo $RescheduleHistories
 *
 * @method \App\Model\Entity\RescheduleHistoryFile newEmptyEntity()
 * @method \App\Model\Entity\RescheduleHistoryFile newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\RescheduleHistoryFile[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\RescheduleHistoryFile get($primaryKey, $options = [])
 * @method \App\Model\Entity\RescheduleHistoryFile findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\RescheduleHistoryFile patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\RescheduleHistoryFile[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\RescheduleHistoryFile|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RescheduleHistoryFile saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RescheduleHistoryFile[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\RescheduleHistoryFile[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\RescheduleHistoryFile[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\RescheduleHistoryFile[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class RescheduleHistoryFilesTable extends Table
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

        $this->setTable('reschedule_history_files');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('RescheduleHistories', [
            'foreignKey' => 'reschedule_history_id',
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
    // public function buildRules(RulesChecker $rules): RulesChecker
    // {
    //     $rules->add($rules->existsIn(['reschedule_history_id'], 'RescheduleHistories'), ['errorField' => 'reschedule_history_id']);

    //     return $rules;
    // }
}

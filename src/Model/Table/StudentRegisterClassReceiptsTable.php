<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * StudentRegisterClassReceipts Model
 *
 * @property \App\Model\Table\StudentRegisterClassesTable&\Cake\ORM\Association\BelongsTo $StudentRegisterClasses
 *
 * @method \App\Model\Entity\StudentRegisterClassReceipt newEmptyEntity()
 * @method \App\Model\Entity\StudentRegisterClassReceipt newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\StudentRegisterClassReceipt[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\StudentRegisterClassReceipt get($primaryKey, $options = [])
 * @method \App\Model\Entity\StudentRegisterClassReceipt findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\StudentRegisterClassReceipt patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\StudentRegisterClassReceipt[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\StudentRegisterClassReceipt|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StudentRegisterClassReceipt saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StudentRegisterClassReceipt[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\StudentRegisterClassReceipt[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\StudentRegisterClassReceipt[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\StudentRegisterClassReceipt[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class StudentRegisterClassReceiptsTable extends Table
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

        $this->setTable('student_register_class_receipts');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('StudentRegisterClasses', [
            'foreignKey' => 'student_register_class_id',
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
        $rules->add($rules->existsIn(['student_register_class_id'], 'StudentRegisterClasses'), ['errorField' => 'student_register_class_id']);

        return $rules;
    }
}

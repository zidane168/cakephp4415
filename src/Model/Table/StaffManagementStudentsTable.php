<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * StaffManagementStudents Model
 *
 * @property \App\Model\Table\CidcClassesTable&\Cake\ORM\Association\BelongsTo $CidcClasses
 * @property \App\Model\Table\CidcParentsTable&\Cake\ORM\Association\BelongsTo $CidcParents
 * @property \App\Model\Table\KidsTable&\Cake\ORM\Association\BelongsTo $Kids
 *
 * @method \App\Model\Entity\StaffManagementStudent newEmptyEntity()
 * @method \App\Model\Entity\StaffManagementStudent newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\StaffManagementStudent[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\StaffManagementStudent get($primaryKey, $options = [])
 * @method \App\Model\Entity\StaffManagementStudent findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\StaffManagementStudent patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\StaffManagementStudent[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\StaffManagementStudent|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StaffManagementStudent saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StaffManagementStudent[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\StaffManagementStudent[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\StaffManagementStudent[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\StaffManagementStudent[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class StaffManagementStudentsTable extends Table
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

        $this->setTable('staff_management_students');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('CidcClasses', [
            'foreignKey' => 'cidc_class_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CidcParents', [
            'foreignKey' => 'cidc_parent_id',
            'joinType' => 'INNER',
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
        $rules->add($rules->existsIn(['cidc_class_id'], 'CidcClasses'), ['errorField' => 'cidc_class_id']);
        $rules->add($rules->existsIn(['cidc_parent_id'], 'CidcParents'), ['errorField' => 'cidc_parent_id']);
        $rules->add($rules->existsIn(['kid_id'], 'Kids'), ['errorField' => 'kid_id']);

        return $rules;
    }
}

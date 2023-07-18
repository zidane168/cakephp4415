<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CenterFiles Model
 *
 * @property \App\Model\Table\CentersTable&\Cake\ORM\Association\BelongsTo $Centers
 *
 * @method \App\Model\Entity\CenterFile newEmptyEntity()
 * @method \App\Model\Entity\CenterFile newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\CenterFile[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CenterFile get($primaryKey, $options = [])
 * @method \App\Model\Entity\CenterFile findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\CenterFile patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CenterFile[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\CenterFile|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CenterFile saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CenterFile[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CenterFile[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\CenterFile[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CenterFile[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class CenterFilesTable extends Table
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

        $this->setTable('center_files');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Centers', [
            'foreignKey' => 'center_id',
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
            ->integer('width')
            ->notEmptyString('width');

        $validator
            ->integer('height')
            ->notEmptyString('height'); 

        $validator
            ->scalar('path')
            ->allowEmptyString('path');

        $validator
            ->scalar('file_name')
            ->maxLength('file_name', 100)
            ->allowEmptyFile('file_name');

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
        $rules->add($rules->existsIn(['center_id'], 'Centers'), ['errorField' => 'center_id']);

        return $rules;
    }
}

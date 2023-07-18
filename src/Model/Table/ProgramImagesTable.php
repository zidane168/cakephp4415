<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ProgramImages Model
 *
 * @property \App\Model\Table\ProgramsTable&\Cake\ORM\Association\BelongsTo $Programs
 *
 * @method \App\Model\Entity\ProgramImage newEmptyEntity()
 * @method \App\Model\Entity\ProgramImage newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\ProgramImage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ProgramImage get($primaryKey, $options = [])
 * @method \App\Model\Entity\ProgramImage findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\ProgramImage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ProgramImage[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ProgramImage|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ProgramImage saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ProgramImage[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ProgramImage[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\ProgramImage[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ProgramImage[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ProgramImagesTable extends Table
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

        $this->setTable('program_images');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
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
            ->scalar('path')
            ->maxLength('path', 191)
            ->requirePresence('path', 'create')
            ->notEmptyString('path');

        $validator
            ->integer('width')
            ->requirePresence('width', 'create')
            ->notEmptyString('width');

        $validator
            ->integer('height')
            ->requirePresence('height', 'create')
            ->notEmptyString('height');

        $validator
            ->integer('size')
            ->notEmptyString('size');

        $validator
            ->scalar('name')
            ->maxLength('name', 191)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

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
        $rules->add($rules->existsIn(['program_id'], 'Programs'), ['errorField' => 'program_id']);

        return $rules;
    }
}

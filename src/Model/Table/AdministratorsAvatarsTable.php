<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AdministratorsAvatars Model
 *
 * @property \App\Model\Table\AdministratorsTable&\Cake\ORM\Association\BelongsTo $Administrators
 *
 * @method \App\Model\Entity\AdministratorsAvatar newEmptyEntity()
 * @method \App\Model\Entity\AdministratorsAvatar newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\AdministratorsAvatar[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AdministratorsAvatar get($primaryKey, $options = [])
 * @method \App\Model\Entity\AdministratorsAvatar findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\AdministratorsAvatar patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AdministratorsAvatar[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\AdministratorsAvatar|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AdministratorsAvatar saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AdministratorsAvatar[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\AdministratorsAvatar[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\AdministratorsAvatar[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\AdministratorsAvatar[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class AdministratorsAvatarsTable extends Table
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

        $this->setTable('administrators_avatars');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Administrators', [
            'foreignKey' => 'administrator_id',
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
            ->maxLength('path', 512)
            ->allowEmptyString('path');

        $validator
            ->integer('size')
            ->allowEmptyString('size');

        $validator
            ->integer('width')
            ->allowEmptyString('width');

        $validator
            ->integer('height')
            ->allowEmptyString('height');

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
        $rules->add($rules->existsIn(['administrator_id'], 'Administrators'), ['errorField' => 'administrator_id']);

        return $rules;
    }
}

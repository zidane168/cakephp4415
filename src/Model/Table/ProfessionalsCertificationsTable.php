<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ProfessionalsCertifications Model
 *
 * @property \App\Model\Table\ProfessionalsTable&\Cake\ORM\Association\BelongsTo $Professionals
 *
 * @method \App\Model\Entity\ProfessionalsCertification newEmptyEntity()
 * @method \App\Model\Entity\ProfessionalsCertification newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\ProfessionalsCertification[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ProfessionalsCertification get($primaryKey, $options = [])
 * @method \App\Model\Entity\ProfessionalsCertification findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\ProfessionalsCertification patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ProfessionalsCertification[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ProfessionalsCertification|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ProfessionalsCertification saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ProfessionalsCertification[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ProfessionalsCertification[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\ProfessionalsCertification[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ProfessionalsCertification[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ProfessionalsCertificationsTable extends Table
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

        $this->setTable('professionals_certifications');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('ProfessionalCertificationLanguages', [
            'foreignKey' => 'professional_certification_id',
            'dependent'  => true
        ]);

        $this->belongsTo('Professionals', [
            'foreignKey' => 'professional_id',
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
        $rules->add($rules->existsIn(['professional_id'], 'Professionals'), ['errorField' => 'professional_id']);

        return $rules;
    }
}

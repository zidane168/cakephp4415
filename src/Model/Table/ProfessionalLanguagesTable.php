<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ProfessionalLanguages Model
 *
 * @property \App\Model\Table\ProfessionalsTable&\Cake\ORM\Association\BelongsTo $Professionals
 *
 * @method \App\Model\Entity\ProfessionalLanguage newEmptyEntity()
 * @method \App\Model\Entity\ProfessionalLanguage newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\ProfessionalLanguage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ProfessionalLanguage get($primaryKey, $options = [])
 * @method \App\Model\Entity\ProfessionalLanguage findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\ProfessionalLanguage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ProfessionalLanguage[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ProfessionalLanguage|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ProfessionalLanguage saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ProfessionalLanguage[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ProfessionalLanguage[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\ProfessionalLanguage[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ProfessionalLanguage[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ProfessionalLanguagesTable extends Table
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

        $this->setTable('professional_languages');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Professionals', [
            'foreignKey' => 'professional_id',
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
            ->scalar('nick_name')
            ->maxLength('nick_name', 100)
            ->allowEmptyString('nick_name');

        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->allowEmptyString('name');

        $validator
            ->scalar('title')
            ->maxLength('title', 100)
            ->allowEmptyString('title');

        $validator
            ->scalar('alias')
            ->maxLength('alias', 10)
            ->requirePresence('alias', 'create')
            ->notEmptyString('alias');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->scalar('alias')
            ->maxLength('alias', 10)
            ->allowEmptyString('alias');

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

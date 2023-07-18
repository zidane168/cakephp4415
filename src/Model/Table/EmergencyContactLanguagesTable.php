<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * EmergencyContactLanguages Model
 *
 * @property \App\Model\Table\EmergencyContactsTable&\Cake\ORM\Association\BelongsTo $EmergencyContacts
 *
 * @method \App\Model\Entity\EmergencyContactLanguage newEmptyEntity()
 * @method \App\Model\Entity\EmergencyContactLanguage newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\EmergencyContactLanguage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\EmergencyContactLanguage get($primaryKey, $options = [])
 * @method \App\Model\Entity\EmergencyContactLanguage findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\EmergencyContactLanguage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\EmergencyContactLanguage[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\EmergencyContactLanguage|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\EmergencyContactLanguage saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\EmergencyContactLanguage[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\EmergencyContactLanguage[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\EmergencyContactLanguage[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\EmergencyContactLanguage[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class EmergencyContactLanguagesTable extends Table
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

        $this->setTable('emergency_contact_languages');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('EmergencyContacts', [
            'foreignKey' => 'emergency_contact_id',
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
            ->scalar('alias')
            ->maxLength('alias', 10)
            ->requirePresence('alias', 'create')
            ->notEmptyString('alias');

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
        $rules->add($rules->existsIn(['emergency_contact_id'], 'EmergencyContacts'), ['errorField' => 'emergency_contact_id']);

        return $rules;
    }
}

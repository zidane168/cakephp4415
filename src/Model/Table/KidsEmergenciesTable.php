<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * KidsEmergencies Model
 *
 * @property \App\Model\Table\KidsTable&\Cake\ORM\Association\BelongsTo $Kids
 * @property \App\Model\Table\RelationshipsTable&\Cake\ORM\Association\BelongsTo $Relationships
 * @property \App\Model\Table\EmergencyContactsTable&\Cake\ORM\Association\BelongsTo $EmergencyContacts
 *
 * @method \App\Model\Entity\KidsEmergency newEmptyEntity()
 * @method \App\Model\Entity\KidsEmergency newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\KidsEmergency[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\KidsEmergency get($primaryKey, $options = [])
 * @method \App\Model\Entity\KidsEmergency findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\KidsEmergency patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\KidsEmergency[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\KidsEmergency|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KidsEmergency saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KidsEmergency[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\KidsEmergency[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\KidsEmergency[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\KidsEmergency[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class KidsEmergenciesTable extends Table
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

        $this->setTable('kids_emergencies');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Kids', [
            'foreignKey' => 'kid_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Relationships', [
            'foreignKey' => 'relationship_id',
            'joinType' => 'INNER',
        ]);
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
            ->boolean('enabled')
            ->notEmptyString('enabled');

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
        $rules->add($rules->existsIn(['relationship_id'], 'Relationships'), ['errorField' => 'relationship_id']);
        $rules->add($rules->existsIn(['emergency_contact_id'], 'EmergencyContacts'), ['errorField' => 'emergency_contact_id']);
        $rules->add($rules->isUnique(['kid_id', 'relationship_id', 'emergency_contact_id']), ['errorField' => 'kid_id']);
        return $rules;
    }
}

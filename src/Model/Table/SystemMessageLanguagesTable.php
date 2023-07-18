<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SystemMessageLanguages Model
 *
 * @property \App\Model\Table\SystemMessagesTable&\Cake\ORM\Association\BelongsTo $SystemMessages
 *
 * @method \App\Model\Entity\SystemMessageLanguage newEmptyEntity()
 * @method \App\Model\Entity\SystemMessageLanguage newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\SystemMessageLanguage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SystemMessageLanguage get($primaryKey, $options = [])
 * @method \App\Model\Entity\SystemMessageLanguage findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\SystemMessageLanguage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SystemMessageLanguage[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SystemMessageLanguage|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SystemMessageLanguage saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SystemMessageLanguage[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SystemMessageLanguage[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\SystemMessageLanguage[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SystemMessageLanguage[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class SystemMessageLanguagesTable extends Table
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

        $this->setTable('system_message_languages');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('SystemMessages', [
            'foreignKey' => 'system_message_id',
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
            ->allowEmptyString('alias');

        $validator
            ->scalar('title')
            ->maxLength('title', 191)
            ->allowEmptyString('title');

        $validator
            ->scalar('message')
            ->allowEmptyString('message');

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
        $rules->add($rules->existsIn(['system_message_id'], 'SystemMessages'), ['errorField' => 'system_message_id']);

        return $rules;
    }
}

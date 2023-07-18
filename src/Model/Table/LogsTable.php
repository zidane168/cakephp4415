<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Logs Model
 *
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 * @property \App\Model\Table\LogErrorsTable&\Cake\ORM\Association\HasMany $LogErrors
 * @property \App\Model\Table\LogSuccessesTable&\Cake\ORM\Association\HasMany $LogSuccesses
 *
 * @method \App\Model\Entity\Log newEmptyEntity()
 * @method \App\Model\Entity\Log newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Log[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Log get($primaryKey, $options = [])
 * @method \App\Model\Entity\Log findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Log patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Log[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Log|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Log saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Log[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Log[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Log[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Log[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LogsTable extends Table
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

        $this->setTable('logs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('LogErrors', [
            'foreignKey' => 'log_id',
        ]);
        $this->hasMany('LogSuccesses', [
            'foreignKey' => 'log_id',
        ]);
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
            ->scalar('plugin')
            ->maxLength('plugin', 50)
            ->requirePresence('plugin', 'create')
            ->notEmptyString('plugin');

        $validator
            ->scalar('controller')
            ->maxLength('controller', 100)
            ->requirePresence('controller', 'create')
            ->notEmptyString('controller');

        $validator
            ->scalar('action')
            ->maxLength('action', 100)
            ->requirePresence('action', 'create')
            ->notEmptyString('action');

        $validator
            ->scalar('new_data')
            ->allowEmptyString('new_data');

        $validator
            ->scalar('old_data')
            ->allowEmptyString('old_data');

        $validator
            ->boolean('archived')
            ->notEmptyString('archived');

        $validator
            ->scalar('remote_ip')
            ->maxLength('remote_ip', 191)
            ->allowEmptyString('remote_ip');

        $validator
            ->scalar('agent')
            ->maxLength('agent', 191)
            ->allowEmptyString('agent');

        $validator
            ->scalar('version')
            ->maxLength('version', 191)
            ->allowEmptyString('version');

        $validator
            ->scalar('platform')
            ->maxLength('platform', 191)
            ->allowEmptyString('platform');

        $validator
            ->scalar('browser')
            ->maxLength('browser', 191)
            ->allowEmptyString('browser');

        $validator
            ->boolean('enabled')
            ->notEmptyString('enabled');

        $validator
            ->integer('created_by')
            ->allowEmptyString('created_by');

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
        $rules->add($rules->existsIn(['company_id'], 'Companies'), ['errorField' => 'company_id']);

        return $rules;
    }
}

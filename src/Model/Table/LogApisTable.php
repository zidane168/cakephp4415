<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * LogApis Model
 *
 * @method \App\Model\Entity\LogApi newEmptyEntity()
 * @method \App\Model\Entity\LogApi newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\LogApi[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\LogApi get($primaryKey, $options = [])
 * @method \App\Model\Entity\LogApi findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\LogApi patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\LogApi[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\LogApi|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\LogApi saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\LogApi[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\LogApi[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\LogApi[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\LogApi[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LogApisTable extends Table
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

        $this->setTable('log_apis');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        // belongto;
        $this->addBehavior('WhoDidIt');
    
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
            ->scalar('url')
            ->maxLength('url', 1000)
            ->requirePresence('url', 'create')
            ->notEmptyString('url');

        $validator
            ->scalar('request')
            ->requirePresence('request', 'create')
            ->notEmptyString('request');

        $validator
            ->scalar('response')
            ->allowEmptyString('response');

        $validator
            ->scalar('old_data')
            ->allowEmptyString('old_data');

        $validator
            ->scalar('new_data')
            ->allowEmptyString('new_data');

        $validator
            ->boolean('status')
            ->allowEmptyString('status');

        $validator
            ->boolean('archived')
            ->notEmptyString('archived');

        $validator
            ->integer('created_by')
            ->allowEmptyString('created_by');

        return $validator;
    }

    public function writeLog($data) {

        // Convert entities
        $logApis = $this->newEmptyEntity();
        $logApis->url = $data['url'];
        $logApis->request  = json_encode($data['request']);
        $logApis->response = json_encode($data['response']);
        $logApis->old_data = isset($data['old_data']) && !empty($data['old_data']) ? json_encode($data['old_data']) : '';
        $logApis->new_data = isset($data['new_data']) && !empty($data['new_data']) ? json_encode($data['new_data']) : '';
        $logApis->status = $data['status'];

        if ($this->save($logApis)) {
            return true;
        }
        return false;
    }
}

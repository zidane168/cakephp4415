<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Utility\Hash;

/**
 * Languages Model
 *
 * @method \App\Model\Entity\Language newEmptyEntity()
 * @method \App\Model\Entity\Language newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Language[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Language get($primaryKey, $options = [])
 * @method \App\Model\Entity\Language findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Language patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Language[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Language|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Language saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Language[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Language[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Language[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Language[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LanguagesTable extends Table
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

        $this->setTable('languages');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
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
            ->maxLength('alias', 191)
            ->requirePresence('alias', 'create')
            ->notEmptyString('alias');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->boolean('is_default')
            ->notEmptyString('is_default');

        $validator
            ->boolean('enabled')
            ->notEmptyString('enabled');

        $validator
            ->integer('modified_by')
            ->allowEmptyString('modified_by');

        $validator
            ->integer('created_by')
            ->allowEmptyString('created_by');

        return $validator;
    }

    public function get_languages() {
		$languages = $this->find('all',array(
			'fields' => array(
				'Languages.id',
				'Languages.alias',
				'Languages.name',
				'Languages.is_default',
			),
			'conditions' => array('Languages.enabled' => true),
		));

        $languages = $languages->toArray();
    	return $languages;
	}

}

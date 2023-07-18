<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TermLanguages Model
 *
 * @property \App\Model\Table\TermsTable&\Cake\ORM\Association\BelongsTo $Terms
 *
 * @method \App\Model\Entity\TermLanguage newEmptyEntity()
 * @method \App\Model\Entity\TermLanguage newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\TermLanguage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TermLanguage get($primaryKey, $options = [])
 * @method \App\Model\Entity\TermLanguage findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\TermLanguage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TermLanguage[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\TermLanguage|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TermLanguage saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TermLanguage[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\TermLanguage[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\TermLanguage[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\TermLanguage[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class TermLanguagesTable extends Table
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

        $this->setTable('term_languages');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('Terms', [
            'foreignKey' => 'term_id',
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
            ->scalar('title')
            ->maxLength('title', 191)
            ->allowEmptyString('title');

        $validator
            ->scalar('content')
            ->allowEmptyString('content');

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
        $rules->add($rules->existsIn(['term_id'], 'Terms'), ['errorField' => 'term_id']);

        return $rules;
    }
}

<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * FeedbackLanguages Model
 *
 * @property \App\Model\Table\FeedbackTable&\Cake\ORM\Association\BelongsTo $Feedback
 *
 * @method \App\Model\Entity\FeedbackLanguage newEmptyEntity()
 * @method \App\Model\Entity\FeedbackLanguage newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\FeedbackLanguage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\FeedbackLanguage get($primaryKey, $options = [])
 * @method \App\Model\Entity\FeedbackLanguage findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\FeedbackLanguage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\FeedbackLanguage[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\FeedbackLanguage|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FeedbackLanguage saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FeedbackLanguage[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\FeedbackLanguage[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\FeedbackLanguage[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\FeedbackLanguage[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class FeedbackLanguagesTable extends Table
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

        $this->setTable('feedback_languages');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Feedbacks', [
            'foreignKey' => 'feedback_id',
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
            ->scalar('name')
            ->maxLength('name', 191)
            ->allowEmptyString('name');

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
        $rules->add($rules->existsIn(['feedback_id'], 'Feedbacks'), ['errorField' => 'feedback_id']);

        return $rules;
    }
}

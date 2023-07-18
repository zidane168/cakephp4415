<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ParentFeedbacks Model
 *
 * @property \App\Model\Table\CidcParentsTable&\Cake\ORM\Association\BelongsTo $CidcParents
 *
 * @method \App\Model\Entity\ParentFeedback newEmptyEntity()
 * @method \App\Model\Entity\ParentFeedback newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\ParentFeedback[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ParentFeedback get($primaryKey, $options = [])
 * @method \App\Model\Entity\ParentFeedback findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\ParentFeedback patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ParentFeedback[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ParentFeedback|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ParentFeedback saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ParentFeedback[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ParentFeedback[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\ParentFeedback[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ParentFeedback[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ParentFeedbacksTable extends Table
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

        $this->setTable('parent_feedbacks');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('CidcParents', [
            'foreignKey' => 'cidc_parent_id',
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
            ->scalar('feedback')
            ->allowEmptyString('feedback');

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
        $rules->add($rules->existsIn(['cidc_parent_id'], 'CidcParents'), ['errorField' => 'cidc_parent_id']);

        return $rules;
    }

    public function add($parent_id, $feedback) {

        $data = [
            'cidc_parent_id' => $parent_id,
            'feedback' => $feedback,
        ];

        $entity = $this->newEntity($data);
        if ($this->save($entity)) {
            return [
                'status' => 200,
                'message' => __('data_is_saved'),
            ];
        }

        return [
            'status' => 500,
            'message' => __('data_is_not_saved'). ' ' . json_encode($entity->getErrors()),
        ];
    }
}

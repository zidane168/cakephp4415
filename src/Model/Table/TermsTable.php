<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Terms Model
 *
 * @property \App\Model\Table\TermLanguagesTable&\Cake\ORM\Association\HasMany $TermLanguages
 *
 * @method \App\Model\Entity\Term newEmptyEntity()
 * @method \App\Model\Entity\Term newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Term[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Term get($primaryKey, $options = [])
 * @method \App\Model\Entity\Term findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Term patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Term[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Term|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Term saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Term[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Term[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Term[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Term[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TermsTable extends Table
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

        $this->setTable('terms');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('TermLanguages', [
            'foreignKey' => 'term_id',
            'dependent'  => true
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
            ->integer('created_by')
            ->allowEmptyString('created_by');

        $validator
            ->integer('modified_by')
            ->allowEmptyString('modified_by');

        return $validator;
    }

    public function get_detail($languages)
    {
        $conditions = [
            'Terms.enabled' => true,
        ];
        $result = $this->find('all', [
            'fields' => [
                'id'        => 'Terms.id',
                'title'     => 'TermLanguages.title',
                'content'   => 'TermLanguages.content',
            ],
            'conditions' => $conditions,
            'join' => [
                'table' => 'term_languages',
                'alias' => 'TermLanguages',
                'type' => 'LEFT',
                'conditions' => [
                    'TermLanguages.term_id = Terms.id',
                    'TermLanguages.alias' => $languages,
                ],
            ]
        ])->first();
        return $result;
    }
}

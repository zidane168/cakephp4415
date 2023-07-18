<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Abouts Model
 *
 * @property \App\Model\Table\AboutLanguagesTable&\Cake\ORM\Association\HasMany $AboutLanguages
 *
 * @method \App\Model\Entity\About newEmptyEntity()
 * @method \App\Model\Entity\About newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\About[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\About get($primaryKey, $options = [])
 * @method \App\Model\Entity\About findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\About patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\About[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\About|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\About saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\About[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\About[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\About[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\About[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AboutsTable extends Table
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

        $this->setTable('abouts');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('AboutLanguages', [
            'foreignKey' => 'about_id',
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
            'Abouts.enabled' => true,
        ];
        $result = $this->find('all', [
            'fields' => [
                'id'        => 'Abouts.id',
                'content'     => 'AboutLanguages.content',
            ],
            'conditions' => $conditions,
            'join' => [
                'table' => 'about_languages',
                'alias' => 'AboutLanguages',
                'type' => 'LEFT',
                'conditions' => [
                    'AboutLanguages.about_id = Abouts.id',
                    'AboutLanguages.alias' => $languages,
                ],
            ]
        ])->first();
        return $result;
    }
}

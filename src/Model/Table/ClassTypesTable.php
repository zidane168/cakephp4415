<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ClassTypes Model
 *
 * @property \App\Model\Table\CidcClassesTable&\Cake\ORM\Association\HasMany $CidcClasses
 * @property \App\Model\Table\ClassTypeLanguagesTable&\Cake\ORM\Association\HasMany $ClassTypeLanguages
 *
 * @method \App\Model\Entity\ClassType newEmptyEntity()
 * @method \App\Model\Entity\ClassType newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\ClassType[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ClassType get($primaryKey, $options = [])
 * @method \App\Model\Entity\ClassType findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\ClassType patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ClassType[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ClassType|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ClassType saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ClassType[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ClassType[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\ClassType[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ClassType[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ClassTypesTable extends Table
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

        $this->setTable('class_types');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('CidcClasses', [
            'foreignKey' => 'class_type_id',
        ]);
        $this->hasMany('ClassTypeLanguages', [
            'foreignKey' => 'class_type_id',
            'dependent'     => true
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

        $validator
            ->integer('created_by')
            ->allowEmptyString('created_by');

        $validator
            ->integer('modified_by')
            ->allowEmptyString('modified_by');

        return $validator;
    }

    public function get_list($language, $conditions = array()) // add product admin page
    {
        $classTypes = $this->find(
            'list',
            [
                'keyField' => 'id',
                'valueField' => function ($row) {
                    return $row->class_type_languages[0]->name;
                }
            ]
        )
            ->where([$conditions])
            ->contain(
                [
                    'ClassTypeLanguages' => [
                        'conditions' => ['ClassTypeLanguages.alias' => $language]
                    ]
                ]
            );
        return $classTypes;
    }

    public function get_list_api($language)
    {
        return $this->find('all', [
            'fields' => [
                'id'     => 'ClassTypes.id',
                'name'   => 'ClassTypeLanguages.name',
            ],
            'conditions' => [
                'ClassTypes.enabled' => true
            ],
            'join' => [
                'table'     => 'class_type_languages',
                'alias'     => 'ClassTypeLanguages',
                'type'      => 'INNER',
                'conditions' => [
                    'ClassTypeLanguages.alias' => $language,
                    'ClassTypeLanguages.class_type_id = ClassTypes.id'
                ]
            ]
        ]);
    }
}

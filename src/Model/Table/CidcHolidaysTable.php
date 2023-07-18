<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CidcHolidays Model
 *
 * @method \App\Model\Entity\CidcHoliday newEmptyEntity()
 * @method \App\Model\Entity\CidcHoliday newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\CidcHoliday[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CidcHoliday get($primaryKey, $options = [])
 * @method \App\Model\Entity\CidcHoliday findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\CidcHoliday patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CidcHoliday[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\CidcHoliday|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CidcHoliday saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CidcHoliday[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CidcHoliday[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\CidcHoliday[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CidcHoliday[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CidcHolidaysTable extends Table
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

        $this->setTable('cidc_holidays');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

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
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->boolean('enabled')
            ->notEmptyString('enabled');

        return $validator;
    }

    public function get_all_list()
    {
        $temp = $this->find('all', [
            'conditions' => [
                'CidcHolidays.enabled' => true,
            ],
            'fields' => [
                'CidcHolidays.id',
                'CidcHolidays.date',
                'CidcHolidays.description',
            ],
        ])->toArray();

        $result = [];
        foreach ($temp as $value) {
            $result[] = [
                'id' => $value->id,
                'date' => $value->date->format('Y-m-d'),
                'description' => $value->description,
            ];
        }
        return $result;
    }
}

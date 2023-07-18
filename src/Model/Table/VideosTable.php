<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use App\MyHelper\MyHelper;

/**
 * Videos Model
 *
 * @property \App\Model\Table\CidcClassesTable&\Cake\ORM\Association\BelongsTo $CidcClasses
 *
 * @method \App\Model\Entity\Video newEmptyEntity()
 * @method \App\Model\Entity\Video newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Video[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Video get($primaryKey, $options = [])
 * @method \App\Model\Entity\Video findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Video patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Video[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Video|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Video saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Video[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Video[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Video[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Video[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class VideosTable extends Table
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

        $this->setTable('videos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('CidcClasses', [
            'foreignKey' => 'cidc_class_id',
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
            ->scalar('ext')
            ->maxLength('ext', 10)
            ->allowEmptyString('ext');

        $validator
            ->integer('size')
            ->notEmptyString('size');

        $validator
            ->scalar('path')
            ->allowEmptyString('path');

        $validator
            ->scalar('file_name')
            ->maxLength('file_name', 100)
            ->allowEmptyFile('file_name');

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
        $rules->add($rules->existsIn(['cidc_class_id'], 'CidcClasses'), ['errorField' => 'cidc_class_id']);

        return $rules;
    }

    public function get_videos($data)
    {
        $conditions = [
            'Videos.cidc_class_id' => $data['cidc_class_id'],
        ];
        $list = $this->find('all', [
            'conditions' => $conditions,
            'page' => (int)$data['page'],
            'limit' => $data['limit']
        ])->toArray();
        if (!$list) {
            return [
                'items' => [],
                'count' => 0
            ];
        }
        $url = MyHelper::getUrl();
        $result = [];
        foreach ($list as $item) {
            $result[] = $url . $item->path;
        }
        $count = $this->find('all', [
            'conditions' => $conditions
        ])->count();
        return [
            'items' => $result,
            'count' => $count
        ];
    }
}

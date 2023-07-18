<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * NewsLanguages Model
 *
 * @property \App\Model\Table\NewsTable&\Cake\ORM\Association\BelongsTo $News
 *
 * @method \App\Model\Entity\NewsLanguage newEmptyEntity()
 * @method \App\Model\Entity\NewsLanguage newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\NewsLanguage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\NewsLanguage get($primaryKey, $options = [])
 * @method \App\Model\Entity\NewsLanguage findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\NewsLanguage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\NewsLanguage[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\NewsLanguage|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\NewsLanguage saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\NewsLanguage[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\NewsLanguage[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\NewsLanguage[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\NewsLanguage[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class NewsLanguagesTable extends Table
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

        $this->setTable('news_languages');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('News', [
            'foreignKey' => 'news_id',
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
            ->requirePresence('alias', 'create')
            ->notEmptyString('alias');

        $validator
            ->scalar('title')
            ->maxLength('title', 191)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('content')
            ->requirePresence('content', 'create')
            ->notEmptyString('content');

        $validator
            ->scalar('author')
            ->maxLength('author', 191)
            ->requirePresence('author', 'create')
            ->notEmptyString('author');

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
        $rules->add($rules->existsIn(['news_id'], 'News'), ['errorField' => 'news_id']);

        return $rules;
    }
}

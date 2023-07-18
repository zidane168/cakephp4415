<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AboutLanguages Model
 *
 * @property \App\Model\Table\AboutsTable&\Cake\ORM\Association\BelongsTo $Abouts
 *
 * @method \App\Model\Entity\AboutLanguage newEmptyEntity()
 * @method \App\Model\Entity\AboutLanguage newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\AboutLanguage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AboutLanguage get($primaryKey, $options = [])
 * @method \App\Model\Entity\AboutLanguage findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\AboutLanguage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AboutLanguage[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\AboutLanguage|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AboutLanguage saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AboutLanguage[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\AboutLanguage[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\AboutLanguage[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\AboutLanguage[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class AboutLanguagesTable extends Table
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

        $this->setTable('about_languages');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Abouts', [
            'foreignKey' => 'about_id',
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
        $rules->add($rules->existsIn(['about_id'], 'Abouts'), ['errorField' => 'about_id']);

        return $rules;
    }
}

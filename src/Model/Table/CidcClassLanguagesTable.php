<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CidcClassLanguages Model
 *
 * @property \App\Model\Table\CidcClassesTable&\Cake\ORM\Association\BelongsTo $CidcClasses
 *
 * @method \App\Model\Entity\CidcClassLanguage newEmptyEntity()
 * @method \App\Model\Entity\CidcClassLanguage newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\CidcClassLanguage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CidcClassLanguage get($primaryKey, $options = [])
 * @method \App\Model\Entity\CidcClassLanguage findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\CidcClassLanguage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CidcClassLanguage[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\CidcClassLanguage|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CidcClassLanguage saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CidcClassLanguage[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CidcClassLanguage[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\CidcClassLanguage[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CidcClassLanguage[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class CidcClassLanguagesTable extends Table
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

        $this->setTable('cidc_class_languages');
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
            ->scalar('alias')
            ->maxLength('alias', 10)
            ->requirePresence('alias', 'create')
            ->notEmptyString('alias');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

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
}

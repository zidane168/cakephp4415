<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ImageTypeLanguages Model
 *
 * @property \App\Model\Table\ImageTypesTable&\Cake\ORM\Association\BelongsTo $ImageTypes
 *
 * @method \App\Model\Entity\ImageTypeLanguage newEmptyEntity()
 * @method \App\Model\Entity\ImageTypeLanguage newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\ImageTypeLanguage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ImageTypeLanguage get($primaryKey, $options = [])
 * @method \App\Model\Entity\ImageTypeLanguage findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\ImageTypeLanguage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ImageTypeLanguage[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ImageTypeLanguage|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ImageTypeLanguage saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ImageTypeLanguage[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ImageTypeLanguage[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\ImageTypeLanguage[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ImageTypeLanguage[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ImageTypeLanguagesTable extends Table
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

        $this->setTable('image_type_languages');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('ImageTypes', [
            'foreignKey' => 'image_type_id',
        ]);



        // belongto;
        $this->addBehavior('WhoDidIt');
    
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
            ->maxLength('alias', 191)
            ->allowEmptyString('alias');

        $validator
            ->scalar('name')
            ->maxLength('name', 191)
            ->allowEmptyString('name');

        $validator
            ->scalar('description')
            ->maxLength('description', 191)
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
        $rules->add($rules->existsIn(['image_type_id'], 'ImageTypes'), ['errorField' => 'image_type_id']);

        return $rules;
    }
}

<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DistrictLanguages Model
 *
 * @property \App\Model\Table\DistrictsTable&\Cake\ORM\Association\BelongsTo $Districts
 *
 * @method \App\Model\Entity\DistrictLanguage newEmptyEntity()
 * @method \App\Model\Entity\DistrictLanguage newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\DistrictLanguage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DistrictLanguage get($primaryKey, $options = [])
 * @method \App\Model\Entity\DistrictLanguage findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\DistrictLanguage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DistrictLanguage[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\DistrictLanguage|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DistrictLanguage saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DistrictLanguage[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\DistrictLanguage[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\DistrictLanguage[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\DistrictLanguage[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class DistrictLanguagesTable extends Table
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

        $this->setTable('district_languages');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Districts', [
            'foreignKey' => 'district_id',
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
        $rules->add($rules->existsIn(['district_id'], 'Districts'), ['errorField' => 'district_id']);

        return $rules;
    }

    public function get_list($language) {
       
        return $this->find('list', 
            [
                'keyField' => 'district_id',
                'valueField' => 'name'      // cannot use CompanyLanguages.name x
            ],
            ['limit' => 200]
        )->where([
            'alias' => $language,
        ]);
    }
}

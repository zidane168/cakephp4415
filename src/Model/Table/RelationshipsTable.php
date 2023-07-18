<?php

declare(strict_types=1);

namespace App\Model\Table;

use Authentication\Authenticator\Result;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Relationships Model
 *
 * @property \App\Model\Table\KidsTable&\Cake\ORM\Association\HasMany $Kids
 * @property \App\Model\Table\KidsEmergenciesTable&\Cake\ORM\Association\HasMany $KidsEmergencies
 * @property \App\Model\Table\RelationshipLanguagesTable&\Cake\ORM\Association\HasMany $RelationshipLanguages
 *
 * @method \App\Model\Entity\Relationship newEmptyEntity()
 * @method \App\Model\Entity\Relationship newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Relationship[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Relationship get($primaryKey, $options = [])
 * @method \App\Model\Entity\Relationship findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Relationship patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Relationship[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Relationship|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Relationship saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Relationship[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Relationship[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Relationship[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Relationship[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RelationshipsTable extends Table
{
    public function joinLanguage($language)
    {
        return [
            'table' => 'relationship_languages',
            'alias' => 'RelationshipLanguages',
            'type' => 'INNER',
            'conditions' => [
                'RelationshipLanguages.relationship_id = Relationships.id',
                'RelationshipLanguages.alias' => $language,
            ],
        ];
    }
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('relationships');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Kids', [
            'foreignKey' => 'relationship_id',
        ]);
        $this->hasMany('KidsEmergencies', [
            'foreignKey' => 'relationship_id',
        ]);
        $this->hasMany('RelationshipLanguages', [
            'foreignKey' => 'relationship_id',
            'dependent' => true,
        ]);

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
        $relationships = $this->find(
            'list',
            [
                'keyField' => 'id',
                'valueField' => function ($row) {
                    return $row->relationship_languages[0]->name;
                }
            ]
        )
            ->where([$conditions])
            ->contain(
                [
                    'RelationshipLanguages' => [
                        'conditions' => ['RelationshipLanguages.alias' => $language]
                    ]
                ]
            );

        return $relationships;
    }

    // no data it mean not yet input data for this language
    public function get_list_pagination($language, $payload)
    {

        $conditions = [
            'Relationships.enabled' => true,
        ];
        $total = $this->find('all', [
            'conditions' => [
                'Relationships.enabled' => true
            ],
        ])->count();
        $result = [];
        if (!$total) {
            goto set_result;
        }

        if (isset($payload['search']) && !empty($payload['search'])) {
            $conditions['LOWER(RelationshipLanguages.name) LIKE'] = '%' . strtolower($payload['search']) . '%';
        }

        $result = $this->find('all', [
            'fields' => [
                'id'    => 'Relationships.id',
                'name'  => 'RelationshipLanguages.name',
            ],
            'conditions' => $conditions,
            'join' => $this->joinLanguage($language),
            'limit' => $payload['limit'],
            'page'  => (int)$payload['page'],

        ]);

        set_result:
        return [
            'count' => $total,
            'items' => $result,
        ];
    }
}

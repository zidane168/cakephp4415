<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Feedbacks Model
 *
 * @property \App\Model\Table\FeedbackLanguagesTable&\Cake\ORM\Association\HasMany $FeedbackLanguages
 *
 * @method \App\Model\Entity\Feedback newEmptyEntity()
 * @method \App\Model\Entity\Feedback newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Feedback[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Feedback get($primaryKey, $options = [])
 * @method \App\Model\Entity\Feedback findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Feedback patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Feedback[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Feedback|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Feedback saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Feedback[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Feedback[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Feedback[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Feedback[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FeedbacksTable extends Table
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

        $this->setTable('feedbacks');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('FeedbackLanguages', [
            'foreignKey' => 'feedback_id',
            'dependent'  =>  true
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

        return $validator;
    }

    public function get_list($language, $conditions = [])
    {
        $result = [];
        $feedbacks = $this->find('all', [
            'fields' => ['Feedbacks.id'],
            'conditions' => array_merge($conditions, [
                'Feedbacks.enabled' => true
            ]),
            'contain'   => [
                'FeedbackLanguages' => [
                    'fields' => [
                        'FeedbackLanguages.feedback_id',
                        'FeedbackLanguages.name'
                    ],
                    'conditions' => [
                        'FeedbackLanguages.alias' => $language
                    ]
                ]
            ]
        ])->toArray();

        foreach ($feedbacks as $feedback) {
            $result[] = [
                'id'    => $feedback->id,
                'name'  => $feedback->feedback_languages ? $feedback->feedback_languages[0]->name : __('pls_input_data_for_this_language'),
            ];
        }
        return $result;
    }

    public function get_list_pagination($language, $payload)
    {

        $conditions = [
            'Feedbacks.enabled' => true,
        ];

        $feedbacks = $this->find('all', [
            'conditions' => [
                'Feedbacks.enabled' => true,
            ]
        ])->toArray();
        if (!$feedbacks) {
            return [
                'count' => 0,
                'items' => [],
            ];
        }

        if (isset($payload['search']) && !empty($payload['search'])) {
            $conditions['LOWER(FeedbackLanguages.name) LIKE'] = '%' . strtolower($payload['search']) . '%';
        }

        $join = [
            'table' => 'feedback_languages',
            'alias' => 'FeedbackLanguages',
            'type' => 'INNER',
            'conditions' => [
                'FeedbackLanguages.feedback_id = Feedbacks.id',
                'FeedbackLanguages.alias' => $language,
            ],
        ];

        $result = $this->find('all', [
            'fields' => [
                'Feedbacks.id',
                'FeedbackLanguages.name',
            ],
            'conditions' => $conditions,
            'join' => $join,
            'limit' => $payload['limit'],
            'page'  => (int)$payload['page'],

        ]);

        $total = $this->find('all', [
            'conditions' => [
                'Feedbacks.enabled' => true
            ],
        ])->count();

        $items = [];
        foreach ($result as $val) {

            $items[] = [
                'id'                    => $val->id,
                'name'                  => $val['FeedbackLanguages']['name'],
            ];
        }

        return [
            'count' => $total,
            'items' => $items,
        ];
    }
}

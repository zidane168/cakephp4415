<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;

/**
 * SystemMessages Model
 *
 * @property \App\Model\Table\CidcClassesTable&\Cake\ORM\Association\BelongsTo $CidcClasses
 * @property \App\Model\Table\SystemMessagesTable&\Cake\ORM\Association\BelongsTo $ParentSystemMessages
 * @property \App\Model\Table\KidsTable&\Cake\ORM\Association\BelongsTo $Kids
 * @property \App\Model\Table\SystemMessageLanguagesTable&\Cake\ORM\Association\HasMany $SystemMessageLanguages
 * @property \App\Model\Table\SystemMessagesTable&\Cake\ORM\Association\HasMany $ChildSystemMessages
 *
 * @method \App\Model\Entity\SystemMessage newEmptyEntity()
 * @method \App\Model\Entity\SystemMessage newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\SystemMessage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SystemMessage get($primaryKey, $options = [])
 * @method \App\Model\Entity\SystemMessage findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\SystemMessage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SystemMessage[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SystemMessage|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SystemMessage saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SystemMessage[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SystemMessage[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\SystemMessage[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SystemMessage[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SystemMessagesTable extends Table
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

        $this->setTable('system_messages');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('CidcClasses', [
            'foreignKey' => 'cidc_class_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CidcParents', [
            'foreignKey' => 'cidc_parent_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Kids', [
            'foreignKey' => 'kid_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('SystemMessageLanguages', [
            'foreignKey' => 'system_message_id',
            'dependent' => true,
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
            ->integer('created_by')
            ->allowEmptyString('created_by');

        $validator
            ->integer('modified_by')
            ->allowEmptyString('modified_by');

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
        $rules->add($rules->existsIn(['cidc_parent_id'], 'CidcParents'), ['errorField' => 'cidc_parent_id']);
        $rules->add($rules->existsIn(['kid_id'], 'Kids'), ['errorField' => 'kid_id']);

        return $rules;
    }

    /* 
    ** $arr_messages: is a array with {title,  message} with [0]zh_HK, [1]zh_CN, [2]en_US
    */
    public function create($cidc_class_id, $cidc_parent_id, $kid_id, $arr_messages)
    {
        $db = $this->getConnection();
        $db->begin();

        $data_systemMessage = [
            'cidc_class_id' => $cidc_class_id,
            'cidc_parent_id' => $cidc_parent_id,
            'kid_id' => $kid_id,
        ];

        $status = 200;
        $message = __('data_is_saved');
        $entity_systemMessage = $this->newEntity($data_systemMessage);

        if ($model = $this->save($entity_systemMessage)) {

            // save language
            $data_systemMessageLanguage = [];
            $languages = TableRegistry::get('Languages')->get_languages();

            foreach ($languages as $lang) {
                if ($lang['alias'] == 'zh_HK') {
                    $data_systemMessageLanguage[] = [
                        'title'     => $arr_messages[0]['title'],
                        'message'   => $arr_messages[0]['message'],
                        'alias'     => $lang['alias'],
                        'system_message_id' => $model->id,
                    ];
                } elseif ($lang['alias'] == 'zh_CN') {
                    $data_systemMessageLanguage[] = [
                        'title'     => $arr_messages[1]['title'],
                        'message'   => $arr_messages[1]['message'],
                        'alias'     => $lang['alias'],
                        'system_message_id' => $model->id,
                    ];
                } elseif ($lang['alias'] == 'en_US') {
                    $data_systemMessageLanguage[] = [
                        'title'     => $arr_messages[2]['title'],
                        'message'   => $arr_messages[2]['message'],
                        'alias'     => $lang['alias'],
                        'system_message_id' => $model->id,
                    ];
                }
            }

            $entities_systemMessageLanguages = $this->SystemMessageLanguages->newEntities($data_systemMessageLanguage);
            if ($this->SystemMessageLanguages->saveMany($entities_systemMessageLanguages)) {
                $db->commit();
                $status = 200;
                $message = __('data_is_saved');
            } else {
                $db->rollback();
                $status = 500;
                $message = __('data_is_not_saved');
            }
        } else {
            $db->rollback();
            $status = 500;
            $message = __('data_is_not_saved');
        }

        return [
            'status' => $status,
            'message' => $message,
        ];
    }

    //  {title, message} with [0]zh_HK, [1]zh_CN, [2]en_US
    public function create_register_successfully_messages($class_info, $kid_info)
    {
        return [
            [
                'title' => '感謝您的註冊课程給小朋友:' .  $kid_info,
                'message' => '您已成功註冊課程: ' . $class_info,
            ],
            [
                'title' => '感谢您的注册课程給小朋友:' .  $kid_info,
                'message' => '您已成功注册课程：' . $class_info,
            ],
            [
                'title' => 'Thanks for your registered class for: ' . $kid_info,
                'message' => 'You have successfully registered class: ' . $class_info,
            ],
        ];
    }

    //  {title, message} with [0]zh_HK, [1]zh_CN, [2]en_US
    public function create_payment_successfully_messages($class_info, $kid_info)
    {
        return [
            [
                'title' => '感謝您的付款給小朋友:' .  $kid_info,
                'message' => '您已成功付款課程: ' . $class_info,
            ],
            [
                'title' => '感谢您的付款給小朋友:' .  $kid_info,
                'message' => '您已成功付款课程：' . $class_info,
            ],
            [
                'title' => 'Thanks for your payment for: ' . $kid_info,
                'message' => 'We had received your payment: ' . $class_info,
            ],
        ];
    }

    public function get_list_pagination($language, $cidc_parent_id, $limit, $page)
    {
        $temp = $this->find('all', [
            'conditions' => [
                'SystemMessages.enabled' => true,
                'SystemMessages.cidc_parent_id' => $cidc_parent_id,
            ],
            'fields' => [
                'SystemMessages.id',
                'SystemMessages.read_time',
            ],
            'contain' => [
                'SystemMessageLanguages' => [
                    'fields' => [
                        'SystemMessageLanguages.system_message_id',
                        'SystemMessageLanguages.title',
                        'SystemMessageLanguages.message',
                    ],
                    'conditions' => [
                        'SystemMessageLanguages.alias' => $language,
                    ],
                ],
            ],
            'order' => ['SystemMessages.id DESC'],
            'limit' => $limit,
            'page' => intVal($page),
        ]);
        $result = [];
        $count = $temp->count();

        if ($count == 0) {
            return [
                'count' => $count,
                'items' => $result,
            ];
        }
        $result =  $temp->toArray();
        // format data 

        $items = [];
        foreach ($result as $data) {
            $items[] = [
                'id'        => $data->id,
                'title'     => $data->system_message_languages[0]->title,
                'message'   => $data->system_message_languages[0]->message,
                'notification' => [
                    'is_read'   => $data->read_time ? 1 : 0,    // 1: read 
                    'read_time' => $data->read_time ? $data->read_time->format('Y-m-d H:i:s') : null,
                ],
            ];
        }

        return [
            'count' => $count,
            'items' => $items,
        ];
    }

    public function getDetail($id, $language)
    {
        $this->updateAll(
            ['read_time' => date('Y-m-d H:i:s')],
            ['id' => $id]
        );

        $temp = $this->find('all', [
            'conditions' => [
                'SystemMessages.enabled' => true,
                'SystemMessages.id' => $id,
            ],
            'fields' => [
                'SystemMessages.id',
                'SystemMessages.read_time',
            ],
            'contain' => [
                'SystemMessageLanguages' => [
                    'fields' => [
                        'SystemMessageLanguages.system_message_id',
                        'SystemMessageLanguages.title',
                        'SystemMessageLanguages.message',
                    ],
                    'conditions' => [
                        'SystemMessageLanguages.alias' => $language,
                    ],
                ],
            ],
        ])->first();

        if (!$temp) {
            return null;
        }

        return [
            'id'        => $temp->id,
            'title'     => $temp->system_message_languages[0]->title,
            'message'   => $temp->system_message_languages[0]->message,
            'notification' => [
                'is_read'   => $temp->read_time ? 1 : 0,    // 1: read 
                'read_time' => $temp->read_time ? $temp->read_time->format('Y-m-d H:i:s') : null,
            ],
        ];
    }

    public function read_all($ids, $cidc_parent_id)
    {
        $this->updateAll([
            'read_time' => date('Y-m-d H:i:s')
        ], [
            'id IN' => $ids,
            'cidc_parent_id' => $cidc_parent_id
        ]);
    }

    public function remove($id, $cidc_parent_id)
    {
        $_ids = $this->find('all', [
            'conditions' => [
                'SystemMessages.cidc_parent_id' => $cidc_parent_id,
                'SystemMessages.id IN' => $id
            ]
        ])->count();
        if ($_ids != count($id)) {
            return false;
        }
        $this->SystemMessageLanguages->deleteAll([
            'SystemMessageLanguages.system_message_id IN' => $id
        ]);
        $this->deleteAll([
            'SystemMessages.id IN' => $id
        ]);
        return true;
    }

    public function number_unread($cidc_parent_id)
    {
        $count = $this->find('all', [
            'conditions' => [
                'SystemMessages.cidc_parent_id' => $cidc_parent_id,
                'SystemMessages.read_time IS NULL'
            ]
        ])->count();
        return $count;
    }

    // 0: no send message
    // 1: sent message
    // 2: no send message
    public function send_system_message($cart_json, $user_id, $language) {     // $this->user->id

        $obj_Parents        = TableRegistry::get('CidcParents');
        $cidc_parent_id     = $obj_Parents->get_id_by_user($user_id);
        $flag               = true;

        if (isset($cart_json) && !empty($cart_json)) {
            $carts = json_decode($cart_json, true);
 
            $obj_CidcClasses    = TableRegistry::get('CidcClasses'); 
            $obj_Kids           = TableRegistry::get('Kids');
            
            foreach ($carts as $cart) { 
                $cidcClass  = $obj_CidcClasses->get($cart['cidc_class_id']);
                $class_info = $cidcClass->name . '-' . $cidcClass->code;
        
                $kid_infos  = $obj_Kids->get_kid_info($cart['kid_id'], $language);
        
                if ($class_info && !empty($class_info) && $kid_infos && !empty($kid_infos)) {
                 
                    $kid_info       = $obj_Kids->format_kid_info($kid_infos);
                    $arr_messages   = $this->create_register_successfully_messages($class_info, $kid_info);
        
                    $result_SystemMessage =  $this->create($cart['cidc_class_id'], $cidc_parent_id, $cart['kid_id'], $arr_messages);

                    if ($result_SystemMessage['status'] != 200) {
                        $flag = false;
                    }

                }  
            }
        }   
        return $flag ? true : false;
    }

}

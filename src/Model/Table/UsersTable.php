<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Authentication\PasswordHasher\DefaultPasswordHasher;

use Cake\Core\Configure;
use Cake\Utility\Security;
use Firebase\JWT\JWT;
use Cake\ORM\TableRegistry;

use App\MyHelper\MyHelper;

/**
 * Users Model
 *
 * @property \App\Model\Table\UserRolesTable&\Cake\ORM\Association\BelongsTo $UserRoles
 * @property \App\Model\Table\StaffsTable&\Cake\ORM\Association\HasMany $Staffs
 *
 * @method \App\Model\Entity\User newEmptyEntity()
 * @method \App\Model\Entity\User newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\User|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    // private $Helper;
    // function __construct()
    // {
    //     $this->Helper = new MyHelper;
    // }
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('UserRoles', [
            'foreignKey' => 'user_role_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Staffs', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('CidcParents', [
            'foreignKey' => 'user_id',
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
            ->scalar('phone_number')
            ->maxLength('phone_number', 10)
            ->requirePresence('phone_number', 'create')
            ->notEmptyString('phone_number'); 

        $validator
            ->scalar('password')
            ->maxLength('password', 100)
            ->requirePresence('password', 'create')
            ->notEmptyString('password');

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
        // $rules->add($rules->isUnique(['email']), ['errorField' => 'Email']);
        $rules->add($rules->IsUnique(['phone_number']), ['errorField' => 'Phone']); 
        $rules->add($rules->existsIn(['user_role_id'], 'UserRoles'), ['errorField' => 'user_role_id']);

        return $rules;
    }

    public function check_phone_email($phone_number, $email)
    {
        $phoneCondition = "SUM(CASE WHEN Users.phone_number = '$phone_number' THEN 1 ELSE 0 END)";
        $fields['phone_number']  = $phoneCondition; 

        if (isset($email) && !empty($email)) {
            $emailCondition = "SUM(CASE WHEN Users.email = '$email' THEN 1 ELSE 0 END)";
            $fields['email'] = $emailCondition;
        } 

        $data = $this->find('all', [
            'fields' => $fields,
        ])->toArray();
        $status = 200;
        $message = "OK";
        if ($data[0]['phone_number']) {
            $status = 304;
            $message = __('phone_number_is_exist');
            goto set_result;
        }

        if (isset($email) && !empty($email)) { 
            if ($data[0]['email']) {
                $status = 305;
                $message = __('email_is_exist');
                goto set_result;
            }
        } 

        set_result:
        return [
            'status'   => $status,
            'message'   => $message
        ];
    }

    public function add($user_role_id, $phone_number, $password, $email = null)
    {
        $status = 200;
        $message = __('data_is_saved');
        $params = []; 

        $check_phone_email = $this->check_phone_email($phone_number, $email);
        if ($check_phone_email['status'] != 200) {
            $status = $check_phone_email['status'];
            $message = $check_phone_email['message'];
            goto set_result;
        }

        // check exist email / phone_number  
        // save data 
        $hash_password = $this->create_member_password($password);
        $data = [
            'user_role_id'  => $user_role_id,
            'phone_number'  => $phone_number,
            'password'      => $hash_password,
            'email'         => $email,
        ];

        $entity = $this->newEntity($data);

        if ($model = $this->save($entity)) {
            $params = $model;
        } else {
            $status = 500;

            if (!empty($entity->getErrors())) {
                $error = json_decode(json_encode($entity->getErrors()), true);
                if (isset($error['Email']['_isUnique']) && !empty($error['Email']['_isUnique'])) {
                    $message = __('email_is_exists');
                } elseif (isset($error['Phone']['_isUnique']) && !empty($error['Phone']['_isUnique'])) {
                    $message = __('phone_is_exists');
                } else {
                    $message = json_encode($entity->getErrors());
                }
            } else {
                $message = __('data_is_not_saved');
            }
        }

        set_result:
        return [
            'status'    => $status,
            'message'   => $message,
            'params'    => $params,
        ];
    }

    public function change_password($id = null, $oldPass, $newPass)
    {
        $status = 999;
        $message = 'CHANGE_PASSWORD_FAIL';
        $params = [];
        $user = $this->find('all', [
            'conditions'    => ['Users.id' => $id]
        ])->first();
        if (!$user) {
            goto set_result;
        }

        if (!$this->verify_member_password($oldPass, $user->password)) {
            $message = "WRONG_PASSWORD";
            goto set_result;
        }

        $user->password = $this->create_member_password($newPass);
        if (!$this->save($user)) {
            goto set_result;
        }
        $status = 200;
        $message = __('change_password_successfully');

        set_result:
        return [
            'status'    => $status,
            'message'   => $message,
            'params'    => $params,
        ];
    }

    public function reset_password($data)
    {
        $db = $this->getConnection();
        $db->begin();
        $status = 999;
        $message = __('RESET_PASSWORD_FAIL');
        $obj_PhoneTokens  = TableRegistry::get("PhoneTokens");
        $phone_user = $obj_PhoneTokens->find('all', [
            'fields' => [
                'PhoneTokens.phone_number',
                'PhoneTokens.created',
            ],
            'conditions' => [
                'PhoneTokens.token' => $data['token_reset'],
                'PhoneTokens.is_used' => 0
            ]
        ])->first();
        if (!$phone_user) {
            $message = "INVALID_TOKEN";
            goto return_data;
        }
        $exp_token = Configure::read("expiry_time_token_reset");
        if (strtotime(($phone_user->created)->format('Y-m-d H:i:s')) + $exp_token < strtotime(date('Y-m-d H:i:s'))) {
            $message = "TOKEN_IS_EXPIRED";
            goto return_data;
        }

        $pwhash = $this->create_member_password($data['password']);
        $_data = array(
            'password'  => $pwhash,
        );

        $users = $this->find('all', [
            'conditions' => [
                'Users.phone_number'           => $phone_user['phone_number'], 
            ],
        ])->first();
        $users = $this->patchEntity($users, $_data);

        if ($users->hasErrors()) {
            $status = 999;
            $message = $users->getErrors();
            goto return_data;
        }

        if ($this->save($users)) {

            $obj_PhoneTokens->update_is_used($phone_user['phone_number'], $data['token_reset']);
            $db->commit();
            $status = 200;
            $message = __('DATA_IS_SAVED');
        }

        return_data:
        return array(
            'status' => $status,
            'message' => $message,
        );
    }

    public function update($id, $role, $phone_number, $email = null)
    {
        $status = 999;
        $message = 'CHANGE_PASSWORD_FAIL';
        $params = [];

        $user = $this->find('all', [
            'conditions'    => ['Users.id' => $id]
        ])->first();
        if (!$user) {
            goto set_result;
        }
 
        $check_phone_email = $this->check_phone_email($phone_number, $email);

        if ($user->phone_number != $phone_number && $check_phone_email['status'] == 304) {
            return $check_phone_email;
        } 
        
        if (!empty($email)) {
            if ($user->email != $email && $check_phone_email['status'] == 305) {
                return $check_phone_email;
            }
        } 

        $user->role = $role;
        $user->phone_number = $phone_number;
        $user->email = $email;
        $user->enabled = true;
        if (!$this->save($user)) {
            goto set_result;
        }

        $status = 200;
        $message = 'SAVE_DATA_SUCCESSFULLY';
        set_result:
        return [
            'status'    => $status,
            'message'   => $message,
            'params'    => $params,
        ];
    }

    public function login($payload)
    {
        // check username is email / phone_number number
        $username = $payload['username'];
        $password = $payload['password'];

        $conditions = [
            'enabled' => true,
        ];
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $conditions['email'] = $username;
        } else {
            $conditions['phone_number'] = $username;
        }

        $result = $this->find('all', [
            'conditions' => $conditions,
            'fields' => [
                'Users.id',
                'Users.password',
                'Users.user_role_id',
            ],
        ]);


        if ($result->count() > 0) {

            $data = $result->first();
            if ($this->verify_member_password($password, $data->password)) {
                // gen token
                $exp = time() + Configure::read('jwt.exp');
                $token = JWT::encode(json_encode($payload) . $exp, Security::getSalt(), 'HS256');

                // update token to users  
                $data->token    = $token;
                $data->exp      = $exp;

                if ($model = $this->save($data)) {
                    unset($model->password);
                    return $model;
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function check_valid($token, $user_role_id)
    { 
        return $this->find('all', [
            'conditions' => [
                'Users.enabled' => true,
                'Users.user_role_id' => $user_role_id,
                'Users.token' => $token,
                'Users.exp >' . strtotime(date('Y-m-d H:i:s')),
            ],
            'fields' => [
                'id',
                'email',
                'phone_number',
                'user_role_id',
                'exp',
            ],
            'contain' => [
                'CidcParents',
                'Staffs'
            ],
        ])->first();
    }

    public function check_sms_forgot_code($data)
    {
        $status = 200;
        $message = __('data_is_saved');
        $params = [];
        $obj_UserVerifications = TableRegistry::get('UserVerifications');
        $flag = $obj_UserVerifications->verify_code(
            $data['phone_number'],
            $data['code'],
            array_search('Forgot password', $obj_UserVerifications->verification_types),
            array_search('Sms', $obj_UserVerifications->verification_methods)
        );

        if ($flag) {
            $status = 200;
            $message = __d('user', 'valid_code');
            $obj_PhoneTokens = TableRegistry::get('PhoneTokens');
            $token = $this->create_phone_token($data['phone_number']);
            $phone_token = $obj_PhoneTokens->newEmptyEntity();
            $phone_token->token = $token;
            $phone_token->phone_number = $data['phone_number'];
            if (!$model = $obj_PhoneTokens->save($phone_token)) {
                $status = 999;
                $message = 'FAIL_CREATE_TOKEN';
                goto set_result;
            }
            $obj_UserVerifications->update_status_is_used(
                $data['phone_number'],
                array_search('Forgot password', $obj_UserVerifications->verification_types),
                array_search('Sms', $obj_UserVerifications->verification_methods),
                $data['code'],
            );
            $params['token_reset'] = $model->token;
        } else {
            $status = 401;
            $message = 'INVALID_CODE';
        }
        set_result:
        return array(
            'status' => $status,
            'message' => $message,
            'params'  => $params
        );
    }

    public function check_token_reset($token)
    {
        $status = 200;
        $message = 'VALID_TOKEN';
        $params = (object)[];
        $obj_PhoneTokens = TableRegistry::get('PhoneTokens');
        $token = $obj_PhoneTokens->find('all', [
            'conditions' => [
                'PhoneTokens.token'     => $token,
                'PhoneTokens.is_used'   => false
            ]
        ])->first();
        if (!$token) {
            $status = 999;
            $message = 'INVALID_TOKEN';
        }
        set_result:
        return array(
            'status' => $status,
            'message' => $message,
            'params'  => $params
        );
    }

    public function is_duplicate_email($email, $user_role_id, $id = array())
    {
        $conditions = [
            'Users.email' => $email,
           // 'Users.user_role_id' => $user_role_id
        ];

        if ($id) {
            $conditions['Users.id <>'] = $id;
        }

        $user = $this->find('all', [
            'conditions' =>  $conditions,
        ])->first(); 
        return $user ? true : false;
    }

    public function is_duplicate_phone($phone, $user_role_id, $id = array())
    {
        $conditions = [
            'Users.phone_number' => $phone,
           // 'Users.user_role_id' => $user_role_id
        ];

        if ($id) {
            $conditions['Users.id <>'] = $id;
        } 

        $user = $this->find('all', [
            'conditions' =>  $conditions,
        ])->first();
        return $user ? true : false;
    }

    public function get_user_id_by_token($token) {
        $temp = $this->find('all', [
            'conditions' => [
                'Users.token' => $token
            ],
            'fields' => [
                'Users.id',
            ]
        ])->first();

        return $temp ? $temp->id : null;
    }
}

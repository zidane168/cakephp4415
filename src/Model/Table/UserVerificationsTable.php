<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\Core\Configure;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UserVerifications Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\UserVerification newEmptyEntity()
 * @method \App\Model\Entity\UserVerification newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\UserVerification[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UserVerification get($primaryKey, $options = [])
 * @method \App\Model\Entity\UserVerification findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\UserVerification patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UserVerification[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\UserVerification|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UserVerification saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UserVerification[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\UserVerification[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\UserVerification[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\UserVerification[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UserVerificationsTable extends Table
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

        $this->setTable('user_verifications');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

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
            ->integer('phone_number')
            ->requirePresence('phone_number', 'create')
            ->notEmptyString('phone_number');

        $validator
            ->requirePresence('code', 'create')
            ->notEmptyString('code');

        $validator
            ->requirePresence('verification_type', 'create')
            ->notEmptyString('verification_type');

        $validator
            ->requirePresence('verification_method', 'create')
            ->notEmptyString('verification_method');

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

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        // $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }

    public $verification_methods = array(
        1 => 'Sms',
       // 2 => 'Email',
    );

    public $verification_types = array(
        1 => 'Forgot password',
        2 => 'Register',
    );

    public function get_verification_methods()
    {
        return [
            1 => __('sms'),
            // 2 => __('email'),
        ];
    }

    public function get_verification_types()
    {
        return [
            1 => __('forgot_password'),
            2 => __('register'), 
        ];
    }

    public function handling_verify_data($verification_method_id, $verification_type_id, $email, $phone_number, $is_dev = null)
    {
        // get number of member verification on current day

        $this->updateAll(
            [
                'enabled' => 0
            ],  // fields need to change
            [
                'verification_type'     => $verification_type_id,
                'verification_method'   => $verification_method_id,
                'phone_number'                 => $phone_number,
            ],  // conditions
        );

        $number_verify = $this->get_number_verification_member($phone_number);
        if ($number_verify >= (int)Configure::read('sms.max_sms_credits')) {
            $status  = 999;
            $message = __d('member', 'sms_over_sent_on_day', (int)Configure::read('sms.max_sms_credits'));
            goto return_data;
        }

        $verify_code = $this->generatePin(Configure::read('sms.verify_digit_length'));
        if (Configure::read('env') == 'local') {
            $verify_code = 111111;
        }

        $expired = date('Y-m-d H:i:s', strtotime(Configure::read('sms.timeout_verify')));

        // save to db;
        $memberVerifications = $this->newEmptyEntity();
        $memberVerifications = $this->patchEntity($memberVerifications, array(
            'verification_method'       => $verification_method_id,
            'verification_type'         => $verification_type_id,
            'code'                      => $verify_code,
            'expired'                   => $expired,
        ));

        if ($verification_method_id == array_search('Sms', $this->verification_methods)) {
            $memberVerifications->phone_number = $phone_number;
        } else {
            $memberVerifications->email = $email;
        }

        if ($memberVerifications->hasErrors()) {
            $status = 999;
            $message = $memberVerifications->getErrors();
            goto return_data;
        }

        if ($this->save($memberVerifications)) {
            return array(
                'status'            => 200,
                'verify_code'       => $verify_code,
                'expired'           => $expired,
                'number_sent_verification' => ($number_verify + 1),
            );
        }

        return_data:
        return array(
            'status' => $status,
            'message' => $message,
        );
    }

    public function get_number_verification_member($phone_number)
    {
        $query = $this->find()
            ->where([
                'UserVerifications.phone_number' => $phone_number,
                'DATE(UserVerifications.created)' => date('Y-m-d'),
            ]);
        return $query->count();
    }

    public function update_status_is_used($phone_number, $verification_type, $verification_method, $code)
    {
        $this->updateAll(
            ['is_used' => 1],
            [
                'verification_type'     => $verification_type,
                'verification_method'   => $verification_method,
                'phone_number'                 => $phone_number,
                'code'                  => $code,
            ],
        );
    }

    public function update_enabled_to_disabled($phone_number, $verification_type, $verification_method)
    {
        $this->updateAll(
            ['enabled' => 0],
            [
                'verification_type'     => $verification_type,
                'verification_method'   => $verification_method,
                'phone_number'                 => $phone_number,
            ],
        );
    }


    public function verify_code($phone_number, $code, $verification_type = 2, $verification_method = 1)
    { // default: register, sms
        $data_MemberVerifications = $this->find()
            ->where([
                'phone_number'     => $phone_number,
                'verification_type'     => $verification_type,
                'verification_method'   => $verification_method,
                'is_used'   => 0,
                'enabled'   => 1,
            ])
            ->select(['code', 'created'])
            ->first();

        if ($data_MemberVerifications) {
            $data_MemberVerifications = $data_MemberVerifications->toArray();
            if (date('Y-m-d H:i:s ', strtotime($data_MemberVerifications['created']->format('Y-m-d H:i:s') . Configure::read('expiry_date_sms.time'))) < date('Y-m-d H:i:s')) {
                return false;
            }
            // compare code
            if ($code === $data_MemberVerifications['code']) {
                return true;
            }

            // for test
            // if (Configure::read('env') != 'production' && $code == '123456') {
            //     return  true;
            // }
        }
        return false;
    }
}

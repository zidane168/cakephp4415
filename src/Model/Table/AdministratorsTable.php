<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Utility\Hash;      // Hash::extract

use App\Controller\Admin\AppController;
use App\Model\Entity\Administrator;

/**
 * Administrators Model
 *
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 * @property \App\Model\Table\RolesTable&\Cake\ORM\Association\BelongsToMany $Roles
 *
 * @method \App\Model\Entity\Administrator newEmptyEntity()
 * @method \App\Model\Entity\Administrator newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Administrator[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Administrator get($primaryKey, $options = [])
 * @method \App\Model\Entity\Administrator findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Administrator patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Administrator[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Administrator|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Administrator saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Administrator[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Administrator[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Administrator[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Administrator[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */


use Cake\ORM\TableRegistry; // use load model (ClassRegistry::init - cakephp2)
use Cake\Core\Configure;

class AdministratorsTable extends Table
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

        $this->setTable('administrators');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Centers', [
            'foreignKey' => 'center_id',
        ]);
        $this->belongsToMany('Roles', [
            'foreignKey' => 'administrator_id',
            'targetForeignKey' => 'role_id',
            'joinTable' => 'administrators_roles',
        ]);
        $this->hasMany('AdministratorsAvatars', [
            'foreignKey' => 'administrator_id',
            'dependent' => true,
        ]);

        $this->hasMany('AdministratorManageCenters', [
            'foreignKey' => 'administrator_id',
            'dependent' => true
        ]);

        $this->addBehavior('MyCommonFunc');   // use Common Function; vilh

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
            ->scalar('token')
            ->maxLength('token', 191)
            ->allowEmptyString('token');

        $validator
            ->scalar('name')
            ->maxLength('name', 191)
            ->allowEmptyString('name');

        $validator
            ->email('email')
            ->allowEmptyString('email');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 191)
            ->allowEmptyString('phone');

        $validator
            ->scalar('password')
            ->maxLength('password', 191)
            ->allowEmptyString('password');

        $validator
            ->dateTime('last_logged_in')
            ->allowEmptyDateTime('last_logged_in');

        $validator
            ->scalar('code_forgot')
            ->maxLength('code_forgot', 6)
            ->allowEmptyString('code_forgot');

        $validator
            ->dateTime('created_code_forgot')
            ->allowEmptyDateTime('created_code_forgot');

        $validator
            ->integer('time_input_code')
            ->allowEmptyString('time_input_code');

        $validator
            ->integer('time_input_pass')
            ->allowEmptyString('time_input_pass');

        $validator
            ->boolean('enabled')
            ->allowEmptyString('enabled');

        $validator
            ->integer('updated_by')
            ->allowEmptyString('updated_by');

        $validator
            ->integer('created_by')
            ->allowEmptyString('created_by');

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
        $rules->add($rules->isUnique(['email']), ['errorField' => 'email']);
        $rules->add($rules->existsIn(['center_id'], 'Centers'), ['errorField' => 'center_id']);

        return $rules;
    }

    public function login($email, $raw_password)
    { 
        // $raw_password = 'Aa123456';
        // debug($this->create_admin_password($raw_password));
        // exit;

        $administrator = $this->find('all', array(
            'fields' => [
                'Administrators.id',
                'Administrators.email',
                'Administrators.name',
                'Administrators.password',
            ],
            'conditions' => array(
                'Administrators.email' => $email,
                'Administrators.enabled' => true,
            ),
            'contain' => [
                'AdministratorsAvatars' => [
                    'fields' => [
                        'AdministratorsAvatars.administrator_id',
                        'AdministratorsAvatars.path',
                    ],
                ],
            ],
            'recursive' => -1
        ))->first();

        if (
            $administrator && !empty($administrator) &&
            $this->verify_admin_password($raw_password, $administrator->password)
        ) {    // use MyCommonFunc Function

            $result = $administrator;

            if (isset($last_logged_user['status']) && ($last_logged_user['status'] == true)) {
                $result->last_logged_in = $last_logged_user['last_logged_in'];
            }

            $obj_AdministratorsRole =  TableRegistry::get('AdministratorsRoles');
            $roles = $obj_AdministratorsRole->get_administrator_roles($result->id);

            if ($roles) {
                $result->Roles             = $roles;
                $result->is_admin         = false;    // admin vtl 

                foreach ($roles as $r) {
 
                    if (strcmp($r->slug, Configure::read('web.super_role')) == 0) {                // change
                        $result->is_admin = true;
                    }
                }

                $role_ids = Hash::extract($roles, "{n}.id");
                $obj_RolesPermission = TableRegistry::get('RolesPermissions');
                $result->Permissions = $obj_RolesPermission->get_permissions_by_role($role_ids); 

                // Get management data center
                $obj_AdministratorManageCenters = TableRegistry::get('AdministratorManageCenters');
                $result->list_centers_management = $obj_AdministratorManageCenters->get_list_management($result->id); 
                $result->is_manage_all_center_data = $obj_AdministratorManageCenters->is_manage_all_center_data($result->id); 
            }

            $logged_user = array(
                'status' => true,
                'message' => __d('administration', 'login_success'),
                'params' => $result
            );
        } else {
            $logged_user = array(
                'status' => false,
                'message' => __d('administration', 'login_fail'),
            );
        }
        return_result:
        return $logged_user;
    }

    public function update_last_logged_user($administrator_id)
    {
        $current = date('Y-m-d H:i:s');

        $data = array(
            'id' => $administrator_id,
            'last_logged_in' => $current,
        );

        $administrator = $this->get($administrator_id);
        $data = $this->patchEntity($administrator, $data);

        if ($this->save($data)) {
            return array(
                'status' => true,
                'last_logged_in' => $current
            );
        } else {
            return array(
                'status' => false,
            );
        }
    }

    public function updateNewPassword($id, $oldPassword, $newPassword)
    { 
        $administrator = $this->find('all', array(
            'fields' => [
                'Administrators.id',
                'Administrators.email',
                'Administrators.name',
                'Administrators.password',
            ],
            'conditions' => array(
                'Administrators.id' => $id,
                'Administrators.enabled' => true,
            ),
            'recursive' => -1
        ))->first();

        $status = true;
        $message = "";

        if ($administrator && !empty($administrator) &&  $this->verify_admin_password($oldPassword, $administrator->password)) {    // use MyCommonFunc Function
            $temp = array(
                'password' => $this->create_admin_password($newPassword),
            );

            $data_Administrators = $this->patchEntity($administrator, $temp);

            // update new password
            if ($this->save($data_Administrators)) {
                $status = true;
                $message = __('data_is_saved');
            } else {
                $status = false;
                $message = __('data_is_not_saved');
            }
        } else {
            $status = false;
            $message = "Old password not found or This Administrator don't exist";
        }
        return array(
            'status' => $status,
            'message' => $message,
        );
    }
}

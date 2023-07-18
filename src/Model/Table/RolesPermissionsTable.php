<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RolesPermissions Model
 *
 * @property \App\Model\Table\RolesTable&\Cake\ORM\Association\BelongsTo $Roles
 * @property \App\Model\Table\PermissionsTable&\Cake\ORM\Association\BelongsTo $Permissions
 *
 * @method \App\Model\Entity\RolesPermission newEmptyEntity()
 * @method \App\Model\Entity\RolesPermission newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\RolesPermission[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\RolesPermission get($primaryKey, $options = [])
 * @method \App\Model\Entity\RolesPermission findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\RolesPermission patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\RolesPermission[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\RolesPermission|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RolesPermission saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RolesPermission[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\RolesPermission[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\RolesPermission[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\RolesPermission[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RolesPermissionsTable extends Table
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

        $this->setTable('roles_permissions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
        
        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
        ]);
        $this->belongsTo('Permissions', [
            'foreignKey' => 'permission_id',
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
        $rules->add($rules->existsIn(['role_id'], 'Roles'), ['errorField' => 'role_id']);
        $rules->add($rules->existsIn(['permission_id'], 'Permissions'), ['errorField' => 'permission_id']);

        return $rules;
    }

    
	public function get_permissions_by_role($role_ids) {
        $result = array();
        $roles_permissions = $this->find('all', array(
                'conditions' => array(
                    'RolesPermissions.role_id IN' => $role_ids,
                ),
            ))
            ->select([
                'Permissions.p_controller',     // phai viet nhu vay T_T, ko dc viet lien 'Permissions.p_controller, Permissions.p_model' T_T
                'Permissions.p_model',
                'action' => 'GROUP_CONCAT(Permissions.action)',
            ])
            ->join([
                'table' => 'permissions',
                'alias' => 'Permissions',
                'type'  => 'INNER',
                'conditions' => 'RolesPermissions.permission_id = Permissions.id',
            ])
            ->group(['Permissions.p_controller', 'Permissions.p_model']);
    
        $roles_permissions = $roles_permissions->toArray();

        foreach ($roles_permissions as $item) {

            $new_item = array(
                'p_controller'  => $item['Permissions']['p_controller'],
                'p_model'       => $item['Permissions']['p_model'],
            );
            $actions = explode(',', $item->action);
            foreach($actions as $action){
                $new_item[$action] = true;
            }
            $result[$item['Permissions']['p_model']] = $new_item;
        }
		return $result;
    }



    public function get_permission_ids_by_role($role_id) {
        // $role_permissions = $this->find('list', array(
        //     'fields' => array('RolesPermissions.permission_id'),
        //     'conditions' => array(
        //         'RolesPermissions.role_id' => $role_id
        //     ),
        //     'recursive' => -1
        // ));

        $role_permissions = $this->find('list', 
            [
                'keyField'          => 'id',
                'valueField'        => 'permission_id',
            ],
        )->where([
            'role_id' => $role_id,
        ]);

		return $role_permissions->toArray();
	}
}

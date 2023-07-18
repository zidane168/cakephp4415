<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Utility\Hash;

/**
 * Permissions Model
 *
 * @property \App\Model\Table\RolesTable&\Cake\ORM\Association\BelongsToMany $Roles
 *
 * @method \App\Model\Entity\Permission newEmptyEntity()
 * @method \App\Model\Entity\Permission newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Permission[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Permission get($primaryKey, $options = [])
 * @method \App\Model\Entity\Permission findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Permission patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Permission[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Permission|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Permission saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Permission[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Permission[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Permission[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Permission[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PermissionsTable extends Table
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

        $this->setTable('permissions');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsToMany('Roles', [
            'foreignKey' => 'permission_id',
            'targetForeignKey' => 'role_id',
            'joinTable' => 'roles_permissions',
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
            ->scalar('slug')
            ->maxLength('slug', 191)
            ->allowEmptyString('slug');

        $validator
            ->scalar('name')
            ->maxLength('name', 191)
            ->allowEmptyString('name');

        $validator
            ->scalar('p_controller')
            ->maxLength('p_controller', 100)
            ->requirePresence('p_controller', 'create')
            ->notEmptyString('p_controller');

        $validator
            ->scalar('p_model')
            ->maxLength('p_model', 191)
            ->allowEmptyString('p_model');

        $validator
            ->scalar('action')
            ->maxLength('action', 50)
            ->requirePresence('action', 'create')
            ->notEmptyString('action');

        $validator
            ->integer('updated_by')
            ->allowEmptyString('updated_by');

        $validator
            ->integer('created_by')
            ->allowEmptyString('created_by');

        return $validator;
    }



	public function get_list() {
		$permissions = $this->find('all', array(
			// 'conditions' => array(
			// 	'slug LIKE' => 'perm-' . $type . '-%'
            // ),
            'order' => 'name ASC', //array('p_plugin', 'p_controller', 'p_model', 'id'),
			'recursive' => -1
		));

        $permissions = $permissions->toArray();

		$list = array();
		if ($permissions) {

            // make permission first
			// $permissions = Hash::extract( $permissions, "{n}.Permission" );
			foreach ($permissions as $key => $permission) {
				$model = ucfirst( $permission->p_model );

				$list[ $model ][] = array(
					'id'    => $permission->id,
					'name'  => $permission->name,
					'action' => $permission->action,
				);
			}
		}

		return $list;
	}
}

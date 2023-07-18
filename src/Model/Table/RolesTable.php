<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Roles Model
 *
 * @property \App\Model\Table\AdministratorsTable&\Cake\ORM\Association\BelongsToMany $Administrators
 * @property \App\Model\Table\PermissionsTable&\Cake\ORM\Association\BelongsToMany $Permissions
 *
 * @method \App\Model\Entity\Role newEmptyEntity()
 * @method \App\Model\Entity\Role newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Role[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Role get($primaryKey, $options = [])
 * @method \App\Model\Entity\Role findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Role patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Role[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Role|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Role saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Role[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Role[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Role[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Role[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RolesTable extends Table
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

        $this->setTable('roles');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsToMany('Administrators', [
            'foreignKey' => 'role_id',
            'targetForeignKey' => 'administrator_id',
            'joinTable' => 'administrators_roles',
        ]);
        $this->belongsToMany('Permissions', [
            'foreignKey' => 'role_id',
            'targetForeignKey' => 'permission_id',
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
            ->integer('updated_by')
            ->allowEmptyString('updated_by');

        $validator
            ->integer('created_by')
            ->allowEmptyString('created_by');

        return $validator;
    }


    public function get_list() {
        return $this->find('list', array(
            'keyField' => 'id',
            'valueField' => 'slug',
        ));
    }



	public function getListRoles($is_admin){
		// $conditions = array('Role.slug LIKE' => 'role-%');

        // if (!$is_admin) {
        //     $conditions['Role.slug <> '] = Configure::read('web.super_role');
        // }

		$roles = $this->find('list', array(
            [
                'keyField'      => 'id',
                'valueField'    => 'name'      // cannot use CompanyLanguages.name x
            ],
            ['limit' => 200],
		))->where([
            // 'slug <>' => Configure::read('web.super_role'),
        ]);

        // return $this->find('list', 
        //     [
        //         'keyField' => 'company_id',
        //         'valueField' => 'name'      // cannot use CompanyLanguages.name x
        //     ],
        //     ['limit' => 200]
        // )->where([
        //     'alias' => $language,
        // ]);
        
		
		return $roles;
	}
}

<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AdministratorsRoles Model
 *
 * @property \App\Model\Table\AdministratorsTable&\Cake\ORM\Association\BelongsTo $Administrators
 * @property \App\Model\Table\RolesTable&\Cake\ORM\Association\BelongsTo $Roles
 *
 * @method \App\Model\Entity\AdministratorsRole newEmptyEntity()
 * @method \App\Model\Entity\AdministratorsRole newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\AdministratorsRole[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AdministratorsRole get($primaryKey, $options = [])
 * @method \App\Model\Entity\AdministratorsRole findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\AdministratorsRole patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AdministratorsRole[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\AdministratorsRole|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AdministratorsRole saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AdministratorsRole[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\AdministratorsRole[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\AdministratorsRole[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\AdministratorsRole[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AdministratorsRolesTable extends Table
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

        $this->setTable('administrators_roles');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Administrators', [
            'foreignKey' => 'administrator_id',
        ]);
        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
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
        $rules->add($rules->existsIn(['administrator_id'], 'Administrators'), ['errorField' => 'administrator_id']);
        $rules->add($rules->existsIn(['role_id'], 'Roles'), ['errorField' => 'role_id']);

        return $rules;
    }




    
	public function get_administrator_roles($administrator_id) {
		$result = array();
        $administrator_roles = $this->find('all', array(
            'fields' => array('AdministratorsRoles.administrator_id', 'AdministratorsRoles.role_id'),
            'conditions' => array('AdministratorsRoles.administrator_id' => $administrator_id),
            'contain' => array(
                'Roles' => array(
                    'fields' => array( 'Roles.id', 'Roles.slug', 'Roles.name' ),
                )
            )
        ));
        

        $administrator_roles = $administrator_roles->toArray(); // use all need use this line
        foreach ($administrator_roles as $r) {
            $result[] = $r->role;
        }

		return $result;
	}



    public function get_user_by_role($role_id) {
		$administrator_roles = $this->find('list', array(
            'keyField' => 'administrator_id',
            'valueField' => 'administrator_id'
		))->where(
            ['role_id' => $role_id]
        );
		
		return $administrator_roles;
	}

    public function getCurrentRole($administrator_id) {
        $administrator_roles = $this->find('list', array(
            'keyField' => 'role_id',
            'valueField' => 'role_id'
		))->where(
            ['administrator_id' => $administrator_id]
        );
		
		return $administrator_roles;
    }
}

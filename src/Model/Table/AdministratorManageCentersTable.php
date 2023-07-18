<?php 
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AdministratorManageCenters Model
 *
 * @property \App\Model\Table\CentersTable&\Cake\ORM\Association\BelongsTo $Centers
 * @property \App\Model\Table\AdministratorsTable&\Cake\ORM\Association\BelongsTo $Administrators
 *
 * @method \App\Model\Entity\AdministratorManageCenter newEmptyEntity()
 * @method \App\Model\Entity\AdministratorManageCenter newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\AdministratorManageCenter[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AdministratorManageCenter get($primaryKey, $options = [])
 * @method \App\Model\Entity\AdministratorManageCenter findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\AdministratorManageCenter patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AdministratorManageCenter[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\AdministratorManageCenter|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AdministratorManageCenter saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AdministratorManageCenter[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\AdministratorManageCenter[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\AdministratorManageCenter[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\AdministratorManageCenter[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AdministratorManageCentersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
  
    private $MAX_CENTER = 4;    // for full control CENTER data administrator;
 
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('administrator_manage_centers');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Centers', [
            'foreignKey' => 'center_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Administrators', [
            'foreignKey' => 'administrator_id',
            'joinType' => 'INNER',
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

        $validator
            ->integer('created_by')
            ->allowEmptyString('created_by');

        $validator
            ->integer('updated_by')
            ->allowEmptyString('updated_by');

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
        $rules->add($rules->existsIn(['center_id'], 'Centers'), ['errorField' => 'center_id']);
        $rules->add($rules->existsIn(['administrator_id'], 'Administrators'), ['errorField' => 'administrator_id']);

        return $rules;
    }

    public function getCurrentCenter($administrator_id)
    {
        $administrator_centers = $this->find('list', array(
            'keyField' => 'center_id',
            'valueField' => 'center_id'
        ))->where(
            ['administrator_id' => $administrator_id]
        );

        return $administrator_centers;
    }
    
    public function get_list_management($administrator_id) {
        $temp = $this->find('all', [
            'conditions' => [
                'AdministratorManageCenters.administrator_id' => $administrator_id, 
                'AdministratorManageCenters.enabled' => true,
            ],
            'fields' => [
                'AdministratorManageCenters.center_id',
            ],
        ])->toArray();
 
        $list = [];
        foreach ($temp as $value) {
            $list[] = $value->center_id;
        }

        return $list;
    }

    public function is_manage_all_center_data($administrator_id) {
        $list = $this->get_list_management($administrator_id);
        if (count($list) == $this->MAX_CENTER) {
            return true;
        }

        return false;
    }

    public function is_data_belong_to_current_user($center_id, $administrator_id) {
        $list = $this->get_list_management($administrator_id);  // [1,2,3]
        
        foreach ($list as $value) {
            if ($value == $center_id) {
                return true;
            }
        }

        return false;
    }
}

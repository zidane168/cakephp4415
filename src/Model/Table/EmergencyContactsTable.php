<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * EmergencyContacts Model
 *
 * @property \App\Model\Table\EmergencyContactLanguagesTable&\Cake\ORM\Association\HasMany $EmergencyContactLanguages
 * @property \App\Model\Table\KidsEmergenciesTable&\Cake\ORM\Association\HasMany $KidsEmergencies
 *
 * @method \App\Model\Entity\EmergencyContact newEmptyEntity()
 * @method \App\Model\Entity\EmergencyContact newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\EmergencyContact[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\EmergencyContact get($primaryKey, $options = [])
 * @method \App\Model\Entity\EmergencyContact findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\EmergencyContact patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\EmergencyContact[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\EmergencyContact|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\EmergencyContact saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\EmergencyContact[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\EmergencyContact[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\EmergencyContact[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\EmergencyContact[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class EmergencyContactsTable extends Table
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

        $this->setTable('emergency_contacts');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('EmergencyContactLanguages', [
            'foreignKey' => 'emergency_contact_id',
            'dependent'  => true
        ]);
        $this->hasMany('KidsEmergencies', [
            'foreignKey' => 'emergency_contact_id',
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
            ->integer('modified_by')
            ->allowEmptyString('modified_by');

        return $validator;
    }

    public function get_list($language, $conditions = array()) // add product admin page
    {
        $emergencyContacts = $this->find(
            'list',
            [
                'keyField' => 'id',
                'valueField' => function ($row) {
                    return $row->emergency_contact_languages[0]->name;
                }
            ]
        )
            ->where([$conditions])
            ->contain(
                [
                    'EmergencyContactLanguages' => [
                        'conditions' => ['EmergencyContactLanguages.alias' => $language]
                    ]
                ]
            );

        return $emergencyContacts;
    }

    // $emergency_contact = {"name": "", "relationship_id": 1, "phone_number: "", "zh_HK_name": "", "en_US_name": "" }
    public function add($emergency_contact)
    { 

        $db = $this->getConnection();
        $db->begin(); 

        $data = $this->find('all', [
            'conditions' => [
                'phone_number' => $emergency_contact['phone_number']
            ],
        ])->First();

        if ($data) {   // exists
            $db->commit();  /// missing commit here will cause cannot add 
            return [
                'status'    => 200,
                'message'   => __('is_exists'),
                'params'    => $data,
            ];
        }

        // add news
        $data_emergency_contact = [
            'phone_number' => $emergency_contact["phone_number"]
        ];
        $entity_emergency_contact = $this->newEntity($data_emergency_contact);

        if ($model = $this->save($entity_emergency_contact)) {

            // save language
            $obj_Languages = TableRegistry::get('Languages');
            $languages = $obj_Languages->find('all');
            $data_emergency_contact_language = [];
            foreach ($languages as $lang) {
                $data_emergency_contact_language[] = [
                    'emergency_contact_id' => $model->id,
                    'alias' => $lang->alias,
                    'name'  => $emergency_contact["$lang->alias" . "_name"],
                ];
            }

            $entities_languages = $this->EmergencyContactLanguages->newEntities($data_emergency_contact_language);
            if (!$this->EmergencyContactLanguages->saveMany($entities_languages)) {
                $db->rollback();
                return [
                    'status'    => 999,
                    'message'   => __('data_is_not_saved') . ' EmergencyContactLanguage!',
                    'params'    => [],
                ];
            }

            $db->commit();
            return [
                'status'    => 200,
                'message'   => __('data_is_saved'),
                'params'    => $model,
            ];
        } 
    } 

    public function add_with_object($emergency_contact)
    {

        if (isset($emergency_contact) && !empty($emergency_contact) && !is_null($emergency_contact) && $emergency_contact != 'null') {

            $db = $this->getConnection();
            $db->begin();

            $ecs = $emergency_contact;
            if (gettype($emergency_contact) == 'string') {
                $ecs = json_decode($emergency_contact);
            }

            $data = $this->find('all', [
                'conditions' => [
                    'phone_number' => $ecs->phone_number
                ],
            ])->First();

            if ($data) {   // exists
                $db->commit();  /// missing commit here will cause cannot add 
                return [
                    'status'    => 200,
                    'message'   => __('is_exists'),
                    'params'    => $data,
                ];
            }

            // add news
            $data_emergency_contact = [
                'phone_number' => $ecs->phone_number
            ];
            $entity_emergency_contact = $this->newEntity($data_emergency_contact);

            if ($model = $this->save($entity_emergency_contact)) {

                // save language
                $obj_Languages = TableRegistry::get('Languages');
                $languages = $obj_Languages->find('all');
                $data_emergency_contact_language = [];
                foreach ($languages as $lang) {
                    $key = $lang->alias . "_name";
                    $data_emergency_contact_language[] = [
                        'emergency_contact_id' => $model->id,
                        'alias' => $lang->alias,
                        'name'  => $ecs->$key,
                    ];
                }

                $entities_languages = $this->EmergencyContactLanguages->newEntities($data_emergency_contact_language);
                if (!$this->EmergencyContactLanguages->saveMany($entities_languages)) {
                    $db->rollback();
                    return [
                        'status'    => 999,
                        'message'   => __('data_is_not_saved') . ' EmergencyContactLanguage!',
                        'params'    => [],
                    ];
                }

                $db->commit();
                return [
                    'status'    => 200,
                    'message'   => __('data_is_saved'),
                    'params'    => $model,
                ];
            }
        }
    }
}

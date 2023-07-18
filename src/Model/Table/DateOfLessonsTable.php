<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DateOfLessons Model
 *
 * @property \App\Model\Table\CidcClassesTable&\Cake\ORM\Association\BelongsTo $CidcClasses
 *
 * @method \App\Model\Entity\DateOfLesson newEmptyEntity()
 * @method \App\Model\Entity\DateOfLesson newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\DateOfLesson[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DateOfLesson get($primaryKey, $options = [])
 * @method \App\Model\Entity\DateOfLesson findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\DateOfLesson patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DateOfLesson[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\DateOfLesson|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DateOfLesson saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DateOfLesson[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\DateOfLesson[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\DateOfLesson[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\DateOfLesson[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DateOfLessonsTable extends Table
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

        $this->setTable('date_of_lessons');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('CidcClasses', [
            'foreignKey' => 'cidc_class_id',
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
            ->integer('day')
            ->requirePresence('day', 'create')
            ->notEmptyString('day');

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

        return $rules;
    }

    public function get_list($cidc_class_id) { 

        $conditions = [
            'DateOfLessons.cidc_class_id' => $cidc_class_id,
        ];

        return $this->find(
            'list',
            [
                'keyField' => 'id',
                'valueField' => 'day',
            ]
        )->where([$conditions]);  
    }

    public function convert_date_of_lessons_to_string($date_for_lessons) {
        $names = []; 
        if ($date_for_lessons === null) return "";

        foreach ($date_for_lessons as $lessons) {
          
            if ($lessons->day == 2) {
                $names[] = __d('cidcclass', 'each_monday');

            } elseif ($lessons->day == 3) {
                $names[] = __d('cidcclass', 'each_tuesday');

            } elseif ($lessons->day == 4) {
                $names[] = __d('cidcclass', 'each_wednesday');

            } elseif ($lessons->day == 5) {
                $names[] = __d('cidcclass', 'each_thursday');

            } elseif ($lessons->day == 6) {
                $names[] = __d('cidcclass', 'each_friday');
                
            } elseif ($lessons->day == 7) {
                $names[] = __d('cidcclass', 'each_saturday');

            } elseif ($lessons->day == 8) {
                $names[] = __d('cidcclass', 'each_sunday');
            } 
        }
  
        return implode(", ", $names);
    }
}

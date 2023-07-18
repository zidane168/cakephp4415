<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Validation\Validator;

/**
 * ImageTypes Model
 *
 * @property \App\Model\Table\CourseImagesTable&\Cake\ORM\Association\HasMany $CourseImages
 * @property \App\Model\Table\ImageTypeLanguagesTable&\Cake\ORM\Association\HasMany $ImageTypeLanguages
 *
 * @method \App\Model\Entity\ImageType newEmptyEntity()
 * @method \App\Model\Entity\ImageType newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\ImageType[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ImageType get($primaryKey, $options = [])
 * @method \App\Model\Entity\ImageType findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\ImageType patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ImageType[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ImageType|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ImageType saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ImageType[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ImageType[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\ImageType[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ImageType[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ImageTypesTable extends Table
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

        $this->setTable('image_types');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('CourseImages', [
            'foreignKey' => 'image_type_id',
        ]);
        $this->hasMany('ImageTypeLanguages', [
            'foreignKey' => 'image_type_id',
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
            ->boolean('enabled')
            ->allowEmptyString('enabled');

        $validator
            ->integer('modified_by')
            ->allowEmptyString('modified_by');

        $validator
            ->integer('created_by')
            ->allowEmptyString('created_by');

        return $validator;
    }





    public function find_list($conditions = array(), $language = 'en_US'){
		$data = array();

		if (isset($conditions) && !empty($conditions)) {
			$conditions = array_merge($conditions, array(
                'Language.alias' => $language,
                'ImageTypes.enabled' => true)
            );

        } else {
            $conditions = array(
                'ImageTypes.enabled' => true
            );
        }

		$data = $this->find('all', array(
			'fields' => array(
                'ImageTypes.id', 'Language.name', 'Language.description'
            ),
			'conditions' => $conditions,
			'order' => array('ImageTypes.id ASC'),
		))
        ->join([
            'table' => 'image_type_languages',
            'alias' => 'Language',
            'type'  => 'INNER',
            'conditions' => array(
                'ImageTypes.id = Language.image_type_id',
                // 'language.alias = \'' . $language . '\'',
            ),
        ])->toArray();

        $rel = array();
        foreach ($data as $d) {
            $rel[] = array(
                'id'    => $d->id,
                'name'  => $d->Language['name'] . " (" . $d->Language['description'] . ")",
            );
        }

        // get id by key, name by value 
        // $result = [
        //     [1] => 'xxx',
        // ]
        $result = Hash::combine($rel, '{n}.id', '{n}.name');
    
		return $result;
	}
    
    public function getImageTypeWithSlug($slug) {
        return $this->find('all')
            ->where(['slug' => $slug])
            ->select(['id'])
            ->first();
    }

}

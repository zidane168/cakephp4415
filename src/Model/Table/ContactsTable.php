<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use App\MyHelper\MyHelper;

/**
 * Contacts Model
 *
 * @property \App\Model\Table\ContactImagesTable&\Cake\ORM\Association\HasMany $ContactImages
 * @property \App\Model\Table\ContactLanguagesTable&\Cake\ORM\Association\HasMany $ContactLanguages
 *
 * @method \App\Model\Entity\Contact newEmptyEntity()
 * @method \App\Model\Entity\Contact newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Contact[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Contact get($primaryKey, $options = [])
 * @method \App\Model\Entity\Contact findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Contact patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Contact[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Contact|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Contact saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Contact[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Contact[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Contact[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Contact[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ContactsTable extends Table
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

        $this->setTable('contacts');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('ContactImages', [
            'foreignKey' => 'contact_id',
            'dependent'  => true
        ]);
        $this->hasMany('ContactLanguages', [
            'foreignKey' => 'contact_id',
            'dependent'  => true
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

    public function get_list($languages)
    {
        $conditions = [
            'Contacts.enabled' => true,
        ];
        $result = [];
        $url  = MyHelper::getUrl();
        $_result = $this->find('all', [
            'fields' => [
                'Contacts.id',
            ],
            'conditions' => $conditions,
            'contain'    => [
                'ContactLanguages' => [
                    'conditions' => [
                        'ContactLanguages.alias' => $languages
                    ]
                ],
                'ContactImages' => [
                    'fields' => [
                        'ContactImages.id',
                        'ContactImages.contact_id',
                        'path' => "CONCAT('$url', ContactImages.path)"
                    ]
                ]
            ]
        ]);
        foreach ($_result as $item) {
            $result[] = [
                'id'        => $item->id,
                'title'     => $item->contact_languages[0]->title,
                'content'   => $item->contact_languages[0]->content,
                'image'     => $item->contact_images[0]->path
            ];
        }

        set_result:
        return $result;
    }
}

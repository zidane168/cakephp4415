<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Routing\Router;
use App\MyHelper\MyHelper;


/**
 * News Model
 *
 * @property \App\Model\Table\NewsImagesTable&\Cake\ORM\Association\HasMany $NewsImages
 *
 * @method \App\Model\Entity\News newEmptyEntity()
 * @method \App\Model\Entity\News newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\News[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\News get($primaryKey, $options = [])
 * @method \App\Model\Entity\News findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\News patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\News[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\News|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\News saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\News[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\News[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\News[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\News[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class NewsTable extends Table
{

    public function joinLanguage($language)
    {
        return [
            [
                'table' => 'news_languages',
                'alias' => 'NewsLanguages',
                'type' => 'LEFT',
                'conditions' => [
                    'NewsLanguages.news_id = News.id',
                    'NewsLanguages.alias' => $language,
                ],
            ],
            [
                'table' => 'news_images',
                'alias' => 'NewsImages',
                'type' => 'INNER',
                'conditions' => [
                    'NewsImages.news_id = News.id',
                ],
            ]

        ];
    }

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('news');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('NewsImages', [
            'foreignKey' => 'news_id',
        ]);
        $this->hasMany('NewsLanguages', [
            'foreignKey' => 'news_id',
            'dependent' => true,
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
            ->date('date')
            ->allowEmptyDate('date');

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

    public function get_list_pagination($language, $payload)
    {
        $conditions = [
            'News.enabled' => true,
        ];

        $total = $this->find('all', [
            'conditions' => [
                'News.enabled' => true
            ],
        ])->count();
        $result = [];
        if (!$total) {
            goto set_result;
        }

        if (isset($payload['search']) && !empty($payload['search'])) {
            $conditions['LOWER(NewsLanguages.name) LIKE'] = '%' . strtolower($payload['search']) . '%';
        }

        $url = MyHelper::getUrl();
        $result = $this->find('all', [
            'fields' => [
                'id'        => 'News.id',
                'date'      => 'News.date',
                'title'     => 'NewsLanguages.title',
                'content'   => 'NewsLanguages.content',
                'image'     => "CONCAT('$url', NewsImages.path)"
            ],
            'conditions' => $conditions,
            'join' => $this->joinLanguage($language),
            'limit' => $payload['limit'],
            'page'  => (int)$payload['page'],

        ]); 

        set_result:
        return [
            'count' => $total,
            'items' => $result,
        ];
    }

    public function get_by_id($id, $language = 'en_US')
    {
        $url = MyHelper::getUrl();
        return $this->find('all', [
            'fields' => [
                'id'    => 'News.id',
                'date'  => 'News.date',
                'title'  => 'NewsLanguages.title',
                'content'   => 'NewsLanguages.content',
                'image'     => "CONCAT('$url', NewsImages.path)"
            ],
            'conditions' => [
                'News.id'        => $id,
                'News.enabled'   => true
            ],
            'join'  => $this->joinLanguage($language)
        ])->first();
    }

    public function create_news($data)
    {
        // pr($data);exit;
        $message = "";
        $params = [];
        $_news = $this->newEntity($data);
        if ($_news->hasErrors()) {
            $message = "DATA_IS_NOT_SAVE";
            goto set_result;
        }
        $news = $this->save($_news);
        if (!$news) {
            $message = "DATA_IS_NOT_SAVE";
            goto set_result;
        }


        // Save Language
        $callbackLanguages = function ($newsLanguages) use ($news) {
            return [
                'news_id'    => $news['id'],
                'title'      => $newsLanguages['title'],
                'content'    => $newsLanguages['content'],
                'alias'      => $newsLanguages['alias'],
                'author'     => $newsLanguages['author']
            ];
        };
        $newsLanguages = $this->NewsLanguages->newEntities(array_map($callbackLanguages, $data['name']));
        if (!$this->NewsLanguages->saveMany($newsLanguages)) {
            $message = "DATA_LANGUAGES_IS_NOT_SAVED";
            goto set_result;
        }


        $message = "DATA_IS_SAVED";

        set_result:
        return [
            'message'   => $message,
            'params'    => $news
        ];
    }

    public function edit_news($data)
    {
        $message = "";
        $params = [];

        $_news = $this->get($data['id']);
        // debug($_news);exit;
        $_news = $this->patchEntity($_news, $data);
        if ($_news->hasErrors()) {
            $message = "DATA_IS_NOT_SAVE";
            goto set_result;
        }
        $news = $this->save($_news);
        if (!$news) {
            $message = "DATA_IS_NOT_SAVE";
            goto set_result;
        }

        // delete all language of news
        $this->NewsLanguages->deleteAll([
            'NewsLanguages.news_id' => $data['id']
        ]);
        $callbackLanguages = function ($newsLanguages) use ($news) {
            return [
                'news_id'    => $news['id'],
                'title'      => $newsLanguages['title'],
                'content'    => $newsLanguages['content'],
                'alias'      => $newsLanguages['alias'],
                'author'     => $newsLanguages['author']
            ];
        };

        //save language
        $newsLanguages = $this->NewsLanguages->newEntities(array_map($callbackLanguages, $data['name']));
        if (!$this->NewsLanguages->saveMany($newsLanguages)) {
            $message = "DATA_LANGUAGES_IS_NOT_SAVED";
            goto set_result;
        }
        $message = "DATA_IS_SAVED";

        set_result:
        return [
            'message'   => $message,
            'params'    => $news
        ];
    }

    public function delete_by_id($id)
    {
        $news = $this->get($id);
        if ($this->delete($news)) {
            return "DATA_IS_DELETED";
        }
        return "DATA_IS_NOT_DELETED";
    }
}

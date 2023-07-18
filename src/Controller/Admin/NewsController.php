<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;


/**
 * News Controller
 *
 * @method \App\Model\Entity\News[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class NewsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $data_search = $this->request->getQuery();
        $_conditions =  array();

        if (isset($data_search['status']) && $data_search['status'] != "") {
            $_conditions['News.enabled'] = intval($data_search['status']);
        }

        if (isset($data_search['title']) && !empty($data_search['title'])) {
            $_conditions['LOWER(NewsLanguages.title) LIKE'] = '%' . trim(strtolower($data_search['title'])) . '%';
        }
        $this->paginate = array(
            'fields' => [
                'News.id',
                'News.date',
                'News.enabled',
                'News.created',
                'News.modified',
                'NewsLanguages.title',
                'NewsLanguages.author',
                'NewsLanguages.content',
            ],
            'conditions' => $_conditions,
            'order' => [
                'News.id DESC'
            ],
            'contain' => [
                'NewsImages' => [
                    'fields' => [
                        'NewsImages.news_id',
                        'NewsImages.path'
                    ],
                ],
            ],
            'join' => [
                'table' => 'news_languages',
                'alias' => 'NewsLanguages',
                'type' => 'INNER',
                'conditions' => [
                    'NewsLanguages.news_id = News.id',
                    'NewsLanguages.alias' => $this->lang18,
                ],
            ]
        );
        $news = $this->paginate($this->News, array(
            'limit' => Configure::read('web.limit')
        ));

        $this->set(compact('news', 'data_search'));
    }

    /**
     * View method
     *
     * @param string|null $id News id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $news = $this->News->get($id, [
            'contain' => [
                'CreatedBy', 'ModifiedBy',
                'NewsLanguages'
            ],
        ]);
        $language_input_fields = array(
            'title',
            'author',
            'content'
        );
        $languages = $news->news_languages;
        $this->set(compact('news', 'language_input_fields', 'languages'));
    }
    public function load_language()
    {
        $language_input_fields = array(
            'id',
            'alias',
            'title',
            'content',
            'author'
        );

        $obj_Language = TableRegistry::get('Languages');
        $languages_list = $obj_Language->get_languages();

        $languages_model = 'NewsLanguages';
        $languages_edit_model = 'news_languages';

        $this->set(compact('language_input_fields', 'languages_list', 'languages_model', 'languages_edit_model'));
    }

    public function load_image()
    {
        $model = 'News';
        $images_model = 'NewsImages';
        $add_new_images_url = Router::url(['controller' => 'News', 'action' => 'add_new_image_no_type', 'admin' => true]);

        $this->set(compact('model', 'images_model', 'add_new_images_url'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $model = 'News';
        $images_model = 'NewsImages';

        $news = $this->News->newEmptyEntity();
        if ($this->request->is('post')) {

            $db = $this->News->getConnection();
            $db->begin();

            $news = $this->News->patchEntity($news, $this->request->getData());
            $news_languages = $this->News->NewsLanguages->newEntities($this->request->getData()['NewsLanguages']);
            if ($model = $this->News->save($news)) {

                // 2, save language
                if (isset($news_languages) && !empty($news_languages)) {
                    foreach ($news_languages as $language) {
                        $language['news_id'] = $model->id;
                    }
                    if (!$this->News->NewsLanguages->saveMany($news_languages)) {
                        $this->Flash->error(__('data_is_not_saved'));
                        goto load_data;
                    }
                }

                // 3. save image
                $images = $this->request->getData('NewsImages');
                $save_data = array();

                if (isset($images) && !empty($images)) {

                    foreach ($images as $key => $image) {
                        $relative_path = 'uploads' . DS . $images_model;
                        $file_name_suffix = "image";

                        $uploaded = $this->Common->upload_images($image['image'], $relative_path, $file_name_suffix, $key);

                        $temp = array(
                            'path'              => $uploaded['path'],
                            'name'              => $uploaded['ori_name'],
                            'width'             => $uploaded['width'],
                            'height'            => $uploaded['height'],
                            'size'              => $uploaded['size'],
                            'news_id'           => $model->id,
                        );
                        $save_data[] = $temp;
                    }

                    if (isset($save_data) && !empty($save_data)) {
                        $orm_NewsImages = $this->News->NewsImages->newEntities($save_data);

                        if (!$this->News->NewsImages->saveMany($orm_NewsImages)) {
                            $db->rollback();
                            $this->Flash->error(__('data_is_not_saved') . " News Images");
                            $this->redirect(array('action' => 'index'));
                        }
                    }
                }

                $db->commit();
                $this->Flash->success(__('data_is_saved'));
                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The News could not be saved. Please, try again.'));
            }
        }

        load_data:
        $current_language = $this->lang18;
        $this->load_language();
        $this->load_image();
        $this->set(compact('news', 'current_language'));
    }

    /**
     * Edit method
     *
     * @param string|null $id News id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $model = 'News';
        $images_model = 'NewsImages';

        $news = $this->News->get($id, [
            'contain' => [
                'NewsImages',
                'NewsLanguages',
            ],
        ]);
        $images_edit_data   = $news->has('news_images') ? $news['news_images'] : array();
        $languages_edit_data = (isset($news['news_languages']) && !empty($news['news_languages'])) ? $news['news_languages'] : false;

        // add row for the replace this->request->date_add
        if ($this->request->is(['patch', 'post', 'put'])) {

            $db  = $this->News->getConnection();
            $db->begin();

            $news = $this->News->patchEntity($news, $this->request->getData());

            if ($this->News->save($news)) {

                // 3  save images
                $images = $this->request->getData('NewsImages');
                $save_data = array();

                if (isset($images) && !empty($images)) {
                    foreach ($images as $key => $image) {
                        $relative_path = 'uploads' . DS . $images_model;
                        $file_name_suffix = "image";

                        $uploaded = $this->Common->upload_images($image['image'], $relative_path, $file_name_suffix, $key);

                        $temp = array(
                            'path'          => $uploaded['path'],
                            'name'          => $uploaded['ori_name'],
                            'width'         => $uploaded['width'],
                            'height'        => $uploaded['height'],
                            'size'          => $uploaded['size'],
                            'news_id'       => $id,
                        );
                        $save_data[] = $temp;
                    } // end foreach

                    $orm_NewsImages = $this->News->NewsImages->newEntities($save_data);
                    if (!empty($orm_NewsImages)) {

                        if (!$this->News->NewsImages->saveMany($orm_NewsImages)) {
                            $db->rollback();
                            $this->Flash->error(__('data_is_not_saved'));
                            $this->redirect(array('action' => 'index'));
                        }
                    }
                }

                // 4, delete images
                if (isset($this->request->getData()['data']) && !empty($this->request->getData()['data'])) {
                    $data = $this->request->getData()['data'];
                    if (isset($data['remove_image']) && !empty($data['remove_image'])) {
                        $this->News->remove_uploaded_image('NewsImages', $data['remove_image']);
                    }
                }

                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The news could not be saved. Please, try again.'));
            }
        }

        load_data:
        $this->load_image();
        $this->load_language();
        $current_language = $this->lang18;
        $this->set(compact('news', 'current_language', 'languages_edit_data', 'images_edit_data'));
    }

    /**
     * Delete method
     *
     * @param string|null $id News id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $news = $this->News->get($id);
        if ($this->News->delete($news)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The news could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

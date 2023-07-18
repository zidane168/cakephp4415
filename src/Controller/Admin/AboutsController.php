<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

/**
 * Abouts Controller
 *
 * @method \App\Model\Entity\About[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AboutsController extends AppController
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
            $_conditions['Abouts.enabled'] = intval($data_search['status']);
        }

        if (isset($data_search['title']) && !empty($data_search['title'])) {
            $_conditions['LOWER(AboutLanguages.title) LIKE'] = '%' . trim(strtolower($data_search['title'])) . '%';
        }
        $this->paginate = [
            'fields' => [
                'Abouts.id',
                'Abouts.enabled',
                'Abouts.created',
                'Abouts.modified',
                'AboutLanguages.content',
            ],
            'conditions' => $_conditions,
            'order'     => [
                'Abouts.id DESC'
            ],
            'join'  => [
                'table' => 'about_languages',
                'alias' => 'AboutLanguages',
                'type' => 'INNER',
                'conditions' => [
                    'AboutLanguages.about_id = Abouts.id',
                    'AboutLanguages.alias' => $this->lang18,
                ],
            ]
        ];

        $abouts = $this->paginate($this->Abouts, [
            'limit' => Configure::read('web.limit')
        ]); 
        $this->set(compact('abouts', 'data_search'));
    }

    /**
     * View method
     *
     * @param string|null $id About id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $about = $this->Abouts->get($id, [
            'contain' => [
                'CreatedBy',
                'ModifiedBy',
                'AboutLanguages'
            ],
        ]);
        $language_input_fields = array(
            'title',
            'content'
        );
        $languages = $about->about_languages;
        $this->set(compact('about', 'language_input_fields', 'languages'));
    }

    public function load_language()
    {
        $language_input_fields = array(
            'id',
            'alias',
            'content',
        );

        $obj_Language = TableRegistry::get('Languages');
        $languages_list = $obj_Language->get_languages();

        $languages_model = 'AboutLanguages';
        $languages_edit_model = 'about_languages';

        $this->set(compact('language_input_fields', 'languages_list', 'languages_model', 'languages_edit_model'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function _add()
    {
        $about = $this->Abouts->newEmptyEntity();
        if ($this->request->is('post')) {
            $about = $this->Abouts->patchEntity($about, $this->request->getData());
            $db = $this->Abouts->getConnection();
            $db->begin();

            $about_languages = $this->Abouts->AboutLanguages->newEntities($this->request->getData()['AboutLanguages']);

            if ($model = $this->Abouts->save($about)) {
                if (isset($about_languages) && !empty($about_languages)) {
                    foreach ($about_languages as $language) {
                        $language['about_id'] = $model->id;
                    }
                    if (!$this->Abouts->AboutLanguages->saveMany($about_languages)) {
                        $db->rollback();
                        $this->Flash->error(__('data_is_not_saved'));
                        goto load_data;
                    }
                }
                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            }
            $db->rollback();
            $this->Flash->error(__('The about could not be saved. Please, try again.'));
        }
        load_data:
        $current_language = $this->lang18;
        $this->load_language();
        $this->set(compact('about'));
    }

    /**
     * Edit method
     *
     * @param string|null $id About id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $about = $this->Abouts->get($id, [
            'contain' => [
                'AboutLanguages'
            ],
        ]);
        $languages_edit_data = (isset($about['about_languages']) && !empty($about['about_languages'])) ? $about['about_languages'] : false;
        if ($this->request->is(['patch', 'post', 'put'])) {
            $about = $this->Abouts->patchEntity($about, $this->request->getData());
            $db = $this->Abouts->getConnection();
            $db->begin();
            if ($this->Abouts->save($about)) {

                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The policy could not be saved. Please, try again.'));
            }
        }
        load_data:
        $this->load_language();
        $current_language = $this->lang18;
        $this->set(compact('about', 'current_language', 'languages_edit_data'));
    }

    /**
     * Delete method
     *
     * @param string|null $id About id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function _delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $about = $this->Abouts->get($id, [
            'contain' => ['AboutLanguages']
        ]);
        if ($this->Abouts->delete($about)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The about could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

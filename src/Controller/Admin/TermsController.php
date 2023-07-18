<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Cake\ORM\TableRegistry;
use App\Controller\Admin\AppController;
use Cake\Core\Configure;

/**
 * Terms Controller
 *
 * @method \App\Model\Entity\Term[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TermsController extends AppController
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
            $_conditions['Terms.enabled'] = intval($data_search['status']);
        }

        if (isset($data_search['title']) && !empty($data_search['title'])) {
            $_conditions['LOWER(TermLanguages.title) LIKE'] = '%' . trim(strtolower($data_search['title'])) . '%';
        }
        $this->paginate = [
            'fields' => [
                'Terms.id',
                'Terms.enabled',
                'Terms.created',
                'Terms.modified',
                'TermLanguages.title',
            ],
            'conditions' => $_conditions,
            'order'     => [
                'Terms.id DESC'
            ],
            'join'  => [
                'table' => 'term_languages',
                'alias' => 'TermLanguages',
                'type' => 'INNER',
                'conditions' => [
                    'TermLanguages.term_id = Terms.id',
                    'TermLanguages.alias' => $this->lang18,
                ],
            ]
        ];

        $terms = $this->paginate($this->Terms, [
            'limit' => Configure::read('web.limit')
        ]); 
        $this->set(compact('terms', 'data_search'));
    }

    /**
     * View method
     *
     * @param string|null $id Term id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $term = $this->Terms->get($id, [
            'contain' => [
                'CreatedBy',
                'ModifiedBy',
                'TermLanguages'
            ],
        ]);
        $language_input_fields = array(
            'title',
            'content'
        );
        $languages = $term->term_languages;
        $this->set(compact('term', 'language_input_fields', 'languages'));
    }
    public function load_language()
    {
        $language_input_fields = array(
            'id',
            'alias',
            'title',
            'content',
        );

        $obj_Language = TableRegistry::get('Languages');
        $languages_list = $obj_Language->get_languages();

        $languages_model = 'PrivatePolicyLanguages';
        $languages_edit_model = 'term_languages';

        $this->set(compact('language_input_fields', 'languages_list', 'languages_model', 'languages_edit_model'));
    }
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function _add()
    {
        $term = $this->Terms->newEmptyEntity();
        if ($this->request->is('post')) {
            $term = $this->Terms->patchEntity($term, $this->request->getData());
            $db = $this->Terms->getConnection();
            $db->begin();

            $term_languages = $this->Terms->TermLanguages->newEntities($this->request->getData()['PrivatePolicyLanguages']);

            if ($model = $this->Terms->save($term)) {
                if (isset($term_languages) && !empty($term_languages)) {
                    foreach ($term_languages as $language) {
                        $language['term_id'] = $model->id;
                    }
                    if (!$this->Terms->TermLanguages->saveMany($term_languages)) {
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
            $this->Flash->error(__('The privacy policy could not be saved. Please, try again.'));
        }
        load_data:
        $current_language = $this->lang18;
        $this->load_language();
        $this->set(compact('term'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Term id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $term = $this->Terms->get($id, [
            'contain' => [
                'TermLanguages'
            ],
        ]);
        $languages_edit_data = (isset($term['term_languages']) && !empty($term['term_languages'])) ? $term['term_languages'] : false;
        if ($this->request->is(['patch', 'post', 'put'])) {
            $term = $this->Terms->patchEntity($term, $this->request->getData());
            $db = $this->Terms->getConnection();
            $db->begin();
            if ($this->Terms->save($term)) {

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
        $this->set(compact('term', 'current_language', 'languages_edit_data'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Term id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $term = $this->Terms->get($id, ['contain' => ['TermLanguages']]);
        if ($this->Terms->delete($term)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The term could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

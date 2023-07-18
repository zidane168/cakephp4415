<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

/**
 * Feedbacks Controller
 *
 * @method \App\Model\Entity\Feedback[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class FeedbacksController extends AppController
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

        if(isset($data_search['status']) && $data_search['status'] != "") {
            $_conditions['Feedbacks.enabled'] = intval($data_search['status']);
        }

        if(isset($data_search['name']) && !empty($data_search['name'])) {
            $_conditions['LOWER(FeedbackLanguages.name) LIKE'] = '%' . trim(strtolower($data_search['name'])) . '%';
        }
        $this->paginate = array(
            'fields' => [
                'Feedbacks.id',
                'Feedbacks.enabled',
                'Feedbacks.created',
                'Feedbacks.modified',
                'FeedbackLanguages.name'
            ],
            'conditions' => $_conditions,
            'order' => [
                'Feedbacks.id DESC'
            ],
            'join' => [
                'table' => 'feedback_languages',
                'alias' => 'FeedbackLanguages',
                'type' => 'INNER',
                'conditions' => [
                    'FeedbackLanguages.feedback_id = Feedbacks.id',
                    'FeedbackLanguages.alias' => $this->lang18, 
                ],
            ]
        );
        $feedbacks = $this->paginate($this->Feedbacks, array(
            'limit' => Configure::read('web.limit')
        ));

        $this->set(compact('feedbacks', 'data_search'));
    }

    /**
     * View method
     *
     * @param string|null $id Feedback id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $feedback = $this->Feedbacks->get($id, [
            'contain' => [
                'CreatedBy', 'ModifiedBy',
                'FeedbackLanguages'
            ],
        ]);
        $language_input_fields = array(
            'name'
        );
        $languages = $feedback->feedback_languages;
        $this->set(compact('feedback', 'language_input_fields', 'languages'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $model = 'Feedbacks';

        $feedback = $this->Feedbacks->newEmptyEntity();
        if ($this->request->is('post')) {
            
            $db = $this->Feedbacks->getConnection();
            $db->begin();
            
            $feedback = $this->Feedbacks->patchEntity($feedback, $this->request->getData());
            $feedback_language = $this->Feedbacks->FeedbackLanguages->newEntities($this->request->getData()['FeedbackLanguages']);
            if ($model = $this->Feedbacks->save($feedback)) {
                
                // 2, save language
                if(isset($feedback_language) && !empty($feedback_language)) {
                    foreach ($feedback_language as $language) {
                        $language['feedback_id'] = $model->id;
                    }
                    if(!$this->Feedbacks->FeedbackLanguages->saveMany($feedback_language)) {
                        // debug(222);exit;
                        $this->Flash->error(__('data_is_not_saved'));
                        goto load_data;
                    }
                }

                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The feedback could not be saved. Please, try again.'));
            }
        }

        load_data:
        $current_language = $this->lang18;
        $this->load_language();
        $this->set(compact('feedback', 'current_language'));
    }

    public function load_language() {
        $language_input_fields = array(
            'id',
            'alias',
            'name'
        );

        $obj_Language = TableRegistry::get('Languages');
        $languages_list = $obj_Language->get_languages();

        $languages_model = 'FeedbackLanguages';
        $languages_edit_model = 'feedback_languages';

        $this->set(compact('language_input_fields', 'languages_list', 'languages_model', 'languages_edit_model'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Feedback id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $feedback = $this->Feedbacks->get($id, [
            'contain' => [
                'FeedbackLanguages',
            ],
        ]);
        $languages_edit_data = (isset($feedback['feedback_languages']) && !empty($feedback['feedback_languages'])) ? $feedback['feedback_languages'] : false;        
       
        // add row for the replace this->request->date_add
        if ($this->request->is(['patch', 'post', 'put'])) { 

            $db  = $this->Feedbacks->getConnection();
            $db->begin();

            $feedback = $this->Feedbacks->patchEntity($feedback, $this->request->getData());
            
            if ($this->Feedbacks->save($feedback)) {
                
                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The feedback could not be saved. Please, try again.'));
            }
        }

        load_data:
        $this->load_language();
        $current_language = $this->lang18;
        $this->set(compact('feedback', 'current_language', 'languages_edit_data'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Feedback id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $feedback = $this->Feedbacks->get($id);
        if ($this->Feedbacks->delete($feedback)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The feedback could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

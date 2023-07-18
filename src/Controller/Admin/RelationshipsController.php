<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
/**
 * Relationships Controller
 *
 * @method \App\Model\Entity\Relationship[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class RelationshipsController extends AppController
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
            $_conditions['Relationships.enabled'] = intval($data_search['status']);
        }

        if(isset($data_search['name']) && !empty($data_search['name'])) {
            $_conditions['LOWER(RelationshipLanguages.name) LIKE'] = '%' . trim(strtolower($data_search['name'])) . '%';
        }
        $this->paginate = array(
            'fields' => [
                'Relationships.id',
                'Relationships.enabled',
                'Relationships.created',
                'Relationships.modified',
                'RelationshipLanguages.name'
            ],
            'conditions' => $_conditions,
            'order' => [
                'Relationships.id DESC'
            ],
            'join' => [
                'table' => 'relationship_languages',
                'alias' => 'RelationshipLanguages',
                'type' => 'INNER',
                'conditions' => [
                    'RelationshipLanguages.relationship_id = Relationships.id',
                    'RelationshipLanguages.alias' => $this->lang18, 
                ],
            ]
        );
        $relationships = $this->paginate($this->Relationships, array(
            'limit' => Configure::read('web.limit')
        ));

        $this->set(compact('relationships', 'data_search'));
    }

    /**
     * View method
     *
     * @param string|null $id Relationship id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $relationship = $this->Relationships->get($id, [
            'contain' => [
                'CreatedBy', 'ModifiedBy',
                'RelationshipLanguages'
            ],
        ]);
        $language_input_fields = array(
            'name'
        );
        $languages = $relationship->relationship_languages;
        $this->set(compact('relationship', 'language_input_fields', 'languages'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $model = 'Relationships';

        $relationship = $this->Relationships->newEmptyEntity();
        if ($this->request->is('post')) {
            
            $db = $this->Relationships->getConnection();
            $db->begin();
            
            $relationship = $this->Relationships->patchEntity($relationship, $this->request->getData());
            $relationship_language = $this->Relationships->RelationshipLanguages->newEntities($this->request->getData()['RelationshipLanguages']);
            if ($model = $this->Relationships->save($relationship)) {

                // 2, save language
                if(isset($relationship_language) && !empty($relationship_language)) {
                    foreach ($relationship_language as $language) {
                        $language['relationship_id'] = $model->id;
                    }
                    if(!$this->Relationships->RelationshipLanguages->saveMany($relationship_language)) {
                        $this->Flash->error(__('data_is_not_saved'));
                        goto load_data;
                    }
                }

                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The relationship could not be saved. Please, try again.'));
            }
        }

        load_data:
        $current_language = $this->lang18;
        $this->load_language();
        $this->set(compact('relationship', 'current_language'));
    }

    public function load_language() {
        $language_input_fields = array(
            'id',
            'alias',
            'name'
        );

        $obj_Language = TableRegistry::get('Languages');
        $languages_list = $obj_Language->get_languages();

        $languages_model = 'RelationshipLanguages';
        $languages_edit_model = 'relationship_languages';

        $this->set(compact('language_input_fields', 'languages_list', 'languages_model', 'languages_edit_model'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Relationship id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $relationship = $this->Relationships->get($id, [
            'contain' => [
                'RelationshipLanguages',
            ],
        ]);
        $languages_edit_data = (isset($relationship['relationship_languages']) && !empty($relationship['relationship_languages'])) ? $relationship['relationship_languages'] : false;        
       
        // add row for the replace this->request->date_add
        if ($this->request->is(['patch', 'post', 'put'])) { 

            $db  = $this->Relationships->getConnection();
            $db->begin();

            $relationship = $this->Relationships->patchEntity($relationship, $this->request->getData());
            
            if ($this->Relationships->save($relationship)) {
                
                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The relationship could not be saved. Please, try again.'));
            }
        }

        load_data:
        $this->load_language();
        $current_language = $this->lang18;
        $this->set(compact('relationship', 'current_language', 'languages_edit_data'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Relationship id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $relationship = $this->Relationships->get($id);
        if ($this->Relationships->delete($relationship)) {
            $this->Flash->success(__('The relationship has been deleted.'));
        } else {
            $this->Flash->error(__('The relationship could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

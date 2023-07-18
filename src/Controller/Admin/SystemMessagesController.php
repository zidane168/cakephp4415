<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Core\Configure;

/**
 * SystemMessages Controller
 *
 * @property \App\Model\Table\SystemMessagesTable $SystemMessages
 * @method \App\Model\Entity\SystemMessage[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SystemMessagesController extends AppController
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

        $session = $this->request->getSession(); 
        $list_centers_management = $session->read('administrator.list_centers_management'); 
  
        if ($list_centers_management && count($list_centers_management) > 0) {
            $cidcClasses = $this->SystemMessages->CidcClasses->get_list_id_belong_center($this->lang18, $list_centers_management);
 
            if ($cidcClasses) {
                $_conditions['SystemMessages.cidc_class_id IN'] = $cidcClasses;
            } 
        }

        if (isset($data_search['status']) && $data_search['status'] != "") {
            $_conditions['SystemMessages.enabled'] = intval($data_search['status']);
        }
        if (isset($data_search['cidc_parent_id']) && $data_search['cidc_parent_id'] != "") {
            $_conditions['SystemMessages.cidc_parent_id'] = intval($data_search['cidc_parent_id']);
        }

        if (isset($data_search['kid_id']) && $data_search['kid_id'] != "") {
            $_conditions['SystemMessages.kid_id'] = intval($data_search['kid_id']);
        }

        if (isset($data_search['cidc_class_id']) && $data_search['cidc_class_id'] != "") {
            $_conditions['SystemMessages.cidc_class_id'] = intval($data_search['cidc_class_id']);
        }
   
        $this->paginate = [
            'fields' => [
                'SystemMessages.id', 
                'SystemMessages.cidc_parent_id',
                'SystemMessages.kid_id',
                'SystemMessages.read_time',
                'SystemMessages.cidc_class_id',
                'SystemMessages.enabled',
                'SystemMessages.created',
                'SystemMessages.modified',
            ],
            'conditions' => $_conditions, 
            'order' => [
                'SystemMessages.id DESC'
            ],
        ]; 

        $systemMessages = $this->paginate($this->SystemMessages, [
            'limit' => Configure::read('web.limit')
        ]); 

        $current_language = $this->lang18;
        $cidcClasses = $this->SystemMessages->CidcClasses->get_list_belong_center($this->lang18, $list_centers_management);
        
        //$cidcClasses = $this->SystemMessages->CidcClasses->get_list($current_language)->toArray();
        $cidcParents = $this->SystemMessages->CidcParents->get_list($current_language)->toArray();
        $kids = $this->SystemMessages->Kids->get_list($current_language)->toArray();
        $this->set(compact('systemMessages', 'cidcParents', 'kids', 'cidcClasses', 'data_search'));
    }

    /**
     * View method
     *
     * @param string|null $id System Message id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $systemMessage = $this->SystemMessages->get($id, [
            'contain' => ['CreatedBy', 'ModifiedBy', 'SystemMessageLanguages'],
        ]);
        $current_language = $this->lang18;
        $cidcClasses = $this->SystemMessages->CidcClasses->get_list($current_language)->toArray();
        $cidcParents = $this->SystemMessages->CidcParents->get_list($current_language)->toArray();
        $kids = $this->SystemMessages->Kids->get_list($current_language)->toArray();
        $language_input_fields = array(
            'title',
            'message'
        );
        $languages = $systemMessage->system_message_languages;
        $this->set(compact('systemMessage', 'cidcClasses', 'cidcParents', 'kids', 'language_input_fields', 'languages'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function _add()
    {
        $systemMessage = $this->SystemMessages->newEmptyEntity();
        if ($this->request->is('post')) {
            $systemMessage = $this->SystemMessages->patchEntity($systemMessage, $this->request->getData());
            if ($this->SystemMessages->save($systemMessage)) {
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The system message could not be saved. Please, try again.'));
        }
        $cidcClasses = $this->SystemMessages->CidcClasses->find('list', ['limit' => 200]);
        $parentSystemMessages = $this->SystemMessages->ParentSystemMessages->find('list', ['limit' => 200]);
        $kids = $this->SystemMessages->Kids->find('list', ['limit' => 200]);
        $createdBy = $this->SystemMessages->CreatedBy->find('list', ['limit' => 200]);
        $modifiedBy = $this->SystemMessages->ModifiedBy->find('list', ['limit' => 200]);
        $this->set(compact('systemMessage', 'cidcClasses', 'parentSystemMessages', 'kids', 'createdBy', 'modifiedBy'));
    }

    /**
     * Edit method
     *
     * @param string|null $id System Message id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function _edit($id = null)
    {
        $systemMessage = $this->SystemMessages->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $systemMessage = $this->SystemMessages->patchEntity($systemMessage, $this->request->getData());
            if ($this->SystemMessages->save($systemMessage)) {
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The system message could not be saved. Please, try again.'));
        }
        $cidcClasses = $this->SystemMessages->CidcClasses->find('list', ['limit' => 200]);
        $parentSystemMessages = $this->SystemMessages->ParentSystemMessages->find('list', ['limit' => 200]);
        $kids = $this->SystemMessages->Kids->find('list', ['limit' => 200]);
        $createdBy = $this->SystemMessages->CreatedBy->find('list', ['limit' => 200]);
        $modifiedBy = $this->SystemMessages->ModifiedBy->find('list', ['limit' => 200]);
        $this->set(compact('systemMessage', 'cidcClasses', 'parentSystemMessages', 'kids', 'createdBy', 'modifiedBy'));
    }

    /**
     * Delete method
     *
     * @param string|null $id System Message id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $systemMessage = $this->SystemMessages->get($id);
        if ($this->SystemMessages->delete($systemMessage)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The system message could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

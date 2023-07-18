<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Core\Configure;

/**
 * UserVerifications Controller
 *
 * @property \App\Model\Table\UserVerificationsTable $UserVerifications
 * @method \App\Model\Entity\UserVerification[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UserVerificationsController extends AppController
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
            $_conditions['Kids.enabled'] = intval($data_search['status']);
        }

        if (isset($data_search['phone_number']) && !empty($data_search['phone_number'])) {
            $_conditions['LOWER(UserVerifications.phone_number) LIKE'] = '%' . trim(strtolower($data_search['phone_number'])) . '%';
        }

        $this->paginate = [
            'conditions' => $_conditions,
            'order' => ['UserVerifications.id DESC'],
            'contain' => ['CreatedBy', 'ModifiedBy'],
        ];
        $userVerifications = $this->paginate($this->UserVerifications, [
            'limit' => Configure::read('web.limit')
        ]);
        $verification_types     = $this->UserVerifications->verification_types;
        $verification_methods   = $this->UserVerifications->verification_methods; 

        $this->set(compact('userVerifications', 'verification_types', 'verification_methods', 'data_search'));
    }

    /**
     * View method
     *
     * @param string|null $id User Verification id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $userVerification = $this->UserVerifications->get($id, [
            'contain' => ['CreatedBy', 'ModifiedBy'],
        ]);
        $verification_types     = $this->UserVerifications->verification_types;
        $verification_methods   = $this->UserVerifications->verification_methods; 
        
        $this->set(compact('userVerification', 'verification_types', 'verification_methods'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $userVerification = $this->UserVerifications->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['verification_type']    = $data['verification_type_id'];
            $data['verification_method']  = $data['verification_method_id'];
            $userVerification = $this->UserVerifications->patchEntity($userVerification, $data);
            if ($this->UserVerifications->save($userVerification)) {
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user verification could not be saved. Please, try again.'));
        }
        $verification_types     = $this->UserVerifications->get_verification_types();
        $verification_methods   = $this->UserVerifications->get_verification_methods();
        $this->set(compact('userVerification', 'verification_types', 'verification_methods'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User Verification id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $userVerification = $this->UserVerifications->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $userVerification = $this->UserVerifications->patchEntity($userVerification, $this->request->getData());
            if ($this->UserVerifications->save($userVerification)) {
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user verification could not be saved. Please, try again.'));
        }
        $verification_types     = $this->UserVerifications->get_verification_types();
        $verification_methods   = $this->UserVerifications->get_verification_methods();
        $this->set(compact('userVerification', 'verification_types', 'verification_methods'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User Verification id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $userVerification = $this->UserVerifications->get($id);
        if ($this->UserVerifications->delete($userVerification)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The user verification could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

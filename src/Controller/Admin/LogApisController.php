<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;

/**
 * LogApis Controller
 *
 * @property \App\Model\Table\LogApisTable $LogApis
 * @method \App\Model\Entity\LogApi[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class LogApisController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [];
        $logApis = $this->paginate($this->LogApis);

        $this->set(compact('logApis'));
    }

    /**
     * View method
     *
     * @param string|null $id Log Api id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $logApi = $this->LogApis->get($id);
        $this->set(compact('logApi'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    // public function add()
    // {
    //     $logApi = $this->LogApis->newEmptyEntity();
    //     if ($this->request->is('post')) {
    //         $logApi = $this->LogApis->patchEntity($logApi, $this->request->getData());
    //         if ($this->LogApis->save($logApi)) {
    //             $this->Flash->success(__('The log api has been saved.'));

    //             return $this->redirect(['action' => 'index']);
    //         }
    //         $this->Flash->error(__('The log api could not be saved. Please, try again.'));
    //     }
    //     $companies = $this->LogApis->Companies->find('list', ['limit' => 200]);
    //     $members = $this->LogApis->Members->find('list', ['limit' => 200]);
    //     $this->set(compact('logApi', 'companies', 'members'));
    // }

    // /**
    //  * Edit method
    //  *
    //  * @param string|null $id Log Api id.
    //  * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
    //  * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
    //  */
    // public function edit($id = null)
    // {
    //     $logApi = $this->LogApis->get($id, [
    //         'contain' => [],
    //     ]);
    //     if ($this->request->is(['patch', 'post', 'put'])) {
    //         $logApi = $this->LogApis->patchEntity($logApi, $this->request->getData());
    //         if ($this->LogApis->save($logApi)) {
    //             $this->Flash->success(__('The log api has been saved.'));

    //             return $this->redirect(['action' => 'index']);
    //         }
    //         $this->Flash->error(__('The log api could not be saved. Please, try again.'));
    //     }
    //     $companies = $this->LogApis->Companies->find('list', ['limit' => 200]);
    //     $members = $this->LogApis->Members->find('list', ['limit' => 200]);
    //     $this->set(compact('logApi', 'companies', 'members'));
    // }

    // /**
    //  * Delete method
    //  *
    //  * @param string|null $id Log Api id.
    //  * @return \Cake\Http\Response|null|void Redirects to index.
    //  * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
    //  */
    // public function delete($id = null)
    // {
    //     $this->request->allowMethod(['post', 'delete']);
    //     $logApi = $this->LogApis->get($id);
    //     if ($this->LogApis->delete($logApi)) {
    //         $this->Flash->success(__('The log api has been deleted.'));
    //     } else {
    //         $this->Flash->error(__('The log api could not be deleted. Please, try again.'));
    //     }

    //     return $this->redirect(['action' => 'index']);
    // }
}

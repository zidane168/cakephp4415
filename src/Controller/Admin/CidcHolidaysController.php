<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Core\Configure;


/**
 * CidcHolidays Controller
 *
 * @property \App\Model\Table\CidcHolidaysTable $CidcHolidays
 * @method \App\Model\Entity\CidcHoliday[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CidcHolidaysController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $data_search = $this->request->getQuery();
        $_conditions =  array(
            'YEAR(CidcHolidays.date)' => date('Y'),
        ); 
        $option_years = [
            1 => date('Y'),
            2 => strval(date('Y') + 1)
        ]; 

        if (isset($data_search['status']) && $data_search['status'] != "") {
            $_conditions['CidcHolidays.enabled'] = intval($data_search['status']);
        }

        if (isset($data_search['description']) && !empty($data_search['description'])) {
            $_conditions['LOWER(CidcHolidays.description) LIKE'] = '%' . trim(strtolower($data_search['description'])) . '%';
        }

        if (isset($data_search['year']) && !empty($data_search['year'])) {
            $_conditions['DATE_FORMAT(CidcHolidays.date, "%Y") = '] = $option_years[$data_search['year']];
        }
        $this->paginate = [
            'fields' => [
                'CidcHolidays.id',
                'CidcHolidays.date',
                'CidcHolidays.description',
                'CidcHolidays.enabled',
                'CidcHolidays.created',
                'CidcHolidays.modified',
            ],
            'conditions' => $_conditions, 
        ];
        $cidcHolidays = $this->paginate($this->CidcHolidays, [
            'limit' => Configure::read('web.limit')
        ]); 
        $this->set(compact('cidcHolidays', 'data_search', 'option_years'));
    }

    /**
     * View method
     *
     * @param string|null $id Cidc Holiday id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $cidcHoliday = $this->CidcHolidays->get($id, [
            'contain' => ['CreatedBy', 'ModifiedBy'],
        ]);

        $this->set(compact('cidcHoliday'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $cidcHoliday = $this->CidcHolidays->newEmptyEntity();

        if ($this->request->is('post')) {
            $cidcHoliday = $this->CidcHolidays->patchEntity($cidcHoliday, $this->request->getData());
            $cidcHoliday->date = $this->request->getData()['date'];
            if ($this->CidcHolidays->save($cidcHoliday)) {
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The cidc holiday could not be saved. Please, try again.'));
        }

        $this->set(compact('cidcHoliday'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Cidc Holiday id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $cidcHoliday = $this->CidcHolidays->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $cidcHoliday = $this->CidcHolidays->patchEntity($cidcHoliday, $this->request->getData());
            if ($this->CidcHolidays->save($cidcHoliday)) {
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The cidc holiday could not be saved. Please, try again.'));
        }
        $createdBy = $this->CidcHolidays->CreatedBy->find('list', ['limit' => 200]);
        $modifiedBy = $this->CidcHolidays->ModifiedBy->find('list', ['limit' => 200]);
        $this->set(compact('cidcHoliday', 'createdBy', 'modifiedBy'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Cidc Holiday id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cidcHoliday = $this->CidcHolidays->get($id);
        if ($this->CidcHolidays->delete($cidcHoliday)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The cidc holiday could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

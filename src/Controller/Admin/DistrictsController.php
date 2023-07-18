<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

/**
 * Districts Controller
 *
 * @property \App\Model\Table\DistrictsTable $Districts
 * @method \App\Model\Entity\District[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DistrictsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $data_search = $this->request->getQuery();
        $_conditions = $__conditions = array();

        if (isset($data_search['status']) && $data_search['status'] != "") {
            $_conditions['Districts.enabled'] = intval($data_search['status']);
        }


        if (isset($data_search['name']) && !empty($data_search['name'])) {
            $__conditions['LOWER(DistrictLanguages.name) LIKE'] = '%' . trim(strtolower($data_search['name'])) . '%';
        }

        $conditions = array(
            'DistrictLanguages.district_id = Districts.id',
            'DistrictLanguages.alias = \'' . $this->lang18  . '\''
        );

        $this->paginate = array(
            'fields' => [
                'Districts.id',
                'Districts.enabled',
                'Districts.created',
                'Districts.modified',
                'DistrictLanguages.name',
            ],
            'conditions' => $_conditions,
            'order' => [
                'Districts.id DESC'
            ],
            'contain' => [
                'CreatedBy',
                'ModifiedBy',
            ],
            'join' => [
                'table' => 'district_languages',
                'alias' => 'DistrictLanguages',
                'type'  => 'INNER',
                'conditions' => array_merge($conditions, $__conditions),
            ]
        );


        $districts = $this->paginate($this->Districts, array(
            'limit' => Configure::read('web.limit')
        ));

        // combo box
        $this->set(compact('districts', 'data_search'));
    }

    /**
     * View method
     *
     * @param string|null $id District id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $district = $this->Districts->get($id, [
            'contain' => [
                'CreatedBy', 'ModifiedBy', 'DistrictLanguages'
            ],
        ]);

        // languages
        $language_input_fields = array(
            'name',
            'description',
        );
        $languages = $district->district_languages;

        $this->set(compact('district', 'language_input_fields', 'languages'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $model = 'Districts';

        $district = $this->Districts->newEmptyEntity();

        if ($this->request->is('post')) {

            $db = $this->Districts->getConnection();
            $db->begin();

            $district = $this->Districts->patchEntity($district, $this->request->getData());
            $district_language = $this->Districts->DistrictLanguages->newEntities($this->request->getData()['DistrictLanguages']);

            if ($model = $this->Districts->save($district)) {

                // 2,save language
                if (isset($district_language) && !empty($district_language)) {
                    foreach ($district_language as &$language) {
                        $language['district_id'] = $model->id;
                    }

                    if (!$this->Districts->DistrictLanguages->saveMany($district_language)) {
                        $this->Flash->error(__('data_is_not_saved'));
                        goto load_data;
                    }
                }

                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__('The district could not be saved. Please, try again.'));
        }

        load_data:

        $current_language = $this->lang18;
        $this->load_language();
        $this->set(compact('district', 'current_language'));
    }

    public function load_language()
    {
        $language_input_fields = array(
            'id',
            'alias',
            'name',
        );

        $obj_Language = TableRegistry::get('Languages');
        $languages_list = $obj_Language->get_languages();

        $languages_model        = 'DistrictLanguages';
        $languages_edit_model   = 'district_languages';

        $this->set(compact('language_input_fields', 'languages_list', 'languages_model', 'languages_edit_model'));
    }

    /**
     * Edit method
     *
     * @param string|null $id District id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $model = 'Districts';

        $district = $this->Districts->get($id, [
            'contain' => [
                'DistrictLanguages',
            ],
        ]);
        // add this row for replace $this->request->data (cakephp 2)
        $languages_edit_data   = isset($district['district_languages']) && !empty($district['district_languages']) ? $district['district_languages'] : false;

        if ($this->request->is(['patch', 'post', 'put'])) {

            $db = $this->Districts->getConnection();
            $db->begin();

            $district = $this->Districts->patchEntity($district, $this->request->getData());

            if ($this->Districts->save($district)) {

                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The district could not be saved. Please, try again.'));
            }
        }

        load_data:
        $this->load_language();

        $current_language = $this->lang18;
        $this->set(compact('district', 'current_language', 'languages_edit_data'));
    }

    /**
     * Delete method
     *
     * @param string|null $id District id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $district = $this->Districts->get($id);
        if ($this->Districts->delete($district)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The district could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

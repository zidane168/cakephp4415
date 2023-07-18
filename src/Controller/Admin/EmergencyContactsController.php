<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

/**
 * EmergencyContacts Controller
 *
 * @method \App\Model\Entity\EmergencyContact[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class EmergencyContactsController extends AppController
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
            $_conditions['EmergencyContacts.enabled'] = intval($data_search['status']);
        }

        if(isset($data_search['name']) && !empty($data_search['name'])) {
            $_conditions['LOWER(EmergencyContactLanguages.name) LIKE'] = '%' . trim(strtolower($data_search['name'])) . '%';
        }
        $this->paginate = array(
            'fields' => [
                'EmergencyContacts.id',
                'EmergencyContacts.phone_number',
                'EmergencyContacts.enabled',
                'EmergencyContacts.created',
                'EmergencyContacts.modified',
                'EmergencyContactLanguages.name'
            ],
            'conditions' => $_conditions,
            'order' => [
                'EmergencyContacts.id DESC'
            ],
            'join' => [
                'table' => 'emergency_contact_languages',
                'alias' => 'EmergencyContactLanguages',
                'type' => 'INNER',
                'conditions' => [
                    'EmergencyContactLanguages.emergency_contact_id = EmergencyContacts.id',
                    'EmergencyContactLanguages.alias' => $this->lang18, 
                ],
            ]
        );
        $emergencyContacts = $this->paginate($this->EmergencyContacts, array(
            'limit' => Configure::read('web.limit')
        ));

        $this->set(compact('emergencyContacts', 'data_search'));
    }

    /**
     * View method
     *
     * @param string|null $id Emergency Contact id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $emergencyContact = $this->EmergencyContacts->get($id, [
            'contain' => [
                'CreatedBy', 'ModifiedBy',
                'EmergencyContactLanguages'
            ],
        ]);
        $language_input_fields = array(
            'name'
        );
        $languages = $emergencyContact->emergency_contact_languages;
        $this->set(compact('emergencyContact', 'language_input_fields', 'languages'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $model = 'EmergencyContacts';

        $emergencyContact = $this->EmergencyContacts->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $db = $this->EmergencyContacts->getConnection();
            $db->begin();
            
            $emergencyContact = $this->EmergencyContacts->patchEntity($emergencyContact, $data);
            $emergency_contact_language = $this->EmergencyContacts->EmergencyContactLanguages->newEntities($this->request->getData()['EmergencyContactLanguages']);
            if ($model = $this->EmergencyContacts->save($emergencyContact)) {

                // 2, save language
                if(isset($emergency_contact_language) && !empty($emergency_contact_language)) {
                    foreach ($emergency_contact_language as $language) {
                        $language['emergency_contact_id'] = $model->id;
                    }
                    // debug($emergency_contact_language);exit;
                    if(!$this->EmergencyContacts->EmergencyContactLanguages->saveMany($emergency_contact_language)) {
                        $this->Flash->error(__('data_is_not_saved'));
                        goto load_data;
                    }
                }

                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The emergencyContact could not be saved. Please, try again.'));
            }
        }

        load_data:
        $current_language = $this->lang18;
        $this->load_language();
        $this->set(compact('emergencyContact', 'current_language'));
    }

    public function load_language() {
        $language_input_fields = array(
            'id',
            'alias',
            'name'
        );

        $obj_Language = TableRegistry::get('Languages');
        $languages_list = $obj_Language->get_languages();

        $languages_model = 'EmergencyContactLanguages';
        $languages_edit_model = 'emergency_contact_languages';

        $this->set(compact('language_input_fields', 'languages_list', 'languages_model', 'languages_edit_model'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Emergency Contact id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $emergencyContact = $this->EmergencyContacts->get($id, [
            'contain' => [
                'EmergencyContactLanguages',
            ],
        ]);
        $languages_edit_data = (isset($emergencyContact['emergency_contact_languages']) && !empty($emergencyContact['emergency_contact_languages'])) ? $emergencyContact['emergency_contact_languages'] : false;        
       
        // add row for the replace this->request->date_add
        if ($this->request->is(['patch', 'post', 'put'])) { 

            $data = $this->request->getData();

            $db  = $this->EmergencyContacts->getConnection();
            $db->begin();

            $emergencyContact = $this->EmergencyContacts->patchEntity($emergencyContact, $data);
            
            if ($this->EmergencyContacts->save($emergencyContact)) {
                
                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The emergencyContact could not be saved. Please, try again.'));
            }
        }

        load_data:
        $this->load_language();
        $current_language = $this->lang18;
        $this->set(compact('emergencyContact', 'current_language', 'languages_edit_data'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Emergency Contact id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $emergencyContact = $this->EmergencyContacts->get($id);
        if ($this->EmergencyContacts->delete($emergencyContact)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The emergency contact could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

/**
 * ClassTypes Controller
 *
 * @method \App\Model\Entity\ClassType[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ClassTypesController extends AppController
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
            $_conditions['ClassTypes.enabled'] = intval($data_search['status']);
        }

        if(isset($data_search['name']) && !empty($data_search['name'])) {
            $_conditions['LOWER(ClassTypeLanguages.name) LIKE'] = '%' . trim(strtolower($data_search['name'])) . '%';
        }
        $this->paginate = array(
            'fields' => [
                'ClassTypes.id',
                'ClassTypes.enabled',
                'ClassTypes.created',
                'ClassTypes.modified',
                'ClassTypeLanguages.name'
            ],
            'conditions' => $_conditions,
            'order' => [
                'ClassTypes.id DESC'
            ],
            'join' => [
                'table' => 'class_type_languages',
                'alias' => 'ClassTypeLanguages',
                'type' => 'INNER',
                'conditions' => [
                    'ClassTypeLanguages.class_type_id = ClassTypes.id',
                    'ClassTypeLanguages.alias' => $this->lang18, 
                ],
            ]
        );
        $classTypes = $this->paginate($this->ClassTypes, array(
            'limit' => Configure::read('web.limit')
        ));

        $this->set(compact('classTypes', 'data_search'));
    }

    /**
     * View method
     *
     * @param string|null $id Class Type id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $classType = $this->ClassTypes->get($id, [
            'contain' => [
                'CreatedBy', 'ModifiedBy',
                'ClassTypeLanguages'
            ],
        ]);
        $language_input_fields = array(
            'name'
        );
        $languages = $classType->class_type_languages;
        $this->set(compact('classType', 'language_input_fields', 'languages'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function _add()
    {
        $model = 'ClassTypes';

        $classType = $this->ClassTypes->newEmptyEntity();
        if ($this->request->is('post')) {
            
            $db = $this->ClassTypes->getConnection();
            $db->begin();
            
            $classType = $this->ClassTypes->patchEntity($classType, $this->request->getData());
            $relationship_language = $this->ClassTypes->ClassTypeLanguages->newEntities($this->request->getData()['ClassTypeLanguages']);
            if ($model = $this->ClassTypes->save($classType)) {

                // 2, save language
                if(isset($relationship_language) && !empty($relationship_language)) {
                    foreach ($relationship_language as $language) {
                        $language['class_type_id'] = $model->id;
                    }
                    if(!$this->ClassTypes->ClassTypeLanguages->saveMany($relationship_language)) {
                        $this->Flash->error(__('data_is_not_saved'));
                        goto load_data;
                    }
                }

                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The classType could not be saved. Please, try again.'));
            }
        }

        load_data:
        $current_language = $this->lang18;
        $this->load_language();
        $this->set(compact('classType', 'current_language'));
    }

    public function load_language() {
        $language_input_fields = array(
            'id',
            'alias',
            'name'
        );

        $obj_Language = TableRegistry::get('Languages');
        $languages_list = $obj_Language->get_languages();

        $languages_model = 'ClassTypeLanguages';
        $languages_edit_model = 'class_type_languages';

        $this->set(compact('language_input_fields', 'languages_list', 'languages_model', 'languages_edit_model'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Class Type id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $classType = $this->ClassTypes->get($id, [
            'contain' => [
                'ClassTypeLanguages',
            ],
        ]);
        $languages_edit_data = (isset($classType['class_type_languages']) && !empty($classType['class_type_languages'])) ? $classType['class_type_languages'] : false;        
       
        // add row for the replace this->request->date_add
        if ($this->request->is(['patch', 'post', 'put'])) { 

            $db  = $this->ClassTypes->getConnection();
            $db->begin();

            $classType = $this->ClassTypes->patchEntity($classType, $this->request->getData());
            
            if ($this->ClassTypes->save($classType)) {
                
                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The classType could not be saved. Please, try again.'));
            }
        }

        load_data:
        $this->load_language();
        $current_language = $this->lang18;
        $this->set(compact('classType', 'current_language', 'languages_edit_data'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Class Type id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function _delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $classType = $this->ClassTypes->get($id);
        if ($this->ClassTypes->delete($classType)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The class type could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

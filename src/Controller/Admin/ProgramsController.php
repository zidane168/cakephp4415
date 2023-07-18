<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * Programs Controller
 *
 * @method \App\Model\Entity\Program[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProgramsController extends AppController
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
            $_conditions['Programs.enabled'] = intval($data_search['status']);
        }

        if (isset($data_search['name']) && !empty($data_search['name'])) {
            $_conditions['LOWER(ProgramLanguages.name) LIKE'] = '%' . trim(strtolower($data_search['name'])) . '%';
        }
        $this->paginate = array(
            'fields' => [
                'Programs.id',
                'Programs.title_color',
                'Programs.background_color',
                'Programs.enabled',
                'Programs.created',
                'Programs.modified',
                'ProgramLanguages.name'
            ],
            'conditions' => $_conditions,
            'order' => [
                'Programs.id DESC'
            ],
            'join' => [
                'table' => 'program_languages',
                'alias' => 'ProgramLanguages',
                'type' => 'INNER',
                'conditions' => [
                    'ProgramLanguages.program_id = Programs.id',
                    'ProgramLanguages.alias' => $this->lang18,
                ],
            ]
        );
        $programs = $this->paginate($this->Programs, array(
            'limit' => Configure::read('web.limit')
        ));

        $this->set(compact('programs', 'data_search'));
    }

    /**
     * View method
     *
     * @param string|null $id Program id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $program = $this->Programs->get($id, [
            'contain' => [
                'CreatedBy',
                'ModifiedBy',
                'ProgramLanguages',
                'ProgramImages'
            ],
        ]);
        $language_input_fields = array(
            'name',
            'description'
        );
        $languages = $program->program_languages;

        $images = $program->program_images;
        $this->set(compact('program', 'language_input_fields', 'languages', 'images'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $model = 'Programs';

        $program = $this->Programs->newEmptyEntity();
        $images_model = 'ProgramImages';
        if ($this->request->is('post')) {

            $db = $this->Programs->getConnection();
            $db->begin();

            $program = $this->Programs->patchEntity($program, $this->request->getData());
            $program_language = $this->Programs->ProgramLanguages->newEntities($this->request->getData()['ProgramLanguages']);
            if ($model = $this->Programs->save($program)) {

                // 2, save language
                if (isset($program_language) && !empty($program_language)) {
                    foreach ($program_language as $language) {
                        $language['program_id'] = $model->id;
                    }
                    if (!$this->Programs->ProgramLanguages->saveMany($program_language)) {
                        $this->Flash->error(__('data_is_not_saved'));
                        goto load_data;
                    }
                }

                //save image
                $images = $this->request->getData('ProgramImages');
                if (isset($images) && !empty($images)) {
                    foreach ($images as $key => $image) {
                        $relative_path = 'uploads' . DS . $images_model;
                        $file_name_suffix = "image";

                        $uploaded = $this->Common->upload_images($image['image'], $relative_path, $file_name_suffix, $key);

                        $temp = array(
                            'path'              => $uploaded['path'],
                            'name'              => $uploaded['ori_name'],
                            'width'             => $uploaded['width'],
                            'height'            => $uploaded['height'],
                            'size'              => $uploaded['size'],
                            'program_id'   => $model->id,
                        );
                        $save_data[] = $temp;
                    }

                    if (isset($save_data) && !empty($save_data)) {
                        $orm_ProgramImages = $this->Programs->ProgramImages->newEntities($save_data);

                        if (!$this->Programs->ProgramImages->saveMany($orm_ProgramImages)) {
                            $db->rollback();
                            $this->Flash->error(__('data_is_not_saved') . " Professional Images");
                            $this->redirect(array('action' => 'index'));
                        }
                    }
                }

                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The program could not be saved. Please, try again.'));
            }
        }

        load_data:
        $current_language = $this->lang18;
        $this->load_language();
        $this->load_image();
        $this->set(compact('program', 'current_language'));
    }

    public function load_language()
    {
        $language_input_fields = array(
            'id',
            'alias',
            'name',
            'description'
        );

        $obj_Language = TableRegistry::get('Languages');
        $languages_list = $obj_Language->get_languages();

        $languages_model = 'ProgramLanguages';
        $languages_edit_model = 'program_languages';

        $this->set(compact('language_input_fields', 'languages_list', 'languages_model', 'languages_edit_model'));
    }

    public function load_image()
    {
        $model = 'Programs';
        $images_model = 'ProgramImages';
        $add_new_images_url = Router::url(['controller' => 'Programs', 'action' => 'add_new_image_no_type', 'admin' => true]);

        $this->set(compact('model', 'images_model', 'add_new_images_url'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Program id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $program = $this->Programs->get($id, [
            'contain' => [
                'ProgramLanguages',
                'ProgramImages'
            ],
        ]);
        $images_model = 'ProgramImages';
        $images_edit_data   = $program->has('program_images') ? $program['program_images'] : array();
        $languages_edit_data = (isset($program['program_languages']) && !empty($program['program_languages'])) ? $program['program_languages'] : false;

        // add row for the replace this->request->date_add
        if ($this->request->is(['patch', 'post', 'put'])) {

            $db  = $this->Programs->getConnection();
            $db->begin();
 
            $program = $this->Programs->patchEntity($program, $this->request->getData());

            if ($this->Programs->save($program)) {
                //save image
                $images = $this->request->getData('ProgramImages');
 
                if (isset($images) && !empty($images)) {
                    foreach ($images as $key => $image) {
                        $relative_path = 'uploads' . DS . $images_model;
                        $file_name_suffix = "image";

                        $uploaded = $this->Common->upload_images($image['image'], $relative_path, $file_name_suffix, $key);

                        $temp = array(
                            'path'              => $uploaded['path'],
                            'name'              => $uploaded['ori_name'],
                            'width'             => $uploaded['width'],
                            'height'            => $uploaded['height'],
                            'size'              => $uploaded['size'],
                            'program_id'   => $id,
                        );
                        $save_data[] = $temp;
                    }

                    if (isset($save_data) && !empty($save_data)) {
                        $orm_ProgramImages = $this->Programs->ProgramImages->newEntities($save_data);
  
                        if (!$this->Programs->ProgramImages->saveMany($orm_ProgramImages)) {
                            $db->rollback();
                            $this->Flash->error(__('data_is_not_saved') . " Professional Images");
                            $this->redirect(array('action' => 'index'));
                        }
                    }
                }  
  
                // 4, delete images
                if (isset($this->request->getData()['data']) && !empty($this->request->getData()['data'])) { 

                    $data = $this->request->getData()['data'];
                    if (isset($data['remove_image']) && !empty($data['remove_image'])) {
                        $this->Programs->remove_uploaded_image('ProgramImages', $data['remove_image']);
                    }
                }

                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The program could not be saved. Please, try again.'));
            }
        }

        load_data:
        $this->load_language();
        $this->load_image();
        $current_language = $this->lang18;
        $this->set(compact('program', 'current_language', 'languages_edit_data', 'images_edit_data'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Program id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        // Check exist in course 
        $exist = $this->loadModel('Courses')->exists(['program_id' => $id]);
        if ($exist) {
            $this->Flash->warning(__d('cidcclass', 'cannot_delete_it_because_this_program_already_exist_on_course'));
            goto return_data;
        }

        // Check exist in class
        $exist = $this->loadModel('CidcClasses')->exists(['program_id' => $id]);
        if ($exist) {
            $this->Flash->warning(__d('cidcclass', 'cannot_delete_it_because_this_program_already_exist_on_class'));
            goto return_data;
        } 

        $program = $this->Programs->get($id);
        if ($this->Programs->delete($program)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The program could not be deleted. Please, try again.'));
        }

        return_data:
        return $this->redirect(['action' => 'index']);
    }
}

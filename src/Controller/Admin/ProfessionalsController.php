<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use App\MyHelper\MyHelper;
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;

/**
 * Professionals Controller
 *
 * @property \App\Model\Table\ProfessionalsTable $Professionals
 * @method \App\Model\Entity\Professional[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProfessionalsController extends AppController
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
            $_conditions['Professionals.enabled'] = intval($data_search['status']);
        }

        if (isset($data_search['title']) && !empty($data_search['title'])) {
            $_conditions['LOWER(ProfessionalLanguages.title) LIKE'] = '%' . trim(strtolower($data_search['title'])) . '%';
        }
        $genders = MyHelper::getGenders();
        $types = MyHelper::getTypesProfessional();
        $this->paginate = [
            'fields' => [
                'Professionals.id',
                'Professionals.gender',
                'Professionals.type',
                'Professionals.enabled',
                'Professionals.created',
                'Professionals.modified',
                'ProfessionalLanguages.name',
            ],
            'conditions' => $_conditions,
            'order' => [
                'Professionals.id DESC'
            ],
            'join' => [
                'table' => 'professional_languages',
                'alias' => 'ProfessionalLanguages',
                'type' => 'INNER',
                'conditions' => [
                    'ProfessionalLanguages.professional_id = Professionals.id',
                    'ProfessionalLanguages.alias' => $this->lang18,
                ],
            ],
        ];
        $professionals = $this->paginate($this->Professionals);

        $this->set(compact('professionals', 'types', 'genders', 'data_search'));
    }

    /**
     * View method
     *
     * @param string|null $id Professional id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $genders = MyHelper::getGenders();
        $types = MyHelper::getTypesProfessional();
        $professional = $this->Professionals->get($id, [
            'contain' => [
                'CreatedBy',
                'ModifiedBy',
                'ProfessionalImages',
                'ProfessionalLanguages',
                'ProfessionalsCertifications' => [
                    'ProfessionalCertificationLanguages'
                ]
            ],
        ]);
        $language_input_fields = array(
            'nick_name',
            'name',
            'title',
            'description',
        );
        $languages = $professional->professional_languages;
        $language_input_fields_certification = [
            'name'
        ];
        $language_certification = $professional->professionals_certifications;
        // -------------- IMAGES --------------
        $images = $professional->professional_images;
        $this->set(compact('professional', 'genders', 'types', 'language_input_fields', 'languages', 'language_input_fields_certification', 'language_certification', 'images'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $genders = MyHelper::getGenders();
        $types = MyHelper::getTypesProfessional();
        $images_model = 'ProfessionalImages';
        $professional = $this->Professionals->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData(); 
 
            $professional_languages = $this->Professionals->ProfessionalLanguages->newEntities($data['ProfessionalLanguages']);
            $db = $this->Professionals->getConnection();
            $db->begin();
            $professional = $this->Professionals->patchEntity($professional, $data);

            if ($model = $this->Professionals->save($professional)) {
                // save_language
                if (isset($professional_languages) && !empty($professional_languages)) {
                    foreach ($professional_languages as $language) {
                        $language['professional_id'] = $model->id;
                    }
                    if (!$this->Professionals->ProfessionalLanguages->saveMany($professional_languages)) {
                        $db->rollback();
                        $this->Flash->error(__('data_professional_language_is_not_saved'));
                        goto load_data;
                    }
                }

                // save certification
                foreach ($data['ProfessionalCertificationLanguages'] as $key => $cer_language) {
                    $professional_certification = $this->Professionals->ProfessionalsCertifications->newEmptyEntity();
                    $professional_certification->professional_id = $model->id;
                    if ($certification = $this->Professionals->ProfessionalsCertifications->save($professional_certification)) {
                        $obj_Professional_Certification_Languages = TableRegistry::get('ProfessionalCertificationLanguages');
                        $professional_certification_languages = $obj_Professional_Certification_Languages->newEntities($cer_language);

                        // save certification language
                        if (isset($professional_certification_languages) && !empty($professional_certification_languages)) {
                            foreach ($professional_certification_languages as $_language) {
                                $_language['professional_certification_id'] = $certification->id;
                            }
                            if (!$test = $obj_Professional_Certification_Languages->saveMany($professional_certification_languages)) {
                                $db->rollback();
                                $this->Flash->error(__('data_professional_certification_is_not_saved'));
                                goto load_data;
                            }
                        }
                    }
                }

                // save image
                $images = $this->request->getData('ProfessionalImages');
                // check images 
                $img = $images[0]['image'];
                if (isset($img) && !empty($img)) {  // pr ($img->getError());  // 0 : ko co bug // $img->getSize() == 0 // ko co file
                    if ($img->getError() > 0) { 
                        goto by_pass_upload_image;
                    }
                }
                    
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
                            'professional_id'   => $model->id,
                        );
                        $save_data[] = $temp;
                    }

                    if (isset($save_data) && !empty($save_data)) {
                        $orm_ProfessionalImages = $this->Professionals->ProfessionalImages->newEntities($save_data);

                        if (!$this->Professionals->ProfessionalImages->saveMany($orm_ProfessionalImages)) {
                            $db->rollback();
                            $this->Flash->error(__('data_is_not_saved') . " Professional Images");
                            $this->redirect(array('action' => 'index'));
                        }
                    }
                }

                by_pass_upload_image:
                $db->commit();
                $this->Flash->success(__('data_is_saved'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The professional could not be saved. Please, try again.'));
        }
        load_data:
        $current_language = $this->lang18;
        $this->load_language();
        $this->load_language_certification();
        $this->load_image();
        $this->set(compact('professional', 'current_language', 'genders', 'types'));
    }

    public function load_image()
    {
        $model = 'Professionals';
        $images_model = 'ProfessionalImages';
        $add_new_images_url = Router::url(['controller' => 'Professionals', 'action' => 'add_new_image_no_type', 'admin' => true]);

        $this->set(compact('model', 'images_model', 'add_new_images_url'));
    }
    public function load_language()
    {
        $language_input_fields = array(
            'id',
            'alias',
            'nick_name',
            'title',
            'description',
            'name'
        );

        $obj_Language = TableRegistry::get('Languages');
        $languages_list = $obj_Language->get_languages();

        $languages_model = 'ProfessionalLanguages';
        $languages_edit_model = 'professional_languages';

        $this->set(compact('language_input_fields', 'languages_list', 'languages_model', 'languages_edit_model'));
    }

    public function load_language_certification()
    {
        $language_input_fields_certification = array(
            'id',
            'alias',
            'name'
        );

        $languages_model_cerification = 'ProfessionalCertificationLanguages';
        $languages_edit_model_certification = 'professional_certification_languages';
        $add_language_input_url = Router::url(['controller' => 'Professionals', 'action' => 'add_new_language_input', 'admin' => true]);

        $this->set(compact('language_input_fields_certification', 'languages_model_cerification', 'languages_edit_model_certification', 'add_language_input_url'));
    }
    /**
     * Edit method
     *
     * @param string|null $id Professional id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $genders = MyHelper::getGenders();
        $types = MyHelper::getTypesProfessional();
        $images_model = 'ProfessionalImages';
        $professional = $this->Professionals->get($id, [
            'contain' => [
                'ProfessionalImages',
                'ProfessionalLanguages',
                'ProfessionalsCertifications' => [
                    'ProfessionalCertificationLanguages'
                ]
            ],
        ]);
        $images_edit_data   = $professional->has('professional_images') ? $professional['professional_images'] : array();
        $languages_edit_data = (isset($professional['professional_languages']) && !empty($professional['professional_languages'])) ? $professional['professional_languages'] : false;
        $professional_certification = $professional->has('professionals_certifications') ? $professional['professionals_certifications'] : [];

        if ($this->request->is(['patch', 'post', 'put'])) {
            $input = $this->request->getData();
            $db = $this->Professionals->getConnection();
            $db->begin();
            $professional = $this->Professionals->patchEntity($professional, $this->request->getData());
            if ($this->Professionals->save($professional)) {
 
                // delete certification
                foreach ($professional->professionals_certifications as $item) {
                    $this->Professionals->ProfessionalsCertifications->delete($item);
                }
                // save certification
                foreach ($input['ProfessionalCertificationLanguages'] as $key => $cer_language) {
                    $professional_certification = $this->Professionals->ProfessionalsCertifications->newEmptyEntity();
                    $professional_certification->professional_id = $id;
                    if ($certification = $this->Professionals->ProfessionalsCertifications->save($professional_certification)) {
                        $obj_Professional_Certification_Languages = TableRegistry::get('ProfessionalCertificationLanguages');
                        $professional_certification_languages = $obj_Professional_Certification_Languages->newEntities($cer_language);

                        // save certification language
                        if (isset($professional_certification_languages) && !empty($professional_certification_languages)) {
                            foreach ($professional_certification_languages as $_language) {
                                $_language['professional_certification_id'] = $certification->id;
                            }
                            if (!$test = $obj_Professional_Certification_Languages->saveMany($professional_certification_languages)) {
                                $db->rollback();
                                $this->Flash->error(__('data_professional_certification_is_not_saved'));
                                goto load_data;
                            }
                        }
                    }
                }

                //save image
                $images = $this->request->getData('ProfessionalImages');

                // check images  
                if (!empty($images)) {
                    $img = $images[0]['image'];
                    if (isset($img) && !empty($img)) { 
                        // pr ($img->getError());  // 0 : ko co bug
                        // $img->getSize() == 0 // ko co file
                        if ($img->getError() > 0) { 
                            goto by_pass_upload_image;
                        }
                    }
                }
             
                
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
                            'professional_id'   => $id,
                        );
                        $save_data[] = $temp;
                    }

                    if (isset($save_data) && !empty($save_data)) {
                        $orm_ProfessionalImages = $this->Professionals->ProfessionalImages->newEntities($save_data);

                        if (!$this->Professionals->ProfessionalImages->saveMany($orm_ProfessionalImages)) {
                            $db->rollback();
                            $this->Flash->error(__('data_is_not_saved') . " Professional Images");
                            $this->redirect(array('action' => 'index'));
                        }
                    }
                }

                by_pass_upload_image:
                // 4, delete images
                if (isset($input['data']) && !empty($input['data'])) {
                    $data = $this->request->getData()['data'];
                    if (isset($data['remove_image']) && !empty($data['remove_image'])) {
                        $this->Professionals->remove_uploaded_image('ProfessionalImages', $data['remove_image']);
                    }
                }
                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            }
            $db->rollback();
            $this->Flash->error(__('The professional could not be saved. Please, try again.'));
        }

        load_data:
        $this->load_image();
        $this->load_language();
        $this->load_language_certification();
        $current_language = $this->lang18;
        $this->set(compact('professional', 'genders', 'types', 'current_language', 'languages_edit_data', 'images_edit_data', 'professional_certification'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Professional id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $professional = $this->Professionals->get($id);
        if ($this->Professionals->delete($professional)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The professional could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

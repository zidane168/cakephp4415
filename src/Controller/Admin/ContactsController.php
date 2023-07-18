<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Routing\Router;

/**
 * Contacts Controller
 *
 * @method \App\Model\Entity\Contact[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ContactsController extends AppController
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
            $_conditions['Contacts.enabled'] = intval($data_search['status']);
        }

        if (isset($data_search['title']) && !empty($data_search['title'])) {
            $_conditions['LOWER(ContactLanguages.title) LIKE'] = '%' . trim(strtolower($data_search['title'])) . '%';
        }
        $this->paginate = [
            'fields' => [
                'Contacts.id',
                'Contacts.enabled',
                'Contacts.created',
                'Contacts.modified',
                'ContactLanguages.content',
                'ContactLanguages.title',
            ],
            'conditions' => $_conditions,
            'order'     => [
                'Contacts.id ASC'
            ],
            'contain' => [
                'ContactImages'
            ],
            'join'  => [
                'table' => 'contact_languages',
                'alias' => 'ContactLanguages',
                'type' => 'INNER',
                'conditions' => [
                    'ContactLanguages.contact_id = Contacts.id',
                    'ContactLanguages.alias' => $this->lang18,
                ],
            ]
        ];

        $contacts = $this->paginate($this->Contacts, [
            'limit' => Configure::read('web.limit')
        ]);
        $this->set(compact('contacts', 'data_search'));
    }

    /**
     * View method
     *
     * @param string|null $id Contact id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $contact = $this->Contacts->get($id, [
            'contain' => [
                'CreatedBy',
                'ModifiedBy',
                'ContactLanguages',
            ],
        ]);
        $language_input_fields = array(
            'title',
            'content'
        );
        $languages = $contact->contact_languages;
        $images = $contact->contact_images;
        $this->set(compact('contact', 'language_input_fields', 'languages', 'images'));
    }
    public function load_language()
    {
        $language_input_fields = array(
            'id',
            'alias',
            'content',
            'title',
        );

        $obj_Language = TableRegistry::get('Languages');
        $languages_list = $obj_Language->get_languages();

        $languages_model = 'ContactLanguages';
        $languages_edit_model = 'contact_languages';

        $this->set(compact('language_input_fields', 'languages_list', 'languages_model', 'languages_edit_model'));
    }

    // public function load_image()
    // {
    //     $model = 'Contacts';
    //     $images_model = 'ContactImages';
    //     $add_new_images_url = Router::url(['controller' => 'Contacts', 'action' => 'add_new_image_no_type', 'admin' => true]);

    //     $this->set(compact('model', 'images_model', 'add_new_images_url'));
    // }
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function _add()
    {
        $contact = $this->Contacts->newEmptyEntity();
        $images_model = 'ContactImages';
        if ($this->request->is('post')) {
            $contact = $this->Contacts->patchEntity($contact, $this->request->getData());
            $db = $this->Contacts->getConnection();
            $db->begin();

            $contact_languages = $this->Contacts->ContactLanguages->newEntities($this->request->getData()['ContactLanguages']);

            if ($model = $this->Contacts->save($contact)) {
                if (isset($contact_languages) && !empty($contact_languages)) {
                    foreach ($contact_languages as $language) {
                        $language['contact_id'] = $model->id;
                    }
                    if (!$this->Contacts->ContactLanguages->saveMany($contact_languages)) {
                        $db->rollback();
                        $this->Flash->error(__('data_is_not_saved'));
                        goto load_data;
                    }
                }

                //save image
                $images = $this->request->getData('ContactImages');
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
                            'contact_id'   => $model->id,
                        );
                        $save_data[] = $temp;
                    }

                    if (isset($save_data) && !empty($save_data)) {
                        $orm_ContactImages = $this->Contacts->ContactImages->newEntities($save_data);

                        if (!$this->Contacts->ContactImages->saveMany($orm_ContactImages)) {
                            $db->rollback();
                            $this->Flash->error(__('data_is_not_saved') . " Contact Images");
                            $this->redirect(array('action' => 'index'));
                        }
                    }
                }
                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            }
            $db->rollback();
            $this->Flash->error(__('The contact could not be saved. Please, try again.'));
        }
        load_data:
        $current_language = $this->lang18;
        $this->load_language();
        $this->set(compact('contact'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Contact id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $contact = $this->Contacts->get($id, [
            'contain' => [
                'ContactLanguages',
                'ContactImages'
            ],
        ]);
        $images_model = 'ContactImages';

        $languages_edit_data = (isset($contact['contact_languages']) && !empty($contact['contact_languages'])) ? $contact['contact_languages'] : false;
        $images_edit_data   = $contact->has('contact_images') ? $contact['contact_images'] : array();
        if ($this->request->is(['patch', 'post', 'put'])) {

            $contact = $this->Contacts->patchEntity($contact, $this->request->getData());
            $db = $this->Contacts->getConnection();
            $db->begin();
            if ($this->Contacts->save($contact)) {
                //save image
                $images = $this->request->getData('ContactImages');
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
                            'contact_id'   => $id,
                        );
                        $save_data[] = $temp;
                    }

                    if (isset($save_data) && !empty($save_data)) {
                        $orm_ContactImages = $this->Contacts->ContactImages->newEntities($save_data);

                        if (!$this->Contacts->ContactImages->saveMany($orm_ContactImages)) {
                            $db->rollback();
                            $this->Flash->error(__('data_is_not_saved') . " Professional Images");
                            $this->redirect(array('action' => 'index'));
                        }
                    }
                }
                // 4, delete images
                $input = $this->request->getData();
                if (isset($input['data']) && !empty($input['data'])) {
                    $data = $this->request->getData()['data'];
                    if (isset($data['remove_image']) && !empty($data['remove_image'])) {
                        $this->Contacts->remove_uploaded_image('ContactImages', $data['remove_image']);
                    }
                }
                $db->commit();
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            } else {
                $db->rollback();
                $this->Flash->error(__('The policy could not be saved. Please, try again.'));
            }
        }
        load_data:
        $this->load_language();
        $current_language = $this->lang18;
        $this->set(compact('contact', 'current_language', 'languages_edit_data', 'images_edit_data'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Contact id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $contact = $this->Contacts->get(
            $id,
            [
                'contain' => [
                    'ContactLanguages',
                    'ContactImages',
                ]
            ]
        );
        if ($this->Contacts->delete($contact)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The contact could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

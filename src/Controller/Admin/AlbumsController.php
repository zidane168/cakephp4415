<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Routing\Router;

/**
 * Albums Controller
 *
 * @method \App\Model\Entity\Album[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AlbumsController extends AppController
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

        if (isset($data_search['cidc_class_id']) && $data_search['cidc_class_id'] != "") {
            $_conditions['Albums.cidc_class_id'] = intval($data_search['cidc_class_id']);
        }
        $this->paginate = [
            'conditions' => $_conditions,
            'order' => [
                'Albums.id DESC',
            ],
            'contain' => [
                'CidcClasses' => [
                    'fields' => [
                        'CidcClasses.id',
                        'CidcClasses.name',
                    ], 
                ],
            ],
        ];
        $albums = $this->paginate($this->Albums); 
        $cidcClasses = $this->Albums->CidcClasses->get_list()->toArray();

        $this->set(compact('albums', 'data_search', 'cidcClasses'));
    }

    /**
     * View method
     *
     * @param string|null $id Album id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $album = $this->Albums->get($id, [
            'contain' => [
                'CidcClasses',
            ],
        ]);

        $this->set(compact('album'));
    }
    public function load_image()
    {
        $model = 'Albums';
        $images_model = 'AlbumImages';
        $add_new_images_url = Router::url(['controller' => 'Albums', 'action' => 'add_new_image_no_type', 'admin' => true]);

        $this->set(compact('model', 'images_model', 'add_new_images_url'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $model = 'Albums';
        $images_model = 'Albums';
        $album = $this->Albums->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $images = $data['AlbumImages'];
            if (isset($images) && !empty($images)) {
                foreach ($images as $key => $image) {
                    $relative_path = 'uploads' . DS . $images_model;
                    $file_name_suffix = "image";
                    $uploaded = $this->Common->upload_images($image['image'], $relative_path, $file_name_suffix, $key);
                    $temp = array(
                        'path'              => $uploaded['path'],
                        'file_name'         => $uploaded['ori_name'],
                        'width'             => $uploaded['width'],
                        'height'            => $uploaded['height'],
                        'size'              => $uploaded['size'],
                        'cidc_class_id'   => $data['cidc_class_id'],
                    );
                    $save_data[] = $temp;
                }
            }
            if (isset($save_data) && !empty($save_data)) {
                $albums = $this->Albums->newEntities($save_data);

                if (!$this->Albums->saveMany($albums)) {
                    $this->Flash->error(__('data_is_not_saved') . "  Albums");
                    $this->redirect(array('action' => 'index'));
                }
            }
            $this->Flash->success(__('data is saved'));
            return $this->redirect(['action' => 'index']);
        }
        $cidcClasses = $this->Albums->CidcClasses->get_list(); 
        $this->load_image();
        $this->set(compact('album', 'cidcClasses'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Album id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function _edit($id = null)
    {
        $album = $this->Albums->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $album = $this->Albums->patchEntity($album, $this->request->getData());
            if ($this->Albums->save($album)) {
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The album could not be saved. Please, try again.'));
        }
        $this->set(compact('album'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Album id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $album = $this->Albums->get($id);
        if ($this->Albums->delete($album)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The album could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use App\MyHelper\MyHelper;
use Cake\Routing\Router;

/**
 * Videos Controller
 *
 * @property \App\Model\Table\VideosTable $Videos
 * @method \App\Model\Entity\Video[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class VideosController extends AppController
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
            $_conditions['Videos.cidc_class_id'] = intval($data_search['cidc_class_id']);
        }

        $this->paginate = [
            'conditions' => $_conditions,
            'order' => [
                'Videos.id DESC',
            ]
        ];
        $cidcClasses = $this->Videos->CidcClasses->get_list()->toArray();
        $videos = $this->paginate($this->Videos);
        $url = MyHelper::getUrl(); 

        $this->set(compact('videos', 'cidcClasses', 'data_search', 'url'));
    }

    /**
     * View method
     *
     * @param string|null $id Video id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $video = $this->Videos->get($id, [
            'contain' => ['CidcClasses'],
        ]);
        $url = MyHelper::getUrl();

        $video->size = $this->Videos->format_size_units($video->size);
        $this->set(compact('video', 'url'));
    }
    public function load_image()
    {
        $images_model = "VideosImages";
        $this->set(compact('images_model'));
    }
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $model = 'Videos';
        $images_model = 'Videos';
        $video = $this->Videos->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $videos = $data['VideosImages'];
            if (isset($video) && !empty($video)) {
                foreach ($videos as $key => $image) {
                    $relative_path = 'uploads' . DS . $images_model;
                    $file_name_suffix = "image";
                    $uploaded = $this->Common->upload_videos($image['image'], $relative_path, $file_name_suffix, $key);
                    $temp = array(
                        'path'              => $uploaded['path'],
                        'file_name'         => $uploaded['ori_name'],
                        'size'              => $uploaded['size'],
                        'ext'              => $uploaded['ext'],
                        'cidc_class_id'   => $data['cidc_class_id'],
                    );
                    $save_data[] = $temp;
                }
            }
            if (isset($save_data) && !empty($save_data)) {
                $videos = $this->Videos->newEntities($save_data);

                if (!$this->Videos->saveMany($videos)) {
                    $this->Flash->error(__('data_is_not_saved') . "  Albums");
                    $this->redirect(array('action' => 'index'));
                }
            }
            $this->Flash->success(__('data is saved'));
            return $this->redirect(['action' => 'index']);
        }
        $cidcClasses = $this->Videos->CidcClasses->get_list();
        $this->load_image();
        $this->set(compact('video', 'cidcClasses'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Video id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $video = $this->Videos->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $video = $this->Videos->patchEntity($video, $this->request->getData());
            if ($this->Videos->save($video)) {
                $this->Flash->success(__('data_is_saved'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The video could not be saved. Please, try again.'));
        }
        $cidcClasses = $this->Videos->CidcClasses->find('list', ['limit' => 200]);
        $this->set(compact('video', 'cidcClasses'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Video id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $video = $this->Videos->get($id);
        if ($this->Videos->delete($video)) {
            $this->Flash->success(__('data_is_deleted'));
        } else {
            $this->Flash->error(__('The video could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

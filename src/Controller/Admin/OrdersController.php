<?php
declare(strict_types=1);

namespace App\Controller\Admin;
use App\MyHelper\MyHelper; 

use App\Controller\Admin\AppController;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Core\Configure;

/**
 * Orders Controller
 *
 * @property \App\Model\Table\OrdersTable $Orders
 * @method \App\Model\Entity\Order[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class OrdersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $data_search = $this->request->getQuery();
        $conditions =  array();

        if (isset($data_search['status']) && $data_search['status'] != "") {
            $conditions['Orders.status'] = intval($data_search['status']);
        }

        if (isset($data_search['order_number']) && !empty($data_search['order_number'])) {
            $conditions['LOWER(Orders.order_number) LIKE'] = '%' . trim(strtolower($data_search['order_number'])) . '%';
        }

        $this->paginate = [
            'contain' => ['CreatedBy', 'ModifiedBy'],
            'conditions' => $conditions,
            'order' => [
                'Orders.id DESC',
            ]
        ];

        $orders = $this->paginate($this->Orders, [
            'limit' => Configure::read('web.limit')
        ]);   

        $this->set(compact('orders', 'data_search'));
    }

    /**
     * View method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $order = $this->Orders->get($id, [
            'contain' => [
                'CreatedBy', 'ModifiedBy', 
                'OrderReceipts',
                'StudentRegisterClasses' => [
                    'CidcClasses',
                    'Kids' => [
                        'KidLanguages' => [
                            'conditions' => [
                                'KidLanguages.alias' => $this->lang18
                            ],
                        ],
                    ],
                ]
            ],
        ]);

        $url = Router::url('/', true);
        $this->set(compact('order', 'url'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    // public function add()
    // {
    //     $order = $this->Orders->newEmptyEntity();
    //     if ($this->request->is('post')) {
    //         $order = $this->Orders->patchEntity($order, $this->request->getData());
    //         if ($this->Orders->save($order)) {
    //             $this->Flash->success(__('data_is_saved'));

    //             return $this->redirect(['action' => 'index']);
    //         }
    //         $this->Flash->error(__('The order could not be saved. Please, try again.'));
    //     }
    //     $createdBy = $this->Orders->CreatedBy->find('list', ['limit' => 200]);
    //     $modifiedBy = $this->Orders->ModifiedBy->find('list', ['limit' => 200]);
    //     $this->set(compact('order', 'createdBy', 'modifiedBy'));
    // }

    /**
     * Edit method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $images_model = "OrderReceipts";
        $order = $this->Orders->get($id, [
            'contain' => [
                'StudentRegisterClasses',
                'OrderReceipts'
            ],
        ]);
  
        if ($order->status == MyHelper::PAID) {
            $this->Flash->error(__d('cidcclass', 'cannot_change_info_payment_paid_already'));
            return $this->redirect(['action' => 'index']);
        }

        $images_edit_data =  json_encode($order['order_receipts']);
       
        if ($this->request->is(['patch', 'post', 'put'])) {
            $db = $this->Orders->getConnection();
            $db->begin();

            $order = $this->Orders->patchEntity($order, $this->request->getData());
         
            if ($this->Orders->save($order)) {

                $files = $this->request->getData('OrderReceipts');

                if (isset($files) && !empty($files)) {
                    $temp = array();

                    foreach ($files as $key => $file) {

                        $relative_path = 'uploads' . DS . $images_model;
                        $file_name_suffix = "file";
                        $f = $file['image'];
                        if (!$f)    {      continue;    }

                        if ($f->getSize() == 0) { 
                            continue;
                        }

                        $uploaded = $this->Common->upload_files($f, $relative_path, $file_name_suffix, $key);

                        $temp[] = array(
                            'order_id'          => $id,
                            'file_name'         => $uploaded['ori_name'],
                            'path'              => $uploaded['path'],
                            'size'              => $f->getSize(),
                            'ext'               => $uploaded['ext'],
                        );
                    }   // end foreach

                    $orm_OrderReceipts = $this->Orders->OrderReceipts->newEntities($temp);
                    if (!empty($orm_OrderReceipts)) {

                        if (!$this->Orders->OrderReceipts->saveMany($orm_OrderReceipts)) {
                            $db->rollback();
                            $this->Flash->error(__('data_is_not_saved'));
                            $this->redirect(array('action' => 'index'));
                        }
                    }
                }

                // 4, delete images
                $remove_images = $this->request->getData('remove_image')[0];

                if (isset($remove_images) && !empty($remove_images)) {
                    $remove_images = json_decode($remove_images);
                    $this->Orders->remove_uploaded_image('OrderReceipts', $remove_images);
                }

                // Send System message 
                // $flag_send_notification = TableRegistry::get('SystemMessages')->send_system_message($cart_json, $user_id, $language) 

                $db->commit();
                $this->Flash->success(__('data_is_saved')); 
                return $this->redirect(['action' => 'index']);

            } else {
                $db->rollback();
                $this->Flash->error(__('The order could not be saved. Please, try again.'));
            }
         
        } 
        $this->set(compact('order', 'images_model', 'images_edit_data'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    // public function delete($id = null)
    // {
    //     $this->request->allowMethod(['post', 'delete']);
    //     $order = $this->Orders->get($id);
    //     if ($this->Orders->delete($order)) {
    //         $this->Flash->success(__('data_is_deleted'));
    //     } else {
    //         $this->Flash->error(__('The order could not be deleted. Please, try again.'));
    //     }

    //     return $this->redirect(['action' => 'index']);
    // }
}

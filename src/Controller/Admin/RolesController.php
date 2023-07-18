<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\ORM\TableRegistry;

/**
 * Roles Controller
 *
 * @property \App\Model\Table\RolesTable $Roles
 * @method \App\Model\Entity\Role[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class RolesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {

        $this->paginate = [
            'contain' => [
                'CreatedBy',
                'ModifiedBy',
            ],
            'order' => array(
                'Roles.id DESC'
            ),
        ];

        $roles = $this->paginate($this->Roles);

        $this->set(compact('roles'));
    }

    /**
     * View method
     *
     * @param string|null $id Role id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $role = $this->Roles->get($id, [
            'contain' => [
                'CreatedBy',
                'ModifiedBy',
                'Permissions'
            ],
        ]);

        $this->set(compact('role'));

        $obj_RolesPermissions = TableRegistry::get('RolesPermissions');
        $permissions_matrix = $obj_RolesPermissions->get_permissions_by_role($id);

        $this->set(compact('permissions_matrix'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $role = $this->Roles->newEmptyEntity();
        if ($this->request->is('post')) {
            $role = $this->Roles->patchEntity($role, $this->request->getData());

            $db = $this->Roles->getConnection();
            $db->begin();

            if ($model = $this->Roles->save($role)) {
               
                // assign roles to role permissions here;
                $temp = array();
                if (isset($this->request->getData()['rules']) && !empty($this->request->getData()['rules'])) {
                    foreach ($this->request->getData()['rules'] as $item) {
                        $temp[] = array(
                            'role_id'       => $model->id,
                            'permission_id' => $item,
                        );
                    }
                }
              
                if ($temp && !empty($temp)) {

                    $obj_RolesPermissions = TableRegistry::get('RolesPermissions');
                    $data = $obj_RolesPermissions->newEntities($temp);

                    if (!$obj_RolesPermissions->saveMany($data)) {
                        $db->rollback();

                        $this->Flash->error(__('data_is_not_saved'));
                        $this->redirect(array('action' => 'index'));
                        return;
                    } 
                }

                $db->commit();

                $this->Flash->success(__('The role has been saved.'));
                return $this->redirect(['action' => 'index']);
            }

            $db->rollback();
            $this->Flash->error(__('The role could not be saved. Please, try again.'));
        }
        // $manageRoles = $this->Role->get_list_parent();
        $permissions_matrix = $this->Roles->Permissions->get_list();    // 'admin'
        $this->set(compact('permissions_matrix', 'role'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Role id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $role = $this->Roles->get($id, [
            'contain' => ['Permissions'],
        ]);

        $obj_RolesPermissions = TableRegistry::get('RolesPermissions');

        if ($this->request->is(['patch', 'post', 'put'])) {
            $role = $this->Roles->patchEntity($role, $this->request->getData());

            $db = $this->Roles->getConnection();
            $db->begin();
           

            if ($this->Roles->save($role)) {
                
                // remove all current role
                $obj_RolesPermissions->deleteAll(array('RolesPermissions.role_id' => $id));
              
                // assign roles to role permissions here;
                $temp = array();
                if (isset($this->request->getData()['rules']) && !empty($this->request->getData()['rules'])) {
                    foreach ($this->request->getData()['rules'] as $item) {
                        $temp[] = array(
                            'role_id'       => $id, 
                            'permission_id' => $item,
                        );
                    }
                }

                if ($temp && !empty($temp)) {

                    $obj_RolesPermissions = TableRegistry::get('RolesPermissions');
                    $data = $obj_RolesPermissions->newEntities($temp);

                    if (!$obj_RolesPermissions->saveMany($data)) {
                        $db->rollback();

                        $this->Flash->error(__('data_is_not_saved'));
                        $this->redirect(array('action' => 'index'));
                        return;
                    } 
                }

                $db->commit();
                $this->Flash->success(__('you_must_relogin_for_the_changes_to_take_effect'));
                return $this->redirect(['controller' => 'Administrators','action' => 'login']);
            }
            $db->rollback();
            $this->Flash->error(__('The role could not be saved. Please, try again.'));
        }

        $permissions_matrix = $this->Roles->Permissions->get_list();    // 'admin'
        $current_permissions = $obj_RolesPermissions->get_permission_ids_by_role($id);
        
        $this->set(compact('role', 'permissions_matrix', 'current_permissions'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Role id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $role = $this->Roles->get($id);
        if ($this->Roles->delete($role)) {
            $this->Flash->success(__('The role has been deleted.'));
        } else {
            $this->Flash->error(__('The role could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

}

<?php
declare(strict_types=1);

// export csv plugin
// composer require friendsofcake/cakephp-csvview
// https://github.com/FriendsOfCake/cakephp-csvview

namespace App\Controller\Admin;

use App\Controller\Admin\AppController; // change this first
use Cake\Utility\Text;
use Cake\ORM\TableRegistry;

/**
 * Permissions Controller
 *
 * @property \App\Model\Table\PermissionsTable $Permissions
 * @method \App\Model\Entity\Permission[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PermissionsController extends AppController
{
    // 30000 x 3, 17000
    public function addall(){
        $permission = $this->Permissions->newEmptyEntity();
        $db = $this->Permissions->getConnection();
        $db->begin();

        if ($this->request->is('post')) {
            $permission = $this->Permissions->patchEntity($permission, $this->request->getData());
          
            if (!$permission->isDirty()) {
                goto load_data;
            }
			
            $slug = "";
            if ($permission['p_model'] && !empty($permission['p_model'])) {
                $slug       = Text::slug($permission['p_model'], '-');
            }
		
			$controller = $permission['p_controller'];
			$model      = $permission['p_model'];
            $name       = $permission['name'];
            
            $exist_permission = $this->Permissions->exists( array(
                'Permissions.p_model' => $model,
                'Permissions.p_controller' => $controller
            ));

            if($exist_permission){
                $this->Flash->error(__d('administration', 'permission_exist'));
            }else{
                $temp = array(
                    array(
                        'slug' => 'perm-admin-'.$slug.'-view',
                        'name' => $name . '-View',
                        'p_controller' => $controller,
                        'p_model' => $model,
                        'action' => 'view',
                    ),
                    array(
                        'slug' => 'perm-admin-'.$slug.'-add',
                        'name' => $name . '-Add',
                        'p_controller' => $controller,
                        'p_model' => $model,
                        'action' => 'add',
                    ),
                    array(
                        'slug' => 'perm-admin-'.$slug.'-edit',
                        'name' => $name . '-Edit',
                        'p_controller' => $controller,
                        'p_model' => $model,
                        'action' => 'edit',
                    ),
                    array(
                        'slug' => 'perm-admin-'.$slug.'-delete',
                        'name' => $name . '-Delete',
                        'p_controller' => $controller,
                        'p_model' => $model,
                        'action' => 'delete',
                    ),
                );

                $data = $this->Permissions->newEntities($temp);
           
                if ($model = $this->Permissions->saveMany($data)) {

                    $db->commit();

                    $this->Flash->success(__('data_is_saved'));
                    $this->redirect(array('action' => 'index'));

                } else {

                    $db->rollback();
                    $this->Flash->error(__('data_is_not_saved'));
                }
            }
			
		}

        load_data:
        $this->set(compact('permission'));
	}


    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $conditions = array();
        $data_search = $this->request->getQuery();

        if (isset($data_search['p_controller']) && !empty($data_search['p_controller'])) {
            $conditions['Permissions.p_controller LIKE'] = '%'. trim($data_search['p_controller']) . '%';
        }

        if (isset($data_search['p_model']) && !empty($data_search['p_model'])) {
            $conditions['Permissions.p_model LIKE'] = '%'. trim($data_search['p_model']) . '%';
        }

        if (isset($data_search['name']) && !empty($data_search['name'])) {
            $conditions['Permissions.name LIKE'] = '%'. trim($data_search['name']) . '%';
        }

        if (isset($data_search['slug']) && !empty($data_search['slug'])) {
            $conditions['Permissions.slug LIKE'] = '%'. trim($data_search['slug']) . '%';
        }

        $permissions_records = $this->paginate($this->Permissions, array(
            'conditions' => $conditions,
            'limit' => 200,
            'order' => array(
                'Permissions.id DESC'
            ),
        ));
        $this->set(compact('permissions_records', 'data_search'));

        if( isset($data_search['button']['delete_all']) && !empty($data_search['button']['delete_all']) ) {
            $this->delete_all();
        }

        // button export
        if (isset($data_search['button']['export']) && !empty($data_search['button']['export'])) {

            $this->export(array(
                'conditions' => $conditions,
                'type' => 'csv',
                'language' => "",	//$language,
            ));
        }

    }


    private function export($params) {

        if( $this->request->is('get') ) {

            $header = array(
                __('id'),
                __d('permission', 'name'),
                __('slug'),
                __d('permission', 'p_controller'),
                __d('permission', 'p_model'),
                __d('permission', 'action'),
                __('modified'),
                __('modified_by'),
                __('created'),
                __('created_by'),
            );

            $extract = array(
                'id', 
                'name', 
                'slug', 
                'p_controller', 
                'p_model', 
                'action', 
                'modified', 'modified_by', 'created', 'created_by');
            
            try{
                $file_name = 'permission_' . date('Ymdhis');

                // export xls
                if ($params['type'] == "xls") {
                   
                } else {
                    $this->setResponse($this->getResponse()->withDownload($file_name . ".csv"));

                    $result_permissions = $this->Permissions->find('all', array('conditions' => $params['conditions']) );
                    $this->set(compact('result_permissions')); // => compact => result_permissions

                    $serialize = 'result_permissions';         // => serialize result_permissions MUST SAME AS compact('result_permissions);
                    $this->viewBuilder()
                        ->setClassName('CsvView.Csv')
                        ->setOptions([
                            'serialize'     => $serialize,
                            'header'        => $header,
                            'extract'       => $extract,        // use all query in this table no change anything, see example: administrators, if u want to change the data display
                            'bom'           => true,    // unicode
                        ]);
                }
                

            } catch ( \Exception $e ) {
                $this->Flash->error(__('export_csv_fail') . ": " . $e->getMessage());
            }
        }
    }


    /**
     * View method
     *
     * @param string|null $id Permission id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $permission = $this->Permissions->get($id, [
            'contain' => ['Roles'],
        ]);

        $this->set(compact('permission'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $permission = $this->Permissions->newEmptyEntity();
        if ($this->request->is('post')) {
            $permission = $this->Permissions->patchEntity($permission, $this->request->getData());
            if ($this->Permissions->save($permission)) {
                $this->Flash->success(__('The permission has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The permission could not be saved. Please, try again.'));
        }
        $roles = $this->Permissions->Roles->find('list', ['limit' => 200]);
        $this->set(compact('permission', 'roles'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Permission id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $permission = $this->Permissions->get($id, [
            'contain' => ['Roles'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $permission = $this->Permissions->patchEntity($permission, $this->request->getData());
            if ($this->Permissions->save($permission)) {
                $this->Flash->success(__('The permission has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The permission could not be saved. Please, try again.'));
        }
        $roles = $this->Permissions->Roles->find('list', ['limit' => 200]);
        $this->set(compact('permission', 'roles'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Permission id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $permission = $this->Permissions->get($id);
        if ($this->Permissions->delete($permission)) {
            $this->Flash->success(__('The permission has been deleted.'));
        } else {
            $this->Flash->error(__('The permission could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }


    // vilh (2021/05/21) night 11:11pm
	// - delete permission on permission table
	// - delete permission had assign to role
	private function delete_all() {
	
        $data = $this->request->getQuery();
        $db = $this->Permissions->getConnection();
        $db->begin();

        if ($data)  {
            $obj_RolesPermissions = TableRegistry::get('RolesPermissions');
            
            // Delete all permission had assigned to role - with permission.id (RolesPermission table)
            $obj_RolesPermissions->deleteAll(array('RolesPermissions.permission_id IN' => $data['ids']));
            
            // Delete all permission - with permission.id (Permission table)
            if (!$this->Permissions->deleteAll(array('Permissions.id IN' => $data['ids']))) {
                $this->Flash->error(__('data_is_not_deleted'). " Permissions");
                $db->rollback();
                goto result;
            }            

            $db->commit();
            $this->Flash->success(__('data_is_deleted'));

            result:
            $this->redirect(array('action' => 'index'));
        }
	}
}


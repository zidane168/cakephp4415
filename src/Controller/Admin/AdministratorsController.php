<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * Administrators Controller
 *
 * @property \App\Model\Table\AdministratorsTable $Administrators
 * @method \App\Model\Entity\Administrator[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AdministratorsController extends AppController
{

    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $data_search = $this->request->getQuery();
        $conditions = array();
        if (isset($data_search['role_id']) && !empty($data_search['role_id'])) {

            $obj_AdministratorsRoles = TableRegistry::get('AdministratorsRoles');

            $user_ids = $obj_AdministratorsRoles->get_user_by_role($data_search['role_id']);
            if ($user_ids) {
                $conditions["Administrators.id IN"] = $user_ids;
            } else {
                $conditions["Administrators.id"] = '-1';
            }
        }

        if (isset($data_search['name']) && !empty($data_search['name'])) {
            $conditions['Administrators.name LIKE'] = '%' . trim($data_search['name']) . '%';
        }

        if (isset($data_search['email']) && !empty($data_search['email'])) {
            $conditions['Administrators.email LIKE'] = '%' . trim($data_search['email']) . '%';
        }

        $this->paginate = [
            'contain' => [ 
                'Roles' => array(
                    'fields' => [
                        'Roles.name',
                    ],
                ), 
                'AdministratorManageCenters' => [
                    'Centers' => [
                        'CenterLanguages' => [
                            'conditions' => [
                                'CenterLanguages.alias' => $this->lang18
                            ]
                        ]
                    ]

                ]
            ],
            'conditions' => $conditions,
            'order' => array(
                'Administrators.id DESC'
            ),

        ];

        // button export
        if (isset($data_search['button']['export']) && !empty($data_search['button']['export'])) {

            $this->export(array(
                'conditions' => $conditions,
                'type' => 'csv',
                'language' => "",    //$language,
            ));
        }

        $administrators = $this->paginate($this->Administrators);
        // debug($administrators);exit;

        $roles = $this->Administrators->Roles->get_list();
        $this->set(compact('administrators', 'roles', 'data_search'));
    }

    private function export($params)
    {

        if ($this->request->is('get')) {

            try {
                $file_name = 'administrator_' . date('Ymdhis');

                $result_Administrators = $this->Administrators->find('all', array(
                    'conditions' => $params['conditions'],
                    'contain' => array(
                        'Roles' => array(
                            'fields' => [
                                'Roles.name',
                            ],
                        ),
                        'Companies' => array(
                            'CompanyLanguages' => array(
                                'conditions' => array(
                                    'CompanyLanguages.alias' => $this->lang18,
                                ),
                            ),
                        ),
                        'CreatedBy',
                        'ModifiedBy',
                    ),
                ));

                $header = array(
                    __('id'),
                    __d('administration', 'name'),
                    __d('administration', 'email'),
                    __d('administration', 'role'),
                    __d('administration', 'company'),
                    __d('administration', 'phone'),
                    __d('administration', 'last_logged_in'),
                    __('enabled'),
                    __('modified'),
                    __('modified_by'),
                    __('created'),
                    __('created_by'),
                );


                // export excel: xls
                if ($params['type'] == "xls") {

                    $data_excel =
                        '<table cellspacing="2" cellpadding="5" style="border: 2px; text-align:center;" border="1" width="60%" >';
                    $data_excel .=
                        '<tr>';

                    foreach ($header as $item) {
                        $data_excel .=
                            '<th style="font-size: 13px; text-align: center; background-color: orange; color: white; font-weight: bold">' .  $item .  '</th>';
                    }

                    $data_excel .=
                        '</tr>';

                    $data_query = $result_Administrators->toArray();
                    foreach ($data_query as $d) {
                        $companies = $d->has('company') ? reset($d->company->company_languages)['name'] : '';
                        $tmp_roles = array();
                        foreach ($d->roles as $role) {
                            $tmp_roles[] = $role->name;
                        }

                        $data_excel .=
                            '<tr>' .
                            '<td style="text-align: center;">' .  $d->id .  '</td>' .
                            '<td style="text-align: center;">' .  $d->name .  '</td>' .
                            '<td style="text-align: center;">' .  $d->email .  '</td>' .
                            '<td style="text-align: center;">' .  implode(", ", $tmp_roles)  .  '</td>' .
                            '<td style="text-align: center;">' .  $companies  .  '</td>' .
                            '<td style="text-align: center;">' .  $d->phone  .  '</td>' .
                            '<td style="text-align: center;">' .  $d->last_logged_in  .  '</td>' .
                            '<td style="text-align: center;">' .  $d->enabled  .  '</td>' .
                            '<td style="text-align: center;">' .  $d->modified  .  '</td>' .
                            '<td style="text-align: center;">' .  ($d->modified_by && !empty($d->modified_by) ? $d->modified_by['name'] : '')  .  '</td>' .
                            '<td style="text-align: center;">' .  $d->created  .  '</td>' .
                            '<td style="text-align: center;">' .  ($d->created_by && !empty($d->created_by) ? $d->created_by['name'] : '') .  '</td>' .
                            '</tr>';
                    }

                    $data_excel .=
                        '</table>';

                    // Export Excel
                    header("Content-type: application/vnd.ms-excel; charset=UTF-8");
                    header('Content-Encoding: UTF-8');

                    header('Content-disposition: attachment; filename=' . $file_name . '.xls');
                    header('Pragma: ');
                    // header ('Cache-Control: ');
                    header('Cache-Control: max-age=0');

                    echo "\xEF\xBB\xBF";        // vilh: unicode here, important line:
                    echo $data_excel;
                    exit;
                } else if ($params['type'] == "csv") {

                    $this->setResponse($this->getResponse()->withDownload($file_name . ".csv"));

                    $data = array();

                    foreach ($result_Administrators as $item) {

                        $roles = "";
                        $tmp_roles = array();
                        foreach ($item->roles as $role) {
                            $tmp_roles[] = $role->name;
                        }

                        $companies = $item->has('company') ? reset($item->company->company_languages)['name'] : '';
                        $data[] = array(
                            $item->id,
                            $item->name,
                            $item->email,
                            implode(", ", $tmp_roles),
                            $companies,
                            $item->phone,
                            $item->last_logged_in,
                            $item->enabled,
                            $item->modified,
                            $item->modified_by && !empty($item->modified_by) ? $item->modified_by['name'] : '',
                            $item->created,
                            $item->created_by && !empty($item->created_by) ? $item->created_by['name'] : '',
                        );
                    }

                    $this->set(compact('data'));
                    $this->viewBuilder()
                        ->setClassName('CsvView.Csv')
                        ->setOptions([
                            'serialize' => 'data',
                            'header'    => $header,
                            'bom'       => true,    // vilh, unicode Important line;
                        ]);
                } else if ($params['type'] == 'pdf') {
                    $this->viewBuilder()->enableAutoLayout(false);

                    $this->viewBuilder()->setClassName('CakePdf.Pdf');
                    $this->viewBuilder()->setOption(
                        'pdfConfig',
                        [
                            // 'orientation' => 'portrait',
                            'orientation' => 'landscape',
                            'download' => true, // This can be omitted if "filename" is specified.
                            'filename' => $file_name . ".pdf", // 'Report_' . $id . '.pdf' //// This can be omitted if you want file name based on URL.
                        ]
                    );
                    $this->set('result_Administrators', $result_Administrators->toArray());
                }
            } catch (\Exception $e) {
                $this->Flash->error(__('export_csv_fail') . ": " . $e->getMessage());
            }
        }
    }

    /**
     * View method
     *
     * @param string|null $id Administrator id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $administrator = $this->Administrators->get($id, [
            'contain' => [
                'Centers' => [
                    'CenterLanguages' => [
                        'conditions' => [
                            'CenterLanguages.alias' => $this->lang18,
                        ],
                    ],
                ],
                'Roles',
                'AdministratorManageCenters' => [
                    'Centers' => [
                        'CenterLanguages' => [
                            'conditions' => [
                                'CenterLanguages.alias' => $this->lang18
                            ]
                        ]
                    ]
                ],
                'CreatedBy',
                'ModifiedBy',
            ],
        ]);

        $this->set(compact('administrator'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {  
        $administrator = $this->Administrators->newEmptyEntity();

        $images_model = 'EformAttachment';

        if ($this->request->is('post')) {

            $db = $this->Administrators->getConnection();
            $db->begin();

            $administrator = $this->Administrators->patchEntity($administrator, $this->request->getData());
            $administrator->password = $this->Administrators->create_admin_password($administrator->password);

            if ($model = $this->Administrators->save($administrator)) {

                // save roles
                $roles = $this->request->getData()['roles'];
                $centers = $this->request->getData()['centers'];
                $temp = array();

                foreach ($roles as $value) {
                    $temp[] = array(
                        'administrator_id'  => $model->id,
                        'role_id'           => $value,
                    );
                }

                $obj_AdministratorsRoles = TableRegistry::get('AdministratorsRoles');
                $data_AdministratorsRoles = $obj_AdministratorsRoles->newEntities($temp);

                if ($obj_AdministratorsRoles->saveMany($data_AdministratorsRoles)) {
                    $temp_center = [];
                    foreach ($centers as $center) {
                        $temp_center[] = [
                            'administrator_id' => $model->id,
                            'center_id'         => $center
                        ];
                    }
                    $obj_AdminManageCenters = TableRegistry::get('AdministratorManageCenters');
                    $data_AdminManageCenters = $obj_AdminManageCenters->newEntities($temp_center);
                    if ($obj_AdminManageCenters->saveMany($data_AdminManageCenters)) {
                        $db->commit();
                        $this->Flash->success(__('The administrator has been saved.'));
                        $this->redirect(array('action' => 'index'));
                    }
                } else {

                    $db->rollback();
                    $this->Flash->error(__('data_is_not_saved'));
                }
            } else {
                $db->rollback();
                $this->Flash->error(__('data_is_not_saved') . "!");
            }
        }
        //   $companies = $this->Administrators->Companies->CompanyLanguages->get_list($this->lang18);

        $roles =  $this->Administrators->Roles->getListRoles($this->is_admin);
        $obj_Centers = TableRegistry::get('Centers');
        $centers = $obj_Centers->get_list($this->lang18, ['Centers.enabled' => true]);

        $this->set(compact('administrator',   'roles', 'images_model', 'centers'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Administrator id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $administrator = $this->Administrators->get($id, [
            'contain' => ['Roles', 'AdministratorManageCenters'],
        ]);
        $obj_AdministratorsRoles = TableRegistry::get('AdministratorsRoles');
        $obj_AdminManageCenters = TableRegistry::get('AdministratorManageCenters');

        if ($this->request->is(['patch', 'post', 'put'])) {

            $db = $this->Administrators->getConnection();
            $db->begin();

            $administrator = $this->Administrators->patchEntity($administrator, $this->request->getData());
         
            // Remove old roles first, then add new roles for avoid duplicate 
            $obj_AdministratorsRoles->deleteAll([ 'AdministratorsRoles.administrator_id' => $id  ]);
            $obj_AdminManageCenters->deleteAll([  'AdministratorManageCenters.administrator_id' => $id ]); 

            if ($this->Administrators->save($administrator)) {
                // save administrator roles
                $roles = $this->request->getData()['roles'];
                $centers = $this->request->getData()['centers'];
 
                $temp = array();

                foreach ($roles as $value) {
                    $temp[] = array(
                        'administrator_id'  => $id,
                        'role_id'           => $value,
                    );
                }
 
                $obj_AdministratorsRoles = TableRegistry::get('AdministratorsRoles');
                $data_AdministratorsRoles = $obj_AdministratorsRoles->newEntities($temp);

                if ($obj_AdministratorsRoles->saveMany($data_AdministratorsRoles)) {

                    if ($centers) {
                        foreach ($centers as $center) {
                            $temp_center[] = [
                                'administrator_id' => $id,
                                'center_id'         => $center
                            ];
                        }
                        $data_AdminManageCenters = $obj_AdminManageCenters->newEntities($temp_center);
                        if ($obj_AdminManageCenters->saveMany($data_AdminManageCenters)) {
                            $db->commit();
                            $this->Flash->success(__('The administrator has been saved.'));
                            $this->redirect(['action' => 'logout', __('you_must_relogin_for_the_changes_to_take_effect')]); 
                        }
                    }
                    
                } else {

                    $db->rollback();
                    $this->Flash->error(__('The administrator could not be saved!'));
                    $this->redirect(['action' => 'index']);
                }
            } else {
                $db->rollback();
                $this->Flash->error(__('The administrator could not be saved. Please, try again.'));
            }
        }

        // Method get list from languages
        // $centers = $this->Administrators->Companies->CompanyLanguages->get_list($this->lang18);
        $roles = $this->Administrators->Roles->find('list', ['limit' => 200]);

        // $centers = $this->Administrators->Roles->find('list', ['limit' => 200]);
        $obj_Centers = TableRegistry::get('Centers');
        $centers = $obj_Centers->get_list($this->lang18, ['Centers.enabled' => true]);
        // Get current roles of administrators
        $currentRoles = $obj_AdministratorsRoles->getCurrentRole($id)->toArray();
        $currentCenters = $obj_AdminManageCenters->getCurrentCenter($id)->toArray();
        $this->set(compact('administrator', 'roles', 'currentRoles', 'currentCenters', 'centers'));
    }



    /**
     * admin_editPassword method
     * vilh - edit password administrator
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function editPassword($id = null)
    {
        // debug($id);exit;
        $administrator = $this->Administrators->get($id);

        if (!$administrator) {
            $this->viewBuilder()->setTemplatePath('Error/InvalidData');
            return;
        }

        // post
        if ($this->request->is('post') || $this->request->is('put')) {

            $data = $this->request->getData();
            $result = $this->Administrators->updateNewPassword($id, $data['oldPassword'], $data['newPassword']);
            $result['status'] == true ? $this->Flash->success($result['message']) : $this->Flash->error($result['message']);
            $this->redirect(['action' => 'logout', __('you_must_relogin_for_the_changes_to_take_effect')]);
        }

        $this->set(compact('administrator'));
    }

    public function accountInfo($id = null)
    {
        $administrator = $this->Administrators->get($id, [
            'contain' => ['AdministratorsAvatars'],
        ]);

        $url = Router::url('/', true);
        if ($this->request->is(['patch', 'post', 'put'])) {

            $data = $this->request->getData();


            $db = $this->Administrators->getConnection();
            $administrator = $this->Administrators->patchEntity($administrator, $this->request->getData());
            if ($this->Administrators->save($administrator)) {

                if (isset($data['AdministratorsAvatars']) && !empty($data['AdministratorsAvatars'])) {

                    // nothing
                    if ($data['AdministratorsAvatars'][0]['image']->getSize() == 0) {
                        goto save_data;
                    }

                    $relative_path = 'uploads' . DS . 'AdministratorImages';
                    $file_name_suffix = "image";
                    $file = $data['AdministratorsAvatars'][0]['image'];

                    $uploaded = $this->Common->upload_images($file, $relative_path, $file_name_suffix, 1);

                    if ($uploaded) {
                        $image = array(
                            'administrator_id'  => $id,
                            'path'              => $uploaded['path'],
                            'size'              => $uploaded['size'],
                            'width'             => $uploaded['width'],
                            'height'            => $uploaded['height'],
                        );

                        $image = $this->Administrators->AdministratorsAvatars->newEntity($image);
                        if (!empty($image)) {
                            // delete old first
                            $this->Administrators->AdministratorsAvatars->deleteAll(
                                ['AdministratorsAvatars.administrator_id' =>  $id]
                            );

                            if (!$this->Administrators->AdministratorsAvatars->save($image)) {
                                $db->rollback();
                                $this->Flash->error(__('data_is_not_saved'));
                            }
                        }
                    }
                }
                save_data:
                $db->commit();
                $this->Flash->success(__('data_is_saved'));
                return $this->redirect(['action' => 'logout', __('you_must_relogin_for_the_changes_to_take_effect')]);
            }
            $this->Flash->error(__('The administrator could not be saved. Please, try again.'));
        }
        load_data:
        $this->set(compact('administrator'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Administrator id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $administrator = $this->Administrators->get($id);
        if ($this->Administrators->delete($administrator)) {
            $this->Flash->success(__('The administrator has been deleted.'));
        } else {
            $this->Flash->error(__('The administrator could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }


    public function login()
    {
        $this->viewBuilder()->setLayout('admin/login');
        $administrator = $this->Administrators->newEmptyEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            $logged_user = $this->Administrators->login($data['email'], $data['password']);

            if (isset($logged_user['status']) && ($logged_user['status'] == true)) {
                $current_user = $logged_user['params'];
 
                $session = $this->request->getSession();
                $session->write('administrator.id', $current_user->id);
                $session->write('administrator.current', $current_user);
                $session->write('administrator.is_manage_all_center_data', $current_user->is_manage_all_center_data);
                $session->write('administrator.list_centers_management', $current_user->list_centers_management);

                if ($this->request->getQuery('last_url') && !empty($this->request->getQuery('last_url'))) {
                    $this->redirect($this->request->getQuery('last_url'));
                } else {
                    $this->redirect(array(
                        'controller' => 'CidcParents',
                        'action' => 'index',
                        'admin' => true
                    ));
                }
            } else {
                $this->Flash->error($logged_user['message']);
            }
        }

        $this->set(compact('administrator'));
    }

    public function logout($message = null)
    {
        $this->layout = $this->autoRender = false;

        $obj_session =  $this->request->getSession();
        if ($obj_session->read('administrator')) {


            $administrator = $this->Administrators->newEmptyEntity();
            $administrator->id = $obj_session->read('administrator.id');
            $administrator->token = null;

            $this->Administrators->save($administrator);

            $obj_session->delete('Administrator.id');
            $obj_session->destroy();
        }

        $msg_logout = $message ? $message : __d('administration', 'user_is_logged_out');
        $this->Flash->success($msg_logout, 'flash/success');

        $this->redirect(array(
            'controller' => 'administrators',
            'action' => 'login',
            'admin' => true
        ));
    }
}

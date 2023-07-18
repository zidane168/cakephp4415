<?php

declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
// namespace App\Controller;
namespace App\Controller\Admin;

use Cake\Controller\Controller;
use Cake\Routing\Router;
use Cake\Event\EventInterface;
use Cake\I18n\I18n;
use Cake\ORM\TableRegistry;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    public $default_language    = 'en_US';
    public $available_language = array(
        'en_US',
        'zh_HK', 
    );
    public $lang18              = '';
    public $is_admin            = true;
    private $allow_actions = array('index', 'add', 'edit', 'view', 'delete', 'approve');
    public static $session_administrator_id = null;
    public static $session_is_manage_all_center_data = false; 

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
 
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Common');

        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/4/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');
    }

    private function redirect_to_current_url()
    {

        /***** Start Secure on web ******/
        $controller  = $this->request->getParam("controller");
        $action      = $this->request->getParam("action");
        $prefix      = strtolower($this->request->getParam("prefix"));

        $current_url = "/" . $prefix . "/" . $controller . "/" . $action;
      

        return $this->redirect(Router::url(array(
            'controller' => 'administrators',
            'action' => 'login',
            'admin' => true,
            '?' => array('last_url' => $current_url)
        ), true));
    }

    public function beforeFilter(EventInterface $event)
    {
        
        $this->viewBuilder()->setLayout('admin/default');

        if (
            strtolower($this->request->getParam("controller")) == "administrators" &&
            (strtolower($this->request->getParam("action")) == "login" || strtolower($this->request->getParam("action")) == "logout")
        ) {
            return;
        }

        $locale = "";
        $session = $this->request->getSession();  

        if ($this->request->is('get')) {
            $locale = $this->request->getQuery('rblanguage') && !empty($this->request->getQuery('rblanguage')) ?
                $this->request->getQuery('rblanguage') : ($session->read('Config.language') && !empty($session->read('Config.language')) ?  $session->read('Config.language') : $this->default_language);
        } else {
            $locale = ($session->read('Config.language') && !empty($session->read('Config.language')) ?  $session->read('Config.language') : $this->default_language);
        }

        I18n::setLocale($locale);
        $session->write('Config.language',  $locale);
        $this->lang18 = $locale;

        // pr ($locale);
        // exit;

        $session = $this->request->getSession();
        $session_administrator = $session->read('administrator');
        self::$session_administrator_id = $session->read('administrator.id');

        $this->set(compact('session_administrator'));

        // pr ($this->Url->build($this->getRequest()->getRequestTarget()));

        if ($session_administrator) {
            // add list center data management;
            $list_centers_management = $session_administrator['current']['list_centers_management']; 
            $is_manage_all_center_data = $session_administrator['current']['is_manage_all_center_data']; 
            self::$session_is_manage_all_center_data = $is_manage_all_center_data;

            // permissions
            $permissions = $session_administrator['current']['Permissions'];
 
            $this->set(compact('permissions', 'list_centers_management', 'is_manage_all_center_data'));

            $current_user = $session->read('administrator.current');
            $this->is_admin = isset($current_user->is_admin) ? $current_user->is_admin : false;

            // check permission in action
            $current_action         = $this->request->getParam('action');
            if ($current_action == "index") {
                $current_action = "view";
            }

            if (in_array($current_action, $this->allow_actions)) {
                $current_controller     = $this->request->getParam('controller');

                $has_permission = array_filter($permissions, function ($item) use ($current_controller, $current_action) {
                    return strtolower($item['p_controller']) == strtolower($current_controller) && isset($item[$current_action]);
                });

                if (!$has_permission) {
                    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        $this->Flash->error(__('invalid_permission'));
                        exit;
                    } else {
                        $this->viewBuilder()->setTemplatePath('Error/InvalidPermission');
                        return;
                    }
                }
            }
        } else {          // don't exist session

            $this->redirect_to_current_url();
            return;
        }
    }

    public function beforeRender(EventInterface $event)
    {
        $available_language = $this->available_language;
        $current_language = $this->lang18;

        $this->set(compact('available_language', 'current_language'));
    }

    public function addNewImageWithType()
    { // Different cakephp2, convert this to get
        $data = $this->request->getQuery();

        $this->viewBuilder()->setLayout('admin/blank');

        if ($data) {
            $images_model = $data['images_model'];

            // init imagetype
            $obj_ImageTypes = TableRegistry::get('ImageTypes');
            $imageTypes = $obj_ImageTypes->find_list(array(
                'ImageTypes.slug LIKE' => strtolower($data['base_model']) . "%"
            ), $this->lang18);

            $this->set(compact('imageTypes', 'images_model'));

            // $this->render('AddMoreControls/add_new_image_with_type');      // Admin/Courses/AddMoreControls/add_new_image_with_type.php <-- use current controller
            $this->render('/Admin/PageBEs/add_new_image_with_type');      // /Admin/AddMoreControls/add_new_image_with_type.php

        } else {
            return 'NULL';
        }
    }

    public function addNewImageNoType()
    {
        $data = $this->request->getQuery();

        $this->viewBuilder()->setLayout('admin/blank');

        if ($data) {
            $images_model = $data['images_model'];
            $this->set(compact('images_model'));
            $this->render('/Admin/PageBEs/add_new_image_no_type');
        } else {
            return 'NULL';
        }
    }

    public function addNewLanguageInput()
    {
        $data = $this->request->getQuery(); 

        $this->viewBuilder()->setLayout('admin/blank');

        if ($data) {
            $languages_model = $data['languages_model'];
            $languages_list = $data['languages_list'];
            $language_input_fields = $data['language_input_fields'];
            $languages_edit_data = isset($data['languages_edit_data']) && !empty($data['languages_edit_data']) ? $data['languages_edit_data'] : false;
            $index_items = $data['index_items'];
            $this->set(compact('languages_model', 'languages_list', 'language_input_fields', 'languages_edit_data', 'index_items'));
            $this->render('/Admin/PageBEs/add_new_language_input');
        } else {
            return 'NULL';
        }
    }

    public function check_manage_data_permission_action($id = array()) { 
      
        if ($id) {  // edit, view
            $obj_AdministratorManageCenters = TableRegistry::get('AdministratorManageCenters'); 
            $is_belong_to_current_center = $obj_AdministratorManageCenters->is_data_belong_to_current_user($id, self::$session_administrator_id);
  
            if ($is_belong_to_current_center == false) {
                $this->Flash->warning(__('incorrect_manage_data_permission'));
                return $this->redirect(['action' => 'index']); 
            }

        } else {   // add 
            if (self::$session_is_manage_all_center_data == false) {
                $this->Flash->warning(__('incorrect_manage_data_permission'));
                return $this->redirect(['action' => 'index']); 
            } 
        } 
    }

}

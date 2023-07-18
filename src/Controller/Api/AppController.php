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
namespace App\Controller\Api; // vilh important for split AppController backend/frontend

use Cake\Controller\Controller;
use Cake\Event\EventInterface;

use Cake\I18n\I18n;
use Cake\Event\Event;
use Cake\Core\Configure;
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
    public $message = "";
    public $status = "";
    public $language = "zh_HK";
    public $user = null; 
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
        $this->loadComponent('Api');
        $this->loadComponent('Sms');
        $this->loadComponent('Email');
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Common');
    }

    public function get_user_id_from_header_token()
    {    
        // Check authentication
        $token      = $this->request->getHeaderLine(Configure::read('header.authorization'));
        if (isset($token) && !empty($token)) {
            $token      = substr($token, 7, strlen($token) - 7);        // Bearer

            if ($token) {
                $obj_Users = TableRegistry::get('Users');  
                return $obj_Users->get_user_id_by_token($token);   
            }  
        }

        return null;
      
    } 

    public function requireAuthenticate($objects)
    {  
        foreach ($objects as $value) {
            if (
                $value["controller"] == $this->request->getParam("controller") &&
                $value["action"] == $this->request->getParam("action")
            ) {

                // Check authentication
                $token      = $this->request->getHeaderLine(Configure::read('header.authorization'));
                $token      = substr($token, 7, strlen($token) - 7);        // Bearer

                if ($token) {
                    $obj_Users = TableRegistry::get('Users');

                    if (isset($value["user_role_id"]) && !empty($value["user_role_id"])) {
                        $this->user = $obj_Users->check_valid($token, $value["user_role_id"]);

                        if (!$this->user) {
                            $this->status = 501;
                            $this->message = __d('user', 'user_not_active');
                            return;
                        }
                    }

                    if (isset($value["user_role_ids"]) && !empty($value["user_role_ids"])) {
                        $flag = 0;
                        foreach ($value["user_role_ids"] as $user_role_id) {
                            $this->user = $obj_Users->check_valid($token, $user_role_id); 
 
                            if ($this->user) { 
                                $this->status = 200;
                                $this->message = __d('user', 'user_active'); 
                                return;
                            } else {
                                $flag++;
                            }
                        } 

                        if ($flag == count($value["user_role_ids"])) {  // don't exist user to fit above role
                            $this->status = 501;
                            $this->message = __d('user', 'user_not_active');
                            return;
                        }  
                    }
                   
                } else {
                    $this->status = 401;
                    $this->message = __('Unauthorized');
                    return;
                }
            }
        }
    }

    public function beforeFilter(EventInterface $event)
    {  
        // ----------- fix CORS - by ViLH
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, PUT, PATCH, DELETE, OPTIONS');

        header('Access-Control-Allow-Headers: *');
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit(0);
        }

        $this->language      = $this->request->getHeaderLine(Configure::read('header.language_key'));

        if (!isset($this->language) || empty($this->language)) {
            $this->status = 404;
            $this->message = __('missing_header') . ' Language';
            return;
        }

        // $identity = $this->Authentication->getIdentity(); 
        // if ($identity) {
        //     $this->user = $identity->getOriginalData(); 

        //     if ($this->user['enabled'] == false) {
        //         $this->status = 501; 
        //         $this->message = __d('user', 'user_not_active'); 
        //         return;
        //     }
        // }  

        $this->status = 200;
    }

    public function get_current_api_url($v1 = 'v1')
    {
        $controller = $this->request->getParam('controller');
        $action = $this->request->getParam('action');
        return '\api\\' . $v1 . '\\' . $controller . '\\' . $action . '.json';
    }

    // $level =
    //          'info'
    //          'warning'
    //          'error'
    //          'alert'
    //          'emergency'
    //          'critical'
    //          'notice'
    //          'debug'
    //          'info'
    //          'warning'
    //          'error'
    //          'alert'
    //          'emergency'
    //          'critical'
    //          'notice'
    //          'debug'
    public function write_api_log($request, $result, $data, $level = 'info')
    {
        $event = new Event('Model.Common.writeAPILog', $this, [
            'level'     => $level,
            'url'       => $this->get_current_api_url(),
            'request'   => $request,
            'response'  => $result,
            'data'      => $data,
        ]);
        $this->getEventManager()->dispatch($event);
    }

    public function show_catch_message_api($e)
    {
        $response = $this->response->withStatus(501);
        $message = json_encode($e->getMessage());
        $status = 501;

        return array(
            'status'    => $status,
            'message'   => $message,
            'response'  => $response,
        );
    }

    public function return_obj_or_null($obj)
    {
        if (!isset($obj) || empty($obj)) {
            return null;
        }
        return $obj;
    }

    public function get_payload_result()
    {

        if ($this->status == 200) {
            return [
                'status' => 200,
            ];
        }

        return [
            'message' => $this->message,
            'status' =>  $this->status,
            'response' => $this->response->withStatus($this->status),
        ];
    }
}

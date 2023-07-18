<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AppController;
use App\MyHelper\MyHelper;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;

/**
 * CidcParents Controller
 *
 * @property \App\Model\Table\CidcParentsTable $CidcParents
 * @method \App\Model\Entity\CidcParent[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CidcParentsController extends AppController
{

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $authentications = [
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "getProfile",
                'user_role_id' =>  MyHelper::PARENT,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "editParent",
                'user_role_id' =>  MyHelper::PARENT,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "getClassesByToken",
                'user_role_id' =>  MyHelper::PARENT,
            ],
            [
                'controller' => $this->request->getParam('controller'),
                'action' => "webGetClassesByToken",
                'user_role_id' =>  MyHelper::PARENT,
            ],
        ];
        $this->requireAuthenticate($authentications);
    }

    // $children_profiles = [{"gender": 1, "dob": "2020/10/08", relationship_id: 1, "number_of_siblings": 1, "caretaker": "", "special_attention_needed": "", 
    //                        "zh_HK_name": "",  "en_US_name": "",  }, {}, {}, ...]
    // $emergency_contact = {"name": "", "relationship_id": 1, "phone_number: "", "zh_HK_name": "", "en_US_name": "", }

    public function register()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 500;
        $message = "";

        $db = $this->CidcParents->getConnection();
        $db->begin();

        try {
            if ($this->request->is('post')) {

                $payload = $this->request->getData();

                if (!isset($payload['phone_number']) || empty($payload['phone_number'])) {
                    $message = __('missing_parameter') .  ' phone_number'; 
                } elseif (!isset($payload['password']) || empty($payload['password'])) {
                    $message = __('missing_parameter') .  ' password';
                } elseif (!isset($payload['gender'])) {
                    $message = __('missing_parameter') .  ' gender';
                } elseif (!isset($payload['zh_HK_name']) || empty($payload['zh_HK_name'])) {
                    $message = __('missing_parameter') .  ' zh_HK_name'; 
                } elseif (!isset($payload['en_US_name']) || empty($payload['en_US_name'])) {
                    $message = __('missing_parameter') .  ' en_US_name';
                } elseif (!isset($payload['children_profiles']) || empty($payload['children_profiles'])) {
                    $message = __('missing_parameter') .  ' children_profiles'; 
                } elseif (!MyHelper::validate_phone($payload['phone_number'])) {
                    $message = 'INVALID_PHONE';
                } else {

                    // set language/ authorization
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }
                    $this->Api->set_language($this->language);
                    // set language/ authorization

                    // add users
                    $obj_Users = TableRegistry::get('Users');
                    $result_User = [];
                    if (isset($payload['email']) && !empty($payload['email'])) {
                        $result_User = $obj_Users->add(1, $payload['phone_number'], $payload['password'], $payload['email']);

                    } else {
                        $result_User = $obj_Users->add(1, $payload['phone_number'], $payload['password']);
                    } 

                    if ($result_User['status'] != 200) {
                        $db->rollback();
                        $status     = $result_User['status'];
                        $message    = $result_User['message'];
                        goto api_result;
                    }
                    $user_id = $result_User['params']['id'];

                    $address = null;
                    if (isset($payload['address']) && !empty($payload['address'])) {
                        $address = $payload['address'];
                    }
                    // add CidcParents   
                    $result_CidcParent = $this->CidcParents->add($payload['gender'], $user_id, $payload['zh_HK_name'],  $payload['en_US_name'], $address);
                   
                    if ($result_CidcParent['status'] != 200) {
                        $db->rollback();
                        $status     = $result_CidcParent['status'];
                        $message    = $result_CidcParent['message'];
                        goto api_result;
                    }
                    $parent_id = $result_CidcParent['params']['id'];

                    // add CidcParent Feedbacks
                    if (isset($payload['feedback']) && !empty($payload['feedback'])) {
                        $obj_ParentFeedback = TableRegistry::get('ParentFeedbacks');
                        $result_ParentFeedback = $obj_ParentFeedback->add($parent_id, $payload['feedback']);
                        if ($result_CidcParent['status'] != 200) {
                            $db->rollback();
                            $status     = $result_ParentFeedback['status'];
                            $message    = $result_ParentFeedback['message'];
                            goto api_result;
                        }
                    }

                    // add Kids 
                    $result_Kid = $this->CidcParents->Kids->add($parent_id, $payload['children_profiles']);
                    $status = $result_Kid['status'];

                    if ($result_Kid['status'] != 200) {
                        $db->rollback();
                        $message = $result_Kid['message'];
                    } else {
                        $db->commit();
                        $message = __('register_successfully');
                    }
                }
            } else {
                $db->rollback();
                $message        = __('invalid_method');
                $status         = 999;
            }
        } catch (\Exception $e) {
            $db->rollback();
            $response = $this->show_catch_message_api($e);
            $this->response = $response['response'];
            $message        = $response['message'];
            $status         = $response['status'];
        }

        api_result:
        $this->Api->set_result($status, $message, $params);
        $this->Api->output($this);
    }

    public function getProfile()
    {
        $params = (object)array();
        $status = 500;
        $message = "";

        try {
            if ($this->request->is('get')) {

                // set language/ authorization
                $payload_result = $this->get_payload_result();
                if ($payload_result['status'] != 200) {
                    $status = $payload_result['status'];
                    $this->response = $this->response->withStatus($this->status);
                    $message = $this->message;
                    goto api_result;
                }
                $this->Api->set_language($this->language);
                // set language/ authorization

                $result_CidcParent = $this->CidcParents->get_by_user_id($this->user->id, $this->language);
                $cidc_parent = [];

                $default_girl   = Configure::read('host_name') .  "/img/cidckids/student/girl.svg";
                $default_boy    = Configure::read('host_name') .  "/img/cidckids/student/boy.svg";

                if ($result_CidcParent) {

                    $kids = [];
                    foreach ($result_CidcParent->kids as $kid) {
                        $avatar = $kid->gender == MyHelper::MALE ? $default_boy :  $default_girl;

                        $kids[] = [
                            'id'        => $kid->id, 
                            'relationship' => $kid->relationship->relationship_languages[0]->name,
                            'name'      => $kid->kid_languages[0]->name,
                            'dob'       => $kid->dob->format('d-m-Y'),
                            'gender'    => $kid->gender == MyHelper::MALE ? __d('parent', 'male') : __d('parent', 'female'),
                            'avatar'    => $kid->kid_images ? Configure::read('host_name') . '/' . $kid->kid_images[0]->path : $avatar,
                        ];
                    }

                    // format data;
                    $en_US_name = null;
                    $zh_HK_name = null; 
                    foreach ($result_CidcParent->cidc_parent_languages as $item) {
                        switch ($item->alias) {
                            case 'en_US':
                                $en_US_name = $item->name;
                                break;
                            case 'zh_HK':
                                $zh_HK_name = $item->name;
                                break; 

                            default:
                                break;
                        }
                    }

                    $avatar = $result_CidcParent->gender == MyHelper::MALE ? $default_boy :  $default_girl;

                    $cidc_parent = [
                        'id'        => $result_CidcParent->id,
                        'name'      => $result_CidcParent->cidc_parent_languages[0]->name,
                        'en_US_name' => $en_US_name,
                        'zh_HK_name' => $zh_HK_name, 
                        'email'     => $this->user->email,
                        'gender'    => $result_CidcParent->gender == MyHelper::MALE ? __d('parent', 'male') : __d('parent', 'female'),
                        'phone_number' => $this->CidcParents->format_phone_number($this->user->phone_number),
                        'avatar'    => $result_CidcParent->cidc_parent_images ? Configure::read('host_name') .  '/' . $result_CidcParent->cidc_parent_images[0]->path : $avatar,
                        'kids'      => $kids,
                    ];
                    $params = $cidc_parent;
                }

                $message        = __('RETRIEVE_DATA_SUCCESSFULLY');
            } else {
                $message        = __('invalid_method');
                $status         = 500;
            }
        } catch (\Exception $e) {
            $response = $this->show_catch_message_api($e);
            $this->response = $response['response'];
            $message        = $response['message'];
            $status         = $response['status'];
        }

        api_result:
        $this->Api->set_result($status, $message, $params);
        $this->Api->output($this);
    }

    public function editParent()
    {
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
  
            if ($this->request->is('post')) {
                $payload = $this->request->getData();
                // set language/ authorization
                $payload_result = $this->get_payload_result();
                if ($payload_result['status'] != 200) {
                    $status = $payload_result['status'];
                    $this->response = $this->response->withStatus($this->status);
                    $message = $this->message;
                    goto api_result;
                }

                $this->Api->set_language($this->language);
                // set language/ authorization
                $db = $this->CidcParents->getConnection();
                $db->begin();
                $result     = $this->CidcParents->edit_profile($this->user->id, $payload);
                $status    = $result['status'];
                $message    = $result['message'];
                $params     = $result['params'];
                if ($status != 200) {
                    $db->rollback();
                    goto api_result;
                }
                if (isset($payload['avatar']) && !empty($payload['avatar'])) {
                    $avatar_data = [];

                    $relative_path = 'uploads' . DS . 'ParentImages';
                    $file_name_suffix = "image";
                    $key = 1;

                    $uploaded = $this->Common->upload_images($payload['avatar'], $relative_path, $file_name_suffix, $key);
                    if ($uploaded) {
                        $temp = array(
                            'path'              => $uploaded['path'],
                            'name'              => $uploaded['re_name'],
                            'width'             => $uploaded['width'],
                            'height'            => $uploaded['height'],
                            'size'              => $uploaded['size'],
                            'cidc_parent_id'    => $params,
                        );
                        $avatar_data[] = $temp;
                    }

                    if (isset($avatar_data) && !empty($avatar_data)) {
                        $data_images = $this->CidcParents->CidcParentImages->find('all', [
                            'fields' => ['CidcParentImages.id'],
                            'conditions' => [
                                'CidcParentImages.cidc_parent_id' => $params
                            ]
                        ]);
                        $ids = [];
                        foreach ($data_images as $img) {
                            $ids[]  = $img->id;
                        }
                        // delete physical file + db
                        if ($ids) {
                            $this->CidcParents->remove_uploaded_image('CidcParentImages', $ids);
                        }
                        // save new file + db;
                        $orm_Image = $this->CidcParents->CidcParentImages->newEntities($avatar_data);
                        if (!$this->CidcParents->CidcParentImages->saveMany($orm_Image)) {
                            $db->rollback();
                            $status = 999;
                            $message = __('data_is_not_saved') . ' CidcParentImages';
                            goto api_result;
                        }
                    }
                }
                $db->commit();

                $result_CidcParent = $this->loadModel('CidcParents')->get_by_user_id($this->user->id, $this->language);
                $cidc_parent = [];

                $default_girl   = Configure::read('host_name') .  "/img/cidckids/student/girl.svg";
                $default_boy    = Configure::read('host_name') .  "/img/cidckids/student/boy.svg";
                if ($result_CidcParent) {

                    $kids = [];
                    foreach ($result_CidcParent->kids as $kid) {
                        $avatar = $kid->gender == MyHelper::MALE ? $default_boy :  $default_girl;

                        $kids[] = [
                            'id'        => $kid->id, 
                            'relationship' => [
                                'id'   => $kid->relationship->id,
                                'name' => $kid->relationship->relationship_languages[0]->name
                            ],
                            'name'      => $kid->kid_languages[0]->name,
                            'dob'       => $kid->dob->format('d-m-Y'),
                            'gender'    => $kid->gender == MyHelper::MALE ? __d('parent', 'male') : __d('parent', 'female'),
                            'gender_id' => (int)$kid->gender,
                            'avatar'    => $kid->kid_images ? Configure::read('host_name') . '/' . $kid->kid_images[0]->path : $avatar,
                        ];
                    }

                    // format data;
                    $en_US_name = null;
                    $zh_HK_name = null; 
                    foreach ($result_CidcParent->cidc_parent_languages as $item) {
                        switch ($item->alias) {
                            case 'en_US':
                                $en_US_name = $item->name;
                                break;
                            case 'zh_HK':
                                $zh_HK_name = $item->name;
                                break; 

                            default:
                                break;
                        }
                    }

                    $avatar = $result_CidcParent->gender == MyHelper::MALE ? $default_boy :  $default_girl;

                    $cidc_parent = [
                        'id'        => $result_CidcParent->id,
                        'name'      => $result_CidcParent->cidc_parent_languages[0]->name,
                        'en_US_name' => $en_US_name,
                        'zh_HK_name' => $zh_HK_name, 
                        'email'     => $this->user->email,
                        'gender'    => $result_CidcParent->gender == MyHelper::MALE ? __d('parent', 'male') : __d('parent', 'female'),
                        'gender_id' => (int)$result_CidcParent->gender,
                        'phone_number' => $this->CidcParents->format_phone_number($this->user->phone_number),
                        'avatar'    => $result_CidcParent->cidc_parent_images ? Configure::read('host_name') .  '/' . $result_CidcParent->cidc_parent_images[0]->path : $avatar,
                        'kids'      => $kids,
                        'user_role_id' => $this->user->user_role_id,
                        'max_kids'  => MyHelper::MAX_KIDS,
                        'address'   => $result_CidcParent->address ? $result_CidcParent->address : ""
                    ];
                    $params = $cidc_parent;
                }
            } else {
                $message        = __('invalid_method');
                $status         = 500;
            }
        } catch (\Exception $e) {
            $response = $this->show_catch_message_api($e);
            $this->response = $response['response'];
            $message        = $response['message'];
            $status         = $response['status'];
        }
        api_result:
        $this->Api->set_result($status, $message, $params);
        $this->Api->output($this);
    }

    public function getClassesByToken()
    {
        $params = array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('get')) {
                $payload = $this->request->getQuery();
                // set language/ authorization
                $payload_result = $this->get_payload_result();
                if ($payload_result['status'] != 200) {
                    $status = $payload_result['status'];
                    $this->response = $this->response->withStatus($this->status);
                    $message = $this->message;
                    goto api_result;
                }
                $this->Api->set_language($this->language);
                // set language/ authorization
                $db = $this->CidcParents->getConnection();
                $db->begin();

                $kid_ids = $this->CidcParents->Kids->get_kid_ids([
                    'Users.id'   => $this->user->id,
                    'Kids.enabled'          =>  true
                ]);
                if (!$kid_ids) {
                    $status = 200;
                    $message = "RETRIEVE_DATA_SUCCESSFULLY";
                    goto api_result;
                }
                $message = "RETRIEVE_DATA_SUCCESSFULLY";
                $type = (isset($payload['type']) && !empty($payload['type'])) ? $payload['type'] : null;
                $kid_id = null;
                if (isset($payload['kid_id']) && !empty($payload['kid_id'])) {
                    if (!in_array($payload['kid_id'], $kid_ids)) {
                        $message = 'KID_NOT_FOUND';
                        $params = [];
                        goto api_result;
                    }
                    $kid_id = $payload['kid_id'];
                }
                $params = $this->CidcParents->Kids->get_kids_classes($this->language, $kid_ids, $this->user->id, $type, $kid_id);
                $db->commit();
            } else {
                $message        = __('invalid_method');
                $status         = 500;
            }
        } catch (\Exception $e) {
            $response = $this->show_catch_message_api($e);
            $this->response = $response['response'];
            $message        = $response['message'];
            $status         = $response['status'];
        }
        api_result:
        $this->Api->set_result($status, $message, $params);
        $this->Api->output($this);
    }

    public function webGetClassesByToken()
    {
        $params = array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('get')) {
                $payload = $this->request->getQuery();

                if (!isset($payload['page']) || empty($payload['page'])) {
                    $message = __('missing_parameter') . ' page';
                } elseif (!isset($payload['limit']) || empty($payload['limit'])) {
                    $message = __('missing_parameter') . ' limit';
                } else {
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }
                    $this->Api->set_language($this->language);
                    // set language/ authorization
                    $db = $this->CidcParents->getConnection();
                    $db->begin();

                    $kid_ids = $this->CidcParents->Kids->get_kid_ids([
                        'Users.id'   => $this->user->id,
                        'Kids.enabled'          =>  true
                    ]);
                    if (!$kid_ids) {
                        $status = 200;
                        $message = "RETRIEVE_DATA_SUCCESSFULLY";
                        goto api_result;
                    }
                    $message = "RETRIEVE_DATA_SUCCESSFULLY";
                    $type = (isset($payload['type']) && !empty($payload['type'])) ? $payload['type'] : 'UPCOMMING';
                    $params = $this->CidcParents->Kids->web_get_kids_classes($this->language, $kid_ids, $payload, $type);
                    $db->commit();
                }
                // set language/ authorization

            } else {
                $message        = __('invalid_method');
                $status         = 500;
            }
        } catch (\Exception $e) {
            $response = $this->show_catch_message_api($e);
            $this->response = $response['response'];
            $message        = $response['message'];
            $status         = $response['status'];
        }
        api_result:
        $this->Api->set_result($status, $message, $params);
        $this->Api->output($this);
    }
}

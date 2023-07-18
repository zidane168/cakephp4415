<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AppController;
use Cake\Event\EventInterface;

class NewsController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
    }

    public function getListPagination()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 999;
        $message = "";
        try {
            if ($this->request->is('get')) {
                $payload = $this->request->getQuery();
                if (!isset($payload['limit']) || empty($payload['limit'])) {
                    $message = __('missing_parameter') . ' limit';
                } elseif (!isset($payload['page']) || empty($payload['page'])) {
                    $message = __('missing_parameter') . ' page';
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


                    $this->Api->set_language($this->language);
                    $params = $this->News->get_list_pagination($this->language, $payload);
                    $message = 'RETRIEVE_DATA_SUCCESSFULLY';
                }
            }
        } catch (\Exception $ex) {
            $this->response = $this->response->withStatus(501);
            $message = $ex->getMessage();
            $status = 501;
        }

        api_result:
        $this->Api->set_result($status, $message, $params);
        $this->Api->output($this);
    }

    public function getNewsById()
    {
        $this->Api->init();
        $params = null;
        $status = 999;
        $message = "";
        try {
            if ($this->request->is('get')) {
                $payload = $this->request->getQuery();
                if (!isset($payload['id']) || empty($payload['id'])) {
                    $message = __('missing_parameter') . ' id';
                    goto api_result;
                } else {
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }

                    $this->Api->set_language($this->language);
                    $news = $this->News->get_by_id($payload['id'], $this->language);
                    if (!$news) {
                        $message = "NOT_FOUND_NEWS";
                        goto api_result;
                    }
                    $message = "RETRIEVE_DATA_SUCCESSFULLY";
                    $params = $news;
                }
            }
        } catch (\Exception $ex) {
            $this->response = $this->response->withStatus(501);
            $message = $ex->getMessage();
            $status = 501;
        }
        api_result:
        $this->Api->set_result($status, $message, $params);
        $this->Api->output($this);
    }

    public function createNews()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 999;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();
                if (!isset($payload['date']) || empty($payload['date'])) {
                    $message = __('missing_parameter') . ' date';
                    goto api_result;
                } elseif (!isset($payload['name']) || empty($payload['name'])) {
                    $message = __('missing_parameter') . ' name';
                    goto api_result;
                } else {
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }
                    $this->Api->set_language($this->language);
                    $result = $this->News->create_news($payload);
                    $message = $result['message'];
                    $params  = $result['params'];
                }
            }
        } catch (\Exception $ex) {
            $this->response = $this->response->withStatus(501);
            $message = $ex->getMessage();
            $status = 501;
        }
        api_result:
        $this->Api->set_result($status, $message, $params);
        $this->Api->output($this);
    }

    public function editNews()
    {
        // debug(1);exit;
        $this->Api->init();
        $params = (object)array();
        $status = 999;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();
                if (!isset($payload['id']) || empty($payload['id'])) {
                    $message = __('missing_parameter') . ' id';
                    goto api_result;
                } elseif (!isset($payload['date']) || empty($payload['date'])) {
                    $message = __('missing_parameter') . ' date';
                    goto api_result;
                } elseif (!isset($payload['name']) || empty($payload['name'])) {
                    $message = __('missing_parameter') . ' name';
                    goto api_result;
                } else {
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }
                    $this->Api->set_language($this->language);
                    $news = $this->News->get_by_id($payload['id']);
                    // debug($news->toArray());exit;
                    if (!$news) {
                        $message = "NOT_FOUND_COURSE";
                        goto api_result;
                    }

                    $result = $this->News->edit_news($payload);
                    $message = $result['message'];
                    $params  = $result['params'];
                }
            }
        } catch (\Exception $ex) {
            $this->response = $this->response->withStatus(501);
            $message = $ex->getMessage();
            $status = 501;
        }
        api_result:
        $this->Api->set_result($status, $message, $params);
        $this->Api->output($this);
    }

    public function deleteNews()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 999;
        $message = "";
        try {
            if ($this->request->is('delete')) {
                $payload = $this->request->getQuery();
                if (!isset($payload['id']) || empty($payload['id'])) {
                    $message = __('missing_parameter') . ' id';
                } else {
                    $news = $this->News->get_by_id($payload['id']);
                    if (!$news) {
                        $message = "NOT_FOUND_COURSE";
                        goto api_result;
                    }
                    $message = $this->News->delete_by_id($payload['id']);
                }
            }
        } catch (\Exception $ex) {
            $this->response = $this->response->withStatus(501);
            $message = $ex->getMessage();
            $status = 501;
        }
        api_result:
        $this->Api->set_result($status, $message, $params);
        $this->Api->output($this);
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AppController;
use Cake\Event\EventInterface;

class CentersController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
    }

    public function getListPagination()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('get')) {
                $payload = $this->request->getQuery();

                if (!isset($payload['limit']) || empty($payload['limit'])) {
                    $message = __('missing_parameter') . ' limit';
                } elseif (!isset($payload['page']) || empty($payload['page'])) {
                    $message = __('missing_parameter') . ' page';
                } else {
                    $payload_result = $this->get_payload_result();
                    if ($payload_result['status'] != 200) {
                        $status = $payload_result['status'];
                        $this->response = $this->response->withStatus($this->status);
                        $message = $this->message;
                        goto api_result;
                    }
                    $this->Api->set_language($this->language);

                    $params = $this->Centers->get_list_pagination($this->language, $payload);
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

    public function getCenterById()
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
                    $course = $this->Centers->get_by_id($payload['id'], $this->language);
                    if (!$course) {
                        $message = "NOT_FOUND_CENTER";
                        goto api_result;
                    }
                    $message = "RETRIEVE_DATA_SUCCESSFULLY";
                    $params = $course;
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

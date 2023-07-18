<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AppController;
use Cake\Event\EventInterface;

class ProgramsController extends AppController
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
                    goto api_result;
                } elseif (!isset($payload['page']) || empty($payload['page'])) {
                    $message = __('missing_parameter') . ' page';
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
                    $params = $this->Programs->get_list_pagination($this->language, $payload);
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

    public function getProgramById()
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
                    $program = $this->Programs->get_by_id($payload['id'], $this->language);
                    if (!$program) {
                        $message = "NOT_FOUND_PROGRAM";
                        goto api_result;
                    }
                    $message = "RETRIEVE_DATA_SUCCESSFULLY";
                    $params = $program;
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

    public function createProgram()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 999;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();

                if (!isset($payload['title_color']) || empty($payload['title_color'])) {
                    $message = __('missing_parameter') . ' title_color';
                } elseif (!isset($payload['background_color']) || empty($payload['background_color'])) {
                    $message = __('missing_parameter') . ' background_color';
                } elseif (!isset($payload['name']) || empty($payload['name'])) {
                    $message = __('missing_parameter') . ' name';
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
                    $result = $this->Programs->create_program($payload);
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

    public function editProgram()
    {
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
                } elseif (!isset($payload['title_color']) || empty($payload['title_color'])) {
                    $message = __('missing_parameter') . ' title_color';
                    goto api_result;
                } elseif (!isset($payload['background_color']) || empty($payload['background_color'])) {
                    $message = __('missing_parameter') . ' background_color';
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
                    $program = $this->Programs->get_by_id($payload['id']);
                    if (!$program) {
                        $message = "NOT_FOUND_PROGRAM";
                        goto api_result;
                    }

                    $result = $this->Programs->edit_program($payload);
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

    public function deleteProgram()
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
                    goto api_result;
                } else {
                    $program = $this->Programs->get_by_id($payload['id']);
                    if (!$program) {
                        $message = "NOT_FOUND_PROGRAM";
                        goto api_result;
                    }
                    $message = $this->Programs->delete_by_id($payload['id']);
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

    public function discover()
    {
        $this->Api->init();
        $params = [];
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

                $payload = $this->request->getQuery();

                $programs = $this->Programs->discover($this->language, $payload);
                $status     = 200;
                $message    = __('retrieve_data_successfully');
                $params     = $programs;
            } else {
                $message        = __('invalid_method');
                $status         = 999;
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

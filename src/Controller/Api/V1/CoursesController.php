<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AppController;
use Cake\Event\EventInterface;

class CoursesController extends AppController
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
                    $params = $this->Courses->get_list_pagination($this->language, $payload);
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

    public function getCourseById()
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
                    $course = $this->Courses->get_by_id($payload['id'], $this->language);
                    if (!$course) {
                        $message = "NOT_FOUND_COURSE";
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

    public function createCourse()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 999;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();
                if (!isset($payload['program_id']) || empty($payload['program_id'])) {
                    $message = __('missing_parameter') . ' program_id';
                    goto api_result;
                } elseif (!isset($payload['age_range_from']) || empty($payload['age_range_from'])) {
                    $message = __('missing_parameter') . ' age_range_from';
                    goto api_result;
                } elseif (!isset($payload['age_range_to']) || empty($payload['age_range_to'])) {
                    $message = __('missing_parameter') . ' age_range_to';
                    goto api_result;
                } elseif (!isset($payload['unit']) || empty($payload['unit'])) {
                    $message = __('missing_parameter') . ' unit';
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
                    $program = $this->Courses->Programs->get_by_id($payload['program_id']);
                    if (!$program) {
                        $message = "NOT_FOUND_PROGRAM";
                        goto api_result;
                    }
                    $result = $this->Courses->create_course($payload);
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

    public function editCourse()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 999;
        $message = "";
        try {
            if ($this->request->is('post')) {
                $payload = $this->request->getData();
                if (!isset($payload['program_id']) || empty($payload['program_id'])) {
                    $message = __('missing_parameter') . ' program_id';
                    goto api_result;
                } elseif (!isset($payload['course_id']) || empty($payload['course_id'])) {
                    $message = __('missing_parameter') . ' course_id';
                    goto api_result;
                } elseif (!isset($payload['age_range_from']) || empty($payload['age_range_from'])) {
                    $message = __('missing_parameter') . ' age_range_from';
                    goto api_result;
                } elseif (!isset($payload['age_range_to']) || empty($payload['age_range_to'])) {
                    $message = __('missing_parameter') . ' age_range_to';
                    goto api_result;
                } elseif (!isset($payload['unit']) || empty($payload['unit'])) {
                    $message = __('missing_parameter') . ' unit';
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
                    $program = $this->Courses->Programs->get_by_id($payload['program_id']);
                    if (!$program) {
                        $message = "NOT_FOUND_PROGRAM";
                        goto api_result;
                    }
                    $course = $this->Courses->get_by_id($payload['course_id']);
                    if (!$course) {
                        $message = "NOT_FOUND_COURSE";
                        goto api_result;
                    }

                    $result = $this->Courses->edit_course($payload);
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

    public function deleteCourse()
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
                    $course = $this->Courses->get_by_id($payload['id']);
                    if (!$course) {
                        $message = "NOT_FOUND_COURSE";
                        goto api_result;
                    }
                    $message = $this->Courses->delete_by_id($payload['id']);
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

    public function dataselect()
    {

        if ($this->request->is('get')) {

            $this->Api->init();
            $data = $this->request->getQuery();
            $message = "";
            $status = false;
            $params = array();

            $conditions = array(
                'Courses.program_id' => $data['id'],
            );

            $message = __('retrieve_data_successfully');
            $status  = true;
            $params  = $this->Courses->get_list($data['language'], $conditions);

            $this->Api->set_result($status, $message, $params);
        }
        $this->Api->output($this);
    }

    public function filter()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 500;
        $message = "";
        try {
            if ($this->request->is('get')) {
                $this->Api->set_language($this->language);

                $this->Api->set_language($this->language);
                $params = $this->Courses->filter($this->language);
                $message = 'RETRIEVE_DATA_SUCCESSFULLY';
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

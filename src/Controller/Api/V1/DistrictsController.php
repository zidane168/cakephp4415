<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AppController;
use Cake\Event\EventInterface;

/**
 * ChildCategories Controller
 *
 * @property \App\Model\Table\ChildCategoriesTable $ChildCategories
 * @method \App\Model\Entity\ChildCategory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DistrictsController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
    }

    // http://localhost/ecpark-portal/api/districts/dataselect.json?id=1&language=en_US
    public function dataselect()
    {

        if ($this->request->is('get')) {

            $this->Api->init();
            $data = $this->request->getQuery();
            $message = "";
            $status = false;
            $params = array();

            if (!isset($data['id']) || empty($data['id'])) {
                $message = __('missing_parameter') .  ' id';
            } elseif (!isset($data['language']) || empty($data['language'])) {
                $message = __('missing_parameter') .  ' language';
            } else {
                $conditions = array(
                    'Districts.region_id' => $data['id'],
                );
                $message = __('retrieve_data_successfully');
                $status  = true;
                $params  = $this->Districts->get_list($data['language'], $conditions);
            }

            $this->Api->set_result($status, $message, $params);
        }
        $this->Api->output($this);
    }

    public function getList()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 500;
        $message = "";

        try {
            if ($this->request->is('get')) {

                $payload = $this->request->getQuery();
                if (!isset($payload['language']) || empty($payload['language'])) {
                    $message = __('missing_parameter') .  ' language';
                } elseif (!isset($payload['region_id']) || empty($payload['region_id'])) {
                    $message = __('missing_parameter') .  ' region_id';
                } else {
                    $this->Api->set_language($payload['language']);

                    $response = $this->Districts->get_list($payload['language'], [
                        'Districts.region_id' => $payload['region_id']
                    ]);

                    $status     = 200;
                    $message    = __('retrieve_data_successfully');

                    if (!empty($response->toArray())) {
                        $params     = $response;
                    }

                    $this->write_api_log($payload, $response, json_encode($response));
                }
            } else {
                $message        = __('invalid_method');
                $status         = 999;
            }
        } catch (\Exception $ex) {
            $this->response = $this->response->withStatus(501);
            $message = $ex->getMessage();
            $status = 501;
        }

        $this->Api->set_result($status, $message, $params);
        $this->Api->output($this);
    }

    public function getListPagination()
    {
        $this->Api->init();
        $params = [];
        $status = 500;
        $message = "";

        try {
            if ($this->request->is('get')) {

                $payload = $this->request->getQuery();

                if (!isset($payload['language']) || empty($payload['language'])) {
                    $message = __('missing_parameter') .  ' language';
                } elseif (!isset($payload['region_id']) || empty($payload['region_id'])) {
                    $message = __('missing_parameter') .  ' region_id';
                } elseif (!isset($payload['limit']) || empty($payload['limit'])) {
                    $message = __('missing_parameter') .  ' limit';
                } elseif (!isset($payload['page']) || empty($payload['page'])) {
                    $message = __('missing_parameter') .  ' page';
                } else {
                    $this->Api->set_language($payload['language']);

                    $response = $this->Districts->get_list_pagination($payload);

                    $status     = 200;
                    $message    = __('retrieve_data_successfully');
                    $params     = $response;
                }
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

        $this->Api->set_result($status, $message, $params);
        $this->Api->output($this);
    }
}

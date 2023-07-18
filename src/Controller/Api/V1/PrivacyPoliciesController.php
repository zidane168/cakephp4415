<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AppController;
use Cake\Event\EventInterface;

class PrivacyPoliciesController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
    }

    public function getDetail()
    {
        $this->Api->init();
        $params = (object)array();
        $status = 999;
        $message = "";
        try {
            if ($this->request->is('get')) {
                $this->Api->set_language($this->language);
                $params = $this->PrivacyPolicies->get_detail($this->language);
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

<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AppController;
use Cake\Event\EventInterface;

class AlbumsController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
    }

    public function getAlbums()
    {
        $this->Api->init();
        $params = [];
        $status = 999;
        $message = "";
        try {
            if ($this->request->is('get')) {
                $payload = $this->request->getQuery();
                if (!isset($payload['limit']) || empty($payload['limit'])) {
                    $message = __('missing_parameter') . ' limit';
                } elseif (!isset($payload['page']) || empty($payload['page'])) {
                    $message = __('missing_parameter') . ' page';
                } elseif (!isset($payload['cidc_class_id']) || empty($payload['cidc_class_id'])) {
                    $message = __('missing_parameter') . ' cidc_class_id';
                } else {
                    $this->Api->set_language($this->language);
                    $params = $this->Albums->get_albums($payload);
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
}

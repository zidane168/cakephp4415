<?php

// ---------------------------------------------------------------------------------------------------------
// -- Author:       ViLH
// -- Description:  Save Session who Edit the record
// ---------------------------------------------------------------------------------------------------------

namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Behavior;
use Cake\Event\Event;
use App\Controller\Admin\AppController;
use Cake\Utility\Text;


class AuditBehavior extends Behavior {

    private static $config = [
        'autoBind'          => true,
    ];

    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options) {

        $session_administrator_id = AppController::$session_administrator_id;   // get value from AppController

        if (self::$config['autoBind']) {
            if (empty($entity->id)) {        //Log::debug('is going to insert');
                $entity->created_by = $session_administrator_id;
            
            } else {                          //Log::debug('is going to update');
                $entity->modified_by = $session_administrator_id;
            }
        } 
    }


}
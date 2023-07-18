<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\I18n\I18n;
use ErrorException;
use PHPUnit\Runner\DefaultTestResultCache;

use function PHPUnit\Framework\throwException;

class ApiComponent extends Component
{

    private $params = array();
    private $status = false;

    private $result;

    public function init()
    {
        $this->result = array(
            'status'     => $this->status,
            'message'     => __("please_provide_information"),
            'params'     => $this->params,
        );
    }

    public function set_result($status = false, $message, $params)
    {
        // set the final result
        switch ($message) {

            case 'RETRIEVE_DATA_SUCCESSFULLY':
                $status     = 200;
                $message    = __('retrieve_data_successfully');
                break;

            case 'DATA_IS_SAVED':
                $status     = 200;
                $message    = __('data_is_saved');
                break;
            case 'VALID_DATA':
                $status     = 200;
                $message    = __('data_valid');
                break;

            case 'DATA_WAS_UPDATED':
                $status     = 200;
                $message    = __('data_was_updated');
                break;

            case 'VALID_CODE':
                $status     = 200;
                $message    = __('valid_code');
                break;
            case 'VALID_TOKEN':
                $status     = 200;
                $message    = __('valid_token');
                break;

            case 'DATA_IS_NOT_SAVED':
                $status     = 999;
                $message    = __('data_is_not_saved');
                break;

            case 'DATA_IS_DELETED':
                $status     = 200;
                $message    = __('data_is_deleted');
                break;

            case 'DATA_IS_NOT_DELETED':
                $status     = 999;
                $message    = __('data_is_not_deleted');
                break;

            case 'NOT_FOUND_COURSE':
                $status     = 999;
                $message    = __('not_found') . ' ' . __d('center', 'course');
                break;

            case 'NOT_FOUND_PROGRAM':
                $status     = 999;
                $message    = __('not_found') . ' ' . __d('center', 'program');
                break;

            case 'NOT_FOUND_PROFESSIONAL':
                $status     = 999;
                $message    = __('not_found') . ' ' . __d('professional', 'professional');
                break;

            case 'REGISTER_CLASS_SUCCESS':
                $status     = 200;
                $message    = __d('cidcclass', 'register_class_success');
                break;

            case 'INVALID_ORDER':
                $status     = 500;
                $message    = __d('order', 'invalid_order');
                break;

            case 'REGISTER_CLASS_SUCCESS_WITHOUT_MESSAGE':
                $status     = 200;
                $message    = __d('cidcclass', 'register_class_success') .  ' but without message';
                break;

            case 'INVALID_CODE':
                $status     = 401;
                $message    = __('invalid_code');
                break;

            case 'INVALID_TOKEN':
                $status     = 401;
                $message    = __('invalid_token');
                break;


            default:
                break;
        }

        $this->result = array(
            'status'        => $status,
            'message'       => $message,
            'params'        => $params
        );
    }

    public function output($obj_controller, $values = array())
    {
        if (empty($values)) {
            $values = $this->result;
        }

        $values = $this->replace_null($values);

        $result = $this->result;
        $obj_controller->set(compact('result'));
        $obj_controller->viewBuilder()->setOption('serialize', 'result');
    }

    /**
     * Public function to set the language
     */
    public function set_language($language = "zh_HK")
    {
        I18n::setLocale($language);
    }

    public function replace_null($data)
    {
        array_walk_recursive($data, function (&$item, $key) {
            if (is_null($item)) {
                $item = "";
            }
        });

        return $data;
    }

    public function validate($api, $payload)
    {
        $arrFieldsRequired = [];
        $arrValueSpecial =  [0, "0", false, null];

        switch ($api) {

                // relationship/
            case "RelationshipGetList":
                $arrFieldsRequired = ["language"];
                break;

                // feedback/
            case "FeedbackGetList":
                $arrFieldsRequired = ["language"];
                break;

                // course
            case "CourseGetList":
                $arrFieldsRequired = ["language"];
                break;

                // program
            case "ProgramGetList":
                $arrFieldsRequired = ["language"];
                break;

                //center
            case "CenterGetList":
                $arrFieldsRequired = ["language"];
                break;

            default:
                throw new ErrorException("Need to check argument api");
        }
        foreach ($arrFieldsRequired as $field) {
            if ((!isset($payload[$field]) ||
                (empty($payload[$field]) && !in_array($payload[$field], $arrValueSpecial)))) {
                return [
                    'isValid' => false,
                    'field'   => $field
                ];
            }
        }
        return [
            'isValid' => true,
            'field'   => ''
        ];
    }

    public function get_value_input($field, $payload)
    {
        if ((!isset($payload[$field]) || empty($payload[$field]))) {
            return null;
        } else {
            return $payload[$field];
        }
    }
}

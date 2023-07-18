<?php

declare(strict_types=1);

namespace App\MyHelper;

use Cake\Routing\Router;
use Cake\Filesystem\File;

class MyHelper
{
    // user role
    public const PARENT = 1;
    public const STAFF = 2;

    public const MALE   = 1;
    public const FEMALE = 0;
    public static function getGenders()
    {
        return [
            MyHelper::FEMALE => __d('parent', 'female'),
            MyHelper::MALE => __d('parent', 'male')
        ];
    }

    public static function getUnits()
    {
        return [
            1 => __d('center', 'months'),
            2 => __d('center', 'years')
        ];
    } 

    public const PAID = 1;
    public const UNPAID = 0;
    public static function getStatusPaidUnpaid()
    {
        return [
            MyHelper::PAID => __d('cidcclass', 'paid'),
            MyHelper::UNPAID => __d('cidcclass', 'unpaid')
        ];
    }


    public static function getUrl()
    {
        $url =  Router::url('/', true);
        return $url;
    }

    public static function validate_phone($phone)
    {
        return preg_match('/^(2|3|4|5|6|8|9)\d{7}$/', $phone);
    }

    public const PENDING = 0;
    public const APPROVAL = 1;
    public static function getStatusPendingApproval()
    {
        return [
            MyHelper::PENDING => __d('cidcclass', 'pending'),
            MyHelper::APPROVAL => __d('cidcclass', 'approval')
        ];
    }

    // class types
    public const CIRCULAR = 1;
    public const NONCIRCULAR = 2; 
    public static function getClassType()
    {
        return [
            MyHelper::CIRCULAR => __d('cidcclass', 'circular'),
            MyHelper::NONCIRCULAR => __d('cidcclass', 'non_circular')
        ];
    }
 
    // status attended
    public const TBD = 4;
    public const ATTENDED = 1;
    public const ABSENT = 2;
    public const ON_LEAVE = 3;

    // type upload-file
    public const SICK_LEAVE = "SICK_LEAVE";
    public const RESCHEDULE = "RESCHEDULE";

    // max kids
    public const MAX_KIDS = 8;
}

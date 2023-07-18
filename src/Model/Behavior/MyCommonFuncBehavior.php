<?php

// ---------------------------------------------------------------------------------------------------------
// -- Author:       ViLH
// -- Description:  MyCommonFuncBehavior
// ---------------------------------------------------------------------------------------------------------

namespace App\Model\Behavior;

use App\MyHelper\MyHelper;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\ORM\Behavior;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use Cake\Utility\Hash;
use Cake\I18n\FrozenTime;
use Cake\Routing\Router;


class MyCommonFuncBehavior extends Behavior
{

    protected $_table;

    // PASSWORD_DEFAULT - Use the bcrypt algorithm (default as of PHP 5.5.0). Note that this constant is designed to change over time as new and stronger algorithms are added to PHP. For that reason, the length of the result from using this identifier can change over time. Therefore, it is recommended to store the result in a database column that can expand beyond 60 characters (255 characters would be a good choice).
    // PASSWORD_BCRYPT - Use the CRYPT_BLOWFISH algorithm to create the hash. This will produce a standard crypt() compatible hash using the "$2y$" identifier. The result will always be a 60 character string, or false on failure.
    // PASSWORD_ARGON2I - Use the Argon2i hashing algorithm to create the hash. This algorithm is only available if PHP has been compiled with Argon2 support.
    // PASSWORD_ARGON2ID - Use the Argon2id hashing algorithm to create the hash. This algorithm is only available if PHP has been compiled with Argon2 support.

    // pw gen dynamic need use verify_admin_password to check it
    // every time pw gen will change another key.
    static function create_admin_password($pwd)
    {
        $pepper = Configure::read('admin.pepper');
        $pwd_peppered = hash_hmac("sha256", $pwd, $pepper);
        return password_hash($pwd_peppered, PASSWORD_DEFAULT);     //PASSWORD_ARGON2ID);
    }

    static function verify_admin_password($pwd, $pw_from_db)
    {
        $pepper = Configure::read('admin.pepper');
        $pwd_peppered = hash_hmac("sha256", $pwd, $pepper);

        if (password_verify($pwd_peppered, $pw_from_db)) {
            return true;
        }
        return false;
    }

    static function create_member_password($pwd)
    {
        $pepper = Configure::read('parent.pepper');
        $pwd_peppered = hash_hmac("sha256", $pwd, $pepper);
        return password_hash($pwd_peppered, PASSWORD_DEFAULT);     //PASSWORD_ARGON2ID);
    }

    static function create_phone_token($phone)
    {
        $pepper = Configure::read('parent.pepper');
        $datetime = (string)(strtotime(date('Y-m-d H:i:s')));
        return hash_hmac("sha256", $datetime . $phone, $pepper);
    }

    static function verify_member_password($pwd, $pw_from_db)
    {
        $pepper = Configure::read('parent.pepper');
        $pwd_peppered = hash_hmac("sha256", $pwd, $pepper);

        if (password_verify($pwd_peppered, $pw_from_db)) {
            return true;
        }
        return false;
    }

    function encrypt($password)
    {
        $plaintext = Configure::read('admin.key');
        $method = "AES-256-CBC";
        $key = hash('sha256', $password, true);
        $iv = openssl_random_pseudo_bytes(16);

        $ciphertext = openssl_encrypt($plaintext, $method, $key, OPENSSL_RAW_DATA, $iv);
        $hash = hash_hmac('sha256', $ciphertext . $iv, $key, true);

        return $iv . $hash . $ciphertext;
    }

    public function remove_uploaded_image($images_model, $image_ids = array())
    {
        $result = array(
            'status' => false,
        );

        $removed = array();

        $conditions = array(
            $images_model . '.id IN ' => $image_ids
        );

        $this->images_model = TableRegistry::get("$images_model");

        $images = $this->images_model->find('all', array(
            'fields' => array(
                $images_model . '.id',
                $images_model . '.path'
            ),
            'conditions' => $conditions,
            'recursive' => -1
        ));

        if ($images) {

            foreach ($images as $key => $image) {

                $model = $this->images_model->get($image->id);
                if ($this->images_model->delete($model)) { 
                    if (file_exists(WWW_ROOT .  $image->path)) {
                        unlink(WWW_ROOT .  $image->path);
                    } 
                }
            }
        }

        if (!empty($removed)) {
            $result = array(
                'status' => true,
            );
        }

        return $result;
    }

    public function isNull($item)
    {
        if ($item === array() || $item === '' || $item === null || empty($item)) {
            return true;
        }

        return false;
    }

    public function generate_code($length = "6")
    {
        return substr(str_shuffle("1234567890"), 0, $length);
    }

    public function generatePin($length = "6")
    {
        return substr(str_shuffle("0123456789"), 0, $length);
    }

    public function generate_transaction_id($length = "9")
    {
        return date('ym') . substr(str_shuffle("123456789"), 0, $length);
    }

    public function generateToken_advance($length = "16")
    {
        $length = $length < 16 ? 16 : $length;
        $unicode = uniqid(rand(), false);    // false: don't have dot signal
        return  strtoupper($unicode) . substr(str_shuffle("123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length - mb_strlen($unicode));
    }

    public function convert_time_to_days($date)
    {
        $current_date = date("Y-m-d H:i:s");
        $time = array();
        $day = floor((strtotime($current_date) - strtotime($date)) / (60 * 60 * 24));

        if ($day == 0) {
            $hour = floor((strtotime($current_date) - strtotime($date)) / (60 * 60));

            if ($hour == 0) {
                $minute = floor((strtotime($current_date) - strtotime($date)) / (60));
                $time = $minute . __("minutes ago");
            } else {
                $time = $hour . __("hours ago");
            }
        } else {
            $time = $day . __("days ago");
        }

        return $time;
    }

    public function create_unique_slug($model, $name, $id = array(), $slug = array(), $is_edit_name = false)
    {
        // get new slug when edit name
        if ($is_edit_name) {
            $slug = strtolower(Text::slug(h($name), Configure::read('slug')));
        } else {
            if (!$slug) {
                $slug = strtolower(Text::slug(h($name), Configure::read('slug')));
            }
        }

        $obj_Model = TableRegistry::get($model);

        $conditions = ["'" . $model . ".slug'" => $slug];
        if ($id) {
            $conditions = [
                "'" . $model . ".slug'" => $slug,
                "'" . $model . ".id <>'" => $id
            ];
        }

        $item = $obj_Model->find('all')
            ->where($conditions)
            ->first();

        $step = 0;
        while ($item) {
            // random number here
            $step++;
            $slug = strtolower(Text::slug(h($name), Configure::read('slug')) . Configure::read('slug') . $step);

            $conditions = ["'" . $model . ".slug'" => $slug];
            if ($id) {
                $conditions = [
                    "'" . $model . ".slug'" => $slug,
                    "'" . $model . ".id <>'" => $id
                ];
            }

            $item = $obj_Model->find('all')
                ->where($conditions)
                ->first();
        }

        return $slug;
    }


    public function get_error_code($status_code)
    {

        $status = $status_code;
        $message = "";
        switch ($status_code) {
            case 200:
                $message = __('data_is_saved');
                break;

            case 500:
                $message = __('data_is_not_saved');
                break;
        }

        return [
            'status' => $status,
            'message' => $message,
        ];
    }

    // return list of date in 1 weeks with date_of_week='Mon' in available range (date_start, $date_end)
    public function get_day_within_one_week($date_start, $date_end, $date_of_week = 'Mon')
    {
        $first = date('Y-m-d', strtotime($date_start));
        $lastDay = date('Y-m-d', strtotime($date_end . '+2 months'));  // buffer same day 

        $day = "";
        do {
            if ((date('D', strtotime($first)) === $date_of_week)) {
                $day = $first;
                break;
            }
            $first = date('Y-m-d', strtotime($first . '+1 days'));
        } while (1);

        // $day = $first;
        $result = [];
        do {
            $result[] = date('Y-m-d', strtotime($day));
            $day = date('Y-m-d', strtotime($day . '+7 days'));
        } while ($day <= $lastDay);
        return $result;
    }

    //  return list of date in 1 weeks with date_of_week='Mon',  date_of_week='Tue', ...
    //  in available range (date_start, $date_end)
    public function get_date_of_week_in_range($date_start, $date_end, $date_of_lessons, $number_of_lessons, $holidays)
    {

        $result2 = $result3 = $result4 = $result5 = $result6 = $result7 = $result8 = [];
        foreach ($date_of_lessons as $week) { // 2,3, ..., 8
            if ($week === 2) {
                $result2 = $this->get_day_within_one_week($date_start, $date_end, 'Mon');
            } elseif ($week === 3) {
                $result3 = $this->get_day_within_one_week($date_start, $date_end, 'Tue');
            } elseif ($week === 4) {
                $result4 = $this->get_day_within_one_week($date_start, $date_end, 'Wed');
            } elseif ($week === 5) {
                $result5 = $this->get_day_within_one_week($date_start, $date_end, 'Thu');
            } elseif ($week === 6) {
                $result6 = $this->get_day_within_one_week($date_start, $date_end, 'Fri');
            } elseif ($week === 7) {
                $result7 = $this->get_day_within_one_week($date_start, $date_end, 'Sat');
            } elseif ($week === 8) {
                $result8 = $this->get_day_within_one_week($date_start, $date_end, 'Sun');
            }
        }

        $result = array_merge($result2, $result3, $result4, $result5, $result6, $result7, $result8);
        $sorted = Hash::sort($result, '{n}');

        $dates = [];
        $count = 0;
        foreach ($sorted as $value) {
            $is_same = false;
            foreach ($holidays as $holiday) {
                if ($holiday['date'] ===  $value) {
                    $is_same = true;
                    break;
                }
            }
            if ($is_same === true) {
                continue;
            }

            ++$count;
            $dates[] = $value;
            if ($count >= $number_of_lessons) {
                break;
            }
        }
        return $dates;
    }


    // date of lessons: 2 (Monday), 3, 4, ... 8 (Sunday)
    // number of lessons: 8 days, loop all 8 date, return date with above range
    // return list of date from current_date until $number_of_lessons

    public function get_dates_with_date_of_week($current_date,  $date_of_week = 'Mon', $number_of_lessons)
    {

        $count = 0;
        $first = date('Y-m-d', strtotime($current_date));
        do {
            if ((date('D', strtotime($first)) === $date_of_week)) {
                $day = $first;
                break;
            }
            $first = date('Y-m-d', strtotime($first . '+1 days'));
        } while (1);

        $day = $first;

        $result = [];
        do {
            $result[] = date('Y-m-d', strtotime($day));
            $day = date('Y-m-d', strtotime($day . '+7 days'));
            $count++;
        } while ($count < $number_of_lessons); // dư ra 
        return $result;
    }

    public function search_dates_with_conditions($current_date, $date_of_lessons, $number_of_lessons, $holidays)
    {

        $result2 = $result3 = $result4 = $result5 = $result6 = $result7 = $result8 = [];
        foreach ($date_of_lessons as $val) {
            if ($val === 2) {
                $result2 = $this->get_dates_with_date_of_week($current_date,   'Mon', $number_of_lessons);
            } elseif ($val === 3) {
                $result3 = $this->get_dates_with_date_of_week($current_date,  'Tue', $number_of_lessons);
            } elseif ($val === 4) {
                $result4 = $this->get_dates_with_date_of_week($current_date,  'Wed', $number_of_lessons);
            } elseif ($val === 5) {
                $result5 = $this->get_dates_with_date_of_week($current_date,  'Thu', $number_of_lessons);
            } elseif ($val === 6) {
                $result6 = $this->get_dates_with_date_of_week($current_date,  'Fri', $number_of_lessons);
            } elseif ($val === 7) {
                $result7 = $this->get_dates_with_date_of_week($current_date,  'Sat', $number_of_lessons);
            } elseif ($val === 8) {
                $result8 = $this->get_dates_with_date_of_week($current_date,  'Sun', $number_of_lessons);
            }
        }


        $result = array_merge($result2, $result3, $result4, $result5, $result6, $result7, $result8);
        $sorted = Hash::sort($result, '{n}');

        // get max = $number_of_lessons, skip the holiday
        $count = 0;
        $dates = [];
        foreach ($sorted as $value) {

            $same = false;
            foreach ($holidays as $holiday) {
                if ($holiday['date']  === $value) {
                    $same = true;
                }
            }

            if ($same == true) {    // skip this day
                continue;
            }
            ++$count;
            $dates[] = $value;
            if ($count >= $number_of_lessons) {
                break;
            }
        }

        return $dates;
    }
    public function set_response($controller, $entity)
    {
        $units = MyHelper::getUnits();
        $url = MyHelper::getUrl();
        switch ($controller) {
            case 'FORAMT_EMERGENCY_CONTACTS':
                $en_US_name = null; 
                $zh_HK_name = null;
                if ($entity->emergency_contact->emergency_contact_languages) {
                    foreach ($entity->emergency_contact->emergency_contact_languages as $language) {
                        switch ($language->alias) {
                            case 'en_US':
                                $en_US_name = $language->name;
                                break; 
                            case 'zh_HK':
                                $zh_HK_name = $language->name;
                                break;
                            default:
                                break;
                        }
                    }
                }
                return [
                    'id'                    => $entity->id,
                    'name'                  => isset($entity->emergency_contact->emergency_contact_languages[0]->name) ? $entity->emergency_contact->emergency_contact_languages[0]->name : null,
                    'phone'                 => $entity->emergency_contact->phone_number,
                    'en_US_name'            => $en_US_name,
                    'zh_HK_name'            => $zh_HK_name, 
                    'relationship'          => [
                        'id'                => $entity->relationship_id,
                        'name'              => isset($entity->relationship->relationship_languages[0]->name) ? $entity->relationship->relationship_languages[0]->name : null
                    ]
                ];
            case 'FORMAT_KIDS':
                $genders = MyHelper::getGenders();
                $en_US_name = null; 
                $zh_HK_name = null; 

                if ($entity->kid_languages) {
                    foreach ($entity->kid_languages as $language) {
                        switch ($language->alias) {
                            case 'en_US':
                                $en_US_name = $language->name; 
                                break; 
                            case 'zh_HK':
                                $zh_HK_name = $language->name; 
                                break;
                            default:
                                break;
                        }
                    }
                }

                return [
                    'id'                => $entity->id,
                    'gender'            => $genders[$entity->gender],
                    'gender_id'            => (int)$entity->gender,
                    'dob'               => $entity->dob,
                    'number_of_siblings'       => $entity->number_of_siblings,
                    'caretaker'                => $entity->caretaker,
                    'special_attention_needed' => $entity->special_attention_needed,
                    'name'                      => isset($entity->kid_languages[0]->name) ? $entity->kid_languages[0]->name : null, 
                    'avatar'                    => isset($entity->kid_images[0]->path) ? $url . $entity->kid_images[0]->path : null,
                    'en_US_name'                => $en_US_name,  
                    'zh_HK_name'                => $zh_HK_name, 

                ];
            case 'CidcParents':
                $genders = MyHelper::getGenders();
                return [
                    'phone_number'                  => $this->format_phone_number($entity->user->phone_number),
                    'email'                         => $entity->user->email,
                    'gender'                        => $genders[$entity->user->gender],
                    'gender_id'                        => (int)$entity->gender,
                    'avatar'                        => isset($entity->cidc_parent_images[0]->path)  ? $url . $entity->cidc_parent_images[0]->path : null,
                    'name'                          => isset($entity->cidc_parent_languages[0]->name) ? $entity->cidc_parent_languages[0]->name : null,
                ];
            case 'FORMAT_KID_CLASSES':
                $start_time         = new FrozenTime($entity->start_time);
                $end_time           = new FrozenTime($entity->end_time);
                $duration           = $start_time->diff($end_time)->format('%H:%I');
                $age_range          = TableRegistry::get('Courses')->convert_course_age_range_to_string($entity->cidc_class->course->age_range_from,  $entity->cidc_class->course->age_range_to,  $entity->cidc_class->course->unit);
                $date_for_lessons   = TableRegistry::get('DateOfLessons')->convert_date_of_lessons_to_string($entity->cidc_class->date_of_lessons);
                return [
                    'id'                            => $entity->cidc_class_id,
                    'is_attended'                   => $entity->is_attended,
                    'fee'                           => "HK$" .  number_format(floatval($entity->fee), 2),
                    'name'                          => $entity->cidc_class->name,
                    'code'                          => $entity->cidc_class->code,
                    'status'                        => $entity->cidc_class->status,
                    'class_type'                    => [
                        'id'    => $entity->cidc_class->class_type_id,
                        'name' => $entity->cidc_class->class_type_id === MyHelper::CIRCULAR ? __d('cidcclass', 'circular') : __d('cidcclass', 'non_circular'),
                    ],
                    'target_audience_from'          => $entity->cidc_class->target_audience_from,
                    'target_audience_to'            => $entity->cidc_class->target_audience_to,
                    'target_unit'                   => $units[$entity->cidc_class->target_unit],
                    'minimum_of_students'           => $entity->cidc_class->minimum_of_students,
                    'maximum_of_students'           => $entity->cidc_class->maximum_of_students,
                    'start_date'                    => $entity->cidc_class->start_date,
                    'end_date'                      => $entity->cidc_class->end_date,
                    'number_of_lessons'             => $entity->cidc_class->number_of_lessons,
                    'date'                          => $entity->cidc_class->start_date->format('d/m/Y') . ' - ' . $entity->cidc_class->end_date->format('d/m/Y'),
                    'start_time'                    => $entity->cidc_class->start_time->format('H:i'),
                    'end_time'                      => $entity->cidc_class->end_time->format('H:i'),
                    'time'                          => $entity->cidc_class->start_time->format('H:i A') . " - " .  $entity->cidc_class->end_time->format('H:i A'),
                    'duration'                      => $duration,
                    'age_range'                     => $age_range,
                    'date_for_lessons'              => $date_for_lessons,
                    'program'                       => [
                        'id'                        => $entity->cidc_class->program->id,
                        'level'                     => $entity->cidc_class->program->level,
                        'banner_color'              => $entity->cidc_class->program->banner_color,
                        'title_color'               => $entity->cidc_class->program->title_color,
                        'background_color'          => $entity->cidc_class->program->background_color,
                        'name'                      => isset($entity->cidc_class->program->program_languages[0]->name) ? $entity->cidc_class->program->program_languages[0]->name : null,
                    ],
                    'course'                        => [
                        'id'                        => $entity->cidc_class->course->id,
                        'age_range_from'            => $entity->cidc_class->course->age_range_from,
                        'age_range_to'              => $entity->cidc_class->course->age_range_to,
                        'unit'                      => $units[$entity->cidc_class->course->unit],
                        'name'                      => isset($entity->cidc_class->course->course_languages[0]->name) ? $entity->cidc_class->course->course_languages[0]->name : null,
                    ],
                    'center'                        => [
                        'id'                        => $entity->cidc_class->center->id,
                        'name'                      => $entity->cidc_class->center->center_languages[0]->name,
                        'address'                   => $entity->cidc_class->center->center_languages[0]->address,
                        'district'                  => [
                            'id'                    => $entity->cidc_class->center->district->id,
                            'name'                  => isset($entity->cidc_class->center->district->district_languages[0]->name) ? $entity->cidc_class->center->district->district_languages[0]->name : null,
                        ]
                    ]
                ];
            case 'FORMAT_CLASSES':
                $start_time         = new FrozenTime($entity->start_time);
                $end_time           = new FrozenTime($entity->end_time);
                $duration           = $start_time->diff($end_time)->format('%H:%I');
                $age_range          = TableRegistry::get('Courses')->convert_course_age_range_to_string($entity->course->age_range_from,  $entity->course->age_range_to,  $entity->course->unit);
                $date_for_lessons   = TableRegistry::get('DateOfLessons')->convert_date_of_lessons_to_string($entity->date_of_lessons);
                $program_image      = !empty($entity->program->program_images) ? Router::url('/', true) . $entity->program->program_images[0]->path : null;
 
                return [
                    'id'                            => $entity->id,
                    'name'                          => $entity->name,
                    'description'                   => $entity->cidc_class_languages ? $entity->cidc_class_languages[0]->description : '',
                    'code'                          => $entity->code,
                    'status'                        => $entity->status,
                    'class_type_id'                 => $entity->class_type_id,
                    'class_type'                    => [
                        'id'   => $entity->class_type_id,
                        'name' => $entity->class_type_id === MyHelper::CIRCULAR ? __d('cidcclass', 'circular') : __d('cidcclass', 'non_circular')
                    ],
                    'number_of_register'            => $entity->number_of_register,
                    'number_of_lessons'             => $entity->number_of_lessons,
                    'fee'                           => "HK$" .  number_format(floatval($entity->fee), 2),
                    'fee_decimal'                   => floatval($entity->fee),
                    'target_audience_from'          => $entity->target_audience_from,
                    'target_audience_to'            => $entity->target_audience_to,
                    'target_unit'                   => $units[$entity->target_unit],
                    'minimum_of_students'           => $entity->minimum_of_students,
                    'maximum_of_students'           => $entity->maximum_of_students,
                    'start_date'                    => $entity->start_date,
                    'end_date'                      => $entity->end_date,
                    'date'                          => $entity->start_date->format('d/m/Y') . ' - ' . $entity->end_date->format('d/m/Y'),
                    'start_time'                    => $entity->start_time->format('H:i'),
                    'end_time'                      => $entity->end_time->format('H:i'),
                    'time'                          => $entity->start_time->format('H:i A') . ' - ' . $entity->end_time->format('H:i A'),
                    'duration'                      => $duration,
                    'age_range'                     => $age_range,
                    'date_for_lessons'              => $date_for_lessons,
                    'program'                       => [
                        'id'                        => $entity->program->id,
                        'level'                     => $entity->program->level,
                        'title_color'               => $entity->program->title_color,
                        'background_color'          => $entity->program->background_color,
                        'name'                      => !empty($entity->program->program_languages) ? $entity->program->program_languages[0]->name : null,
                        'description'               => !empty($entity->program->program_languages) ? $entity->program->program_languages[0]->description : null,
                        'image'                     => $program_image,
                    ],
                    'course'                        => [
                        'id'                        => $entity->course->id,
                        'age_range_from'            => $entity->course->age_range_from,
                        'age_range_to'              => $entity->course->age_range_to,
                        'unit'                      => $units[$entity->course->unit],
                        'name'                      => !empty($entity->course->course_languages) ? $entity->course->course_languages[0]->name : null,
                    ],
                    'center'                        => [
                        'id'                        => $entity->center->id,
                        'name'                      => !empty($entity->center->center_languages) ? $entity->center->center_languages[0]->name : null,
                        'address'                   => !empty($entity->center->center_languages) ? $entity->center->center_languages[0]->address : null,
                        'district'                  => [
                            'id'                    => $entity->center->district->id,
                            'name'                  => !empty($entity->center->district->district_languages) ? $entity->center->district->district_languages[0]->name : null,
                        ]
                    ]
                ];
            case 'FORMAT_RELATIONSHIP':
                return [
                    'id'            =>      $entity->relationship_id,
                    'name'          =>      !empty($entity->relationship->relationship_languages) ? $entity->relationship->relationship_languages[0]->name : null
                ];
            default:
                break;
        }
    }

    function format_size_units($bytes) { 
        if ($bytes >= 1024 * 1024 * 1024 * 1024) { 
            $bytes = number_format($bytes / 1099511627776, 2) . ' TB'; 
        
        } elseif ($bytes >= 1024 * 1024 * 1024) { 
            $bytes = number_format($bytes / 1073741824, 2) . ' GB'; 
        
        } elseif ($bytes >= 1024 * 1024) { 
            $bytes = number_format($bytes / 1048576, 2) . ' MB'; 
        
        } elseif ($bytes >= 1024) { 
            $bytes = number_format($bytes / 1024, 2) . ' KB'; 
        
        } elseif ($bytes >= 1) { 
            $bytes = $bytes . ' B'; 
        
        } else { 
            $bytes = '0 B'; 
        }

        return $bytes; 
    }

    public function format_phone_number($phone_number) {
 
        if (strlen($phone_number) <= 8)
            return substr($phone_number, 0, 4) . "-" . substr($phone_number, 4, strlen($phone_number) - 1);

        elseif (strlen($phone_number) <= 10) {
            return substr($phone_number, 0, 4) . " " . substr($phone_number, 4, 3) . " " . substr($phone_number, 7, strlen($phone_number) - 1);
        }

        return $phone_number;
    }
}

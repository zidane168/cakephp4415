<?php

$lists = [
    [
        'key' => 'abouts',
        'name' => __d('setting', 'abouts'),
    ],
    [
        'key' => 'administrators',
        'name' => __d('administrator', 'administrators'),
    ],
    [
        'key' => 'albums',
        'name' => __d('staff', 'albums'),
    ],
    [
        'key' => 'centers',
        'name' => __d('center', 'centers'),
    ],
    [
        'key' => 'CidcClasses',
        'name' => __d('center', 'cidc_classes'),
    ],
    [
        'key' => 'CidcHolidays',
        'name' => __d('setting', 'cidc_holidays'),
    ],
    [
        'key' => 'CidcParents',
        'name' => __d('parent', 'cidc_parents'),
    ],
    [
        'key' => 'ClassTypes',
        'name' => __d('center', 'class_types'),
    ],
    [
        'key' => 'Contacts',
        'name' => __d('setting', 'contacts'),
    ],
    [
        'key' => 'Courses',
        'name' => __d('center', 'courses'),
    ],
    [
        'key' => 'EmergencyContacts',
        'name' => __d('setting', 'emergency_contacts'),
    ],
    [
        'key' => 'Feedbacks',
        'name' => __d('setting', 'feedbacks'),
    ],
    [
        'key' => 'Kids',
        'name' => __d('parent', 'kids'),
    ],
    [
        'key' => 'News',
        'name' => __d('news', 'newses'),
    ],
    [
        'key' => 'CidcParents',
        'name' => __d('parent', 'cidc_parents'),
    ],
    [
        'key' => 'orders',
        'name' => __d('order', 'orders'),
    ],
    [
        'key' => 'Permissions',
        'name' => __d('permission', 'permissions'),
    ],
    [
        'key' => 'PrivacyPolicies',
        'name' => __d('cidcclass', 'privacy_policies'),
    ],
    [
        'key' => 'Professionals',
        'name' => __d('professional', 'professionals'),
    ],
    [
        'key' => 'Programs',
        'name' => __d('center', 'programs'),
    ],
    [
        'key' => 'Relationships',
        'name' => __d('setting', 'relationships'),
    ],
    [
        'key' => 'Roles',
        'name' => __d('role', 'roles'),
    ],
    [
        'key' => 'RescheduleHistories',
        'name' => __d('staff', 'reschedule_histories'),
    ],
    [
        'key' => 'Staffs',
        'name' => __d('staff', 'staffs'),
    ],
    [
        'key' => 'StudentAttendedClasses',
        'name' => __d('cidcclass', 'student_attended_classes'),
    ],
    [
        'key' => 'StudentRegisterClasses',
        'name' => __d('cidcclass', 'student_register_classes'),
    ],
    [
        'key' => 'SystemMessages',
        'name' => __d('user', 'system_messages'),
    ],
    [
        'key' => 'Terms',
        'name' => __d('setting', 'terms'),
    ],
    [
        'key' => 'UserVerifications',
        'name' => __d('user', 'user_verifications'),
    ],
    [
        'key' => 'Videos',
        'name' => __d('staff', 'videos'),
    ],
    [
        'key' => 'Districts',
        'name' => __d('setting', 'districts'),
    ],
    [
        'key' => 'SickLeaveHistories',
        'name' => __d('staff', 'sick_leave_histories'),
    ],
    [
        'key' => 'Languages',
        'name' => __('languages'),
    ], 
];
  
foreach ($lists as $list) {
    if (strcmp(strtolower($key), strtolower($list['key'])) == 0) {
        echo $list['name'];
        return;
    }
}


echo $key;

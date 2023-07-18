<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CidcClass Entity
 *
 * @property int $id
 * @property string $name
 * @property int $program_id
 * @property int $course_id
 * @property int $center_id
 * @property int $status
 * @property string $fee
 * @property int $class_type_id
 * @property string $code
 * @property int $target_audience_from
 * @property int $target_audience_to
 * @property int $target_unit
 * @property int $minimun_of_students
 * @property int $maximun_of_students
 * @property string $date_of_lesson
 * @property int $number_of_weeks
 * @property int $start_date
 * @property int $end_date
 * @property int $start_time
 * @property int $end_time
 * @property bool $enabled
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $created_by
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $modified_by
 *
 * @property \App\Model\Entity\Program $program
 * @property \App\Model\Entity\Course $course
 * @property \App\Model\Entity\Center $center
 * @property \App\Model\Entity\ClassType $class_type
 * @property \App\Model\Entity\CidcClassLanguage[] $cidc_class_languages
 */
class CidcClass extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'name' => true,
        'program_id' => true,
        'course_id' => true,
        'center_id' => true,
        'status' => true,
        'fee' => true,
        'number_of_register' => true,
        'class_type_id' => true,
        'code' => true,
        'target_audience_from' => true,
        'target_audience_to' => true,
        'target_unit' => true,
        'minimum_of_students' => true,
        'maximum_of_students' => true,
        'number_of_lessons' => true,
        'start_date' => true,
        'end_date' => true,
        'start_time' => true,
        'end_time' => true,
        'enabled' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'program' => true,
        'course' => true,
        'center' => true,
        'class_type' => true,
        'cidc_class_languages' => true,
        'date_of_lessons' => true,
        'albums' => true
    ];
}

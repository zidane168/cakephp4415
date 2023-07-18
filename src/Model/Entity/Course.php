<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Course Entity
 *
 * @property int $id
 * @property int $program_id
 * @property int $age_range_from
 * @property int $age_range_to
 * @property int $unit
 * @property bool $enabled
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $created_by
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $modified_by
 *
 * @property \App\Model\Entity\Program $program
 * @property \App\Model\Entity\Class[] $classes
 * @property \App\Model\Entity\CourseLanguage[] $course_languages
 */
class Course extends Entity
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
        'program_id' => true,
        'age_range_from' => true,
        'age_range_to' => true,
        'unit' => true,
        'enabled' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'sort'  => true, 
        'program' => true,
        'classes' => true,
        'course_languages' => true,
    ];
}

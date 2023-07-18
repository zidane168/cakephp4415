<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * StudentRegisterClassReceipt Entity
 *
 * @property int $id
 * @property int $student_register_class_id
 * @property string $file_name
 * @property string|null $path
 * @property string|null $ext
 * @property int|null $size
 *
 * @property \App\Model\Entity\StudentRegisterClass $student_register_class
 */
class StudentRegisterClassReceipt extends Entity
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
        'student_register_class_id' => true,
        'file_name' => true,
        'path' => true,
        'ext' => true,
        'size' => true,
        'student_register_class' => true,
    ];
}

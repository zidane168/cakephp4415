<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Staff Entity
 *
 * @property int $id
 * @property int $user_id
 * @property bool $gender
 * @property bool $enabled
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $created_by
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $modified_by
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\StaffLanguage[] $staff_languages
 * @property \App\Model\Entity\StudentJoinClass[] $student_join_classes
 */
class Staff extends Entity
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
        'user_id' => true,
        'center_id' => true,
        'gender' => true,
        'enabled' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'user' => true,
        'center' => true,
        'staff_languages' => true,
        'student_join_classes' => true,
    ];
}

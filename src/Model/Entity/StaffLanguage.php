<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * StaffLanguage Entity
 *
 * @property int $id
 * @property int $staff_id
 * @property string $alias
 * @property string|null $name
 *
 * @property \App\Model\Entity\Staff $staff
 */
class StaffLanguage extends Entity
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
        'staff_id' => true,
        'alias' => true,
        'name' => true,
        'staff' => true,
    ];
}

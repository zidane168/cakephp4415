<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * EmergencyContactLanguage Entity
 *
 * @property int $id
 * @property int $emergency_contact_id
 * @property string $alias
 * @property string $name
 * @property int $phone_number
 *
 * @property \App\Model\Entity\EmergencyContact $emergency_contact
 */
class EmergencyContactLanguage extends Entity
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
        'emergency_contact_id' => true,
        'alias' => true,
        'name' => true,
        'emergency_contact' => true,
    ];
}

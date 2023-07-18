<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * KidsEmergency Entity
 *
 * @property int $id
 * @property int $kid_id
 * @property int $relationship_id
 * @property int $emergency_contact_id
 * @property bool $enabled
 *
 * @property \App\Model\Entity\Kid $kid
 * @property \App\Model\Entity\Relationship $relationship
 * @property \App\Model\Entity\EmergencyContact $emergency_contact
 */
class KidsEmergency extends Entity
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
        'kid_id' => true,
        'relationship_id' => true,
        'emergency_contact_id' => true,
        'enabled' => true,
        'kid' => true,
        'relationship' => true,
        'emergency_contact' => true,
    ];
}

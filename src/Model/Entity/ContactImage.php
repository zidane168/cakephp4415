<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ContactImage Entity
 *
 * @property int $id
 * @property int $contact_id
 * @property int|null $width
 * @property int|null $height
 * @property string $path
 * @property int|null $size
 *
 * @property \App\Model\Entity\Contact $contact
 */
class ContactImage extends Entity
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
        'contact_id' => true,
        'width' => true,
        'height' => true,
        'path' => true,
        'size' => true,
        'contact' => true,
    ];
}

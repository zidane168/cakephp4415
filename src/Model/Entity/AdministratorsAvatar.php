<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AdministratorsAvatar Entity
 *
 * @property int $id
 * @property int $administrator_id
 * @property string|null $path
 * @property int|null $size
 * @property int|null $width
 * @property int|null $height
 *
 * @property \App\Model\Entity\Administrator $administrator
 */
class AdministratorsAvatar extends Entity
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
        'administrator_id' => true,
        'path' => true,
        'size' => true,
        'width' => true,
        'height' => true,
        'administrator' => true,
    ];
}

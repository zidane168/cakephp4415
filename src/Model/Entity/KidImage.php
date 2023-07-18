<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * KidImage Entity
 *
 * @property int $id
 * @property int $kid_id
 * @property int $width
 * @property int $height
 * @property string $name
 * @property string $path
 * @property int $size
 *
 * @property \App\Model\Entity\Kid $kid
 */
class KidImage extends Entity
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
        'width' => true,
        'height' => true,
        'name' => true,
        'path' => true,
        'size' => true,
        'kid' => true,
    ];
}

<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CenterFile Entity
 *
 * @property int $id
 * @property int $center_id
 * @property int $width
 * @property int $height
 * @property int $type
 * @property string|null $path
 * @property string|null $file_name
 *
 * @property \App\Model\Entity\Center $center
 */
class CenterFile extends Entity
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
        'center_id' => true,
        'width' => true,
        'height' => true,
        'ext' => true,
        'path' => true,
        'size' => true,
        'file_name' => true,
        'center' => true,
    ];
}

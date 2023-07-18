<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CidcParentImage Entity
 *
 * @property int $id
 * @property int $cidc_parent_id
 * @property int $width
 * @property int $height
 * @property string $name
 * @property string $path
 * @property int $size
 *
 * @property \App\Model\Entity\CidcParent $cidc_parent
 */
class CidcParentImage extends Entity
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
        'cidc_parent_id' => true,
        'width' => true,
        'height' => true,
        'name' => true,
        'path' => true,
        'size' => true,
        'cidc_parent' => true,
    ];
}

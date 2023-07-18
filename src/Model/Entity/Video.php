<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Video Entity
 *
 * @property int $id
 * @property int $cidc_class_id
 * @property string|null $ext
 * @property int $size
 * @property string|null $path
 * @property string|null $file_name
 *
 * @property \App\Model\Entity\CidcClass $cidc_class
 */
class Video extends Entity
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
        'cidc_class_id' => true,
        'ext' => true,
        'size' => true,
        'path' => true,
        'file_name' => true,
        'cidc_class' => true,
    ];
}

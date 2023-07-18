<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * NewsImage Entity
 *
 * @property int $id
 * @property int $news_id
 * @property string $name
 * @property string $path
 * @property int $width
 * @property int $height
 * @property int $size
 *
 * @property \App\Model\Entity\News $news
 */
class NewsImage extends Entity
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
        'news_id' => true,
        'name' => true,
        'path' => true,
        'width' => true,
        'height' => true,
        'size' => true,
        'news' => true,
    ];
}

<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ImageType Entity
 *
 * @property int $id
 * @property string|null $slug
 * @property bool|null $enabled
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $modified_by
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $created_by
 *
 * @property \App\Model\Entity\CourseImage[] $course_images
 * @property \App\Model\Entity\ImageTypeLanguage[] $image_type_languages
 */
class ImageType extends Entity
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
        'slug' => true,
        'enabled' => true,
        'modified' => true,
        'modified_by' => true,
        'created' => true,
        'created_by' => true,
        'course_images' => true,
        'image_type_languages' => true,
    ];
}

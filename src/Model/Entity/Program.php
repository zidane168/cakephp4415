<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Program Entity
 *
 * @property int $id 
 * @property string|null $title_color
 * @property string|null $background_color
 * @property bool $enabled
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Administrator $created_by
 * @property \App\Model\Entity\Administrator $modified_by
 * @property \App\Model\Entity\Class[] $classes
 * @property \App\Model\Entity\Course[] $courses
 * @property \App\Model\Entity\ProgramImage[] $program_images
 * @property \App\Model\Entity\ProgramLanguage[] $program_languages
 */
class Program extends Entity
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
        'title_color' => true,
        'background_color' => true,
        'enabled' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'classes' => true,
        'courses' => true,
        'program_images' => true,
        'program_languages' => true,
    ];
}

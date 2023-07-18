<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ClassType Entity
 *
 * @property int $id
 * @property bool $enabled
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $created_by
 * @property int $modified_by
 *
 * @property \App\Model\Entity\CidcClass[] $cidc_classes
 * @property \App\Model\Entity\ClassTypeLanguage[] $class_type_languages
 */
class ClassType extends Entity
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
        'enabled' => true,
        'created' => true,
        'modified' => true,
        'created_by' => true,
        'modified_by' => true,
        'cidc_classes' => true,
        'class_type_languages' => true,
    ];
}

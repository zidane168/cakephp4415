<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Kid Entity
 *
 * @property int $id
 * @property int $cidc_parent_id
 * @property int $relationship_id
 * @property bool $gender
 * @property \Cake\I18n\FrozenDate $dob
 * @property int $number_of_siblings
 * @property string|null $caretaker
 * @property string|null $special_attention_needed
 * @property bool $enabled
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Administrator $created_by
 * @property \App\Model\Entity\Administrator $modified_by
 * @property \App\Model\Entity\Kid $parent_kid
 * @property \App\Model\Entity\Relationship $relationship
 * @property \App\Model\Entity\KidImage[] $kid_images
 * @property \App\Model\Entity\KidLanguage[] $kid_languages
 * @property \App\Model\Entity\Kid[] $child_kids
 * @property \App\Model\Entity\KidsEmergency[] $kids_emergencies
 */
class Kid extends Entity
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
        'relationship_id' => true,
        'gender' => true,
        'dob' => true,
        'number_of_siblings' => true,
        'caretaker' => true,
        'special_attention_needed' => true,
        'enabled' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'parent_kid' => true,
        'relationship' => true,
        'kid_images' => true,
        'kid_languages' => true,
        'child_kids' => true,
        'kids_emergencies' => true,
        'student_register_classes' => true,
        'student_attended_classes' => true
    ];
}

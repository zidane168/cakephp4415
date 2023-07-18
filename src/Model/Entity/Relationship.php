<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Relationship Entity
 *
 * @property int $id
 * @property bool $enabled
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $created_by
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $modified_by
 *
 * @property \App\Model\Entity\Kid[] $kids
 * @property \App\Model\Entity\KidsEmergency[] $kids_emergencies
 * @property \App\Model\Entity\RelationshipLanguage[] $relationship_languages
 */
class Relationship extends Entity
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
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'kids' => true,
        'kids_emergencies' => true,
        'relationship_languages' => true,
    ];
}

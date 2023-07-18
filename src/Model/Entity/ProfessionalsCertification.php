<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ProfessionalsCertification Entity
 *
 * @property int $id
 * @property int $professional_id
 * @property bool $enabled
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $created_by
 * @property int|null $modified_by
 *
 * @property \App\Model\Entity\Professional $professional
 */
class ProfessionalsCertification extends Entity
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
        'professional_id' => true,
        'enabled' => true,
        'created' => true,
        'modified' => true,
        'created_by' => true,
        'modified_by' => true,
        'professional' => true,
        'professional_certification_languages' => true,
    ];
}

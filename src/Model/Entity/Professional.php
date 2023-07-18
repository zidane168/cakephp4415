<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Professional Entity
 *
 * @property int $id
 * @property bool $gender
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $created_by
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $modified_by
 *
 * @property \App\Model\Entity\ProfessionalLanguage[] $professional_languages
 * @property \App\Model\Entity\ProfessionalsCertification[] $professionals_certifications
 */
class Professional extends Entity
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
        'gender' => true,
        'type'  => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'professional_languages' => true,
        'professional_images' => true,
        'professionals_certifications' => true,
    ];
}

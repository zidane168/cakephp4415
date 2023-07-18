<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ProfessionalCertificationLanguage Entity
 *
 * @property int $id
 * @property int $professional_certification_id
 * @property string $alias
 * @property string $name
 *
 * @property \App\Model\Entity\ProfessionalCertification $professional_certification
 */
class ProfessionalCertificationLanguage extends Entity
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
        'professional_certification_id' => true,
        'alias' => true,
        'name' => true,
        'professionals_certification' => true,
    ];
}

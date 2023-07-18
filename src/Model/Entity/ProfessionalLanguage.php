<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ProfessionalLanguage Entity
 *
 * @property int $id
 * @property int|null $professional_id
 * @property string|null $nick_name
 * @property int $type
 * @property string|null $name
 * @property string|null $title
 * @property string|null $description
 * @property string|null $alias
 *
 * @property \App\Model\Entity\Professional $professional
 */
class ProfessionalLanguage extends Entity
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
        'nick_name' => true,
        'name' => true,
        'title' => true,
        'description' => true,
        'alias' => true,
        'professional' => true,
    ];
}

<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DistrictLanguage Entity
 *
 * @property int $id
 * @property int|null $district_id
 * @property string|null $alias
 * @property string|null $name
 * @property string|null $description
 *
 * @property \App\Model\Entity\District $district
 */
class DistrictLanguage extends Entity
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
        'district_id' => true,
        'alias' => true,
        'name' => true,
        'description' => true,
        'district' => true,
    ];
}

<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * District Entity
 *
 * @property int $id
 * @property int $region_id
 * @property string|null $slug
 * @property bool|null $enabled
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $created_by
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $modified_by
 *
 * @property \App\Model\Entity\Region $region
 * @property \App\Model\Entity\DistrictLanguage[] $district_languages
 * @property \App\Model\Entity\ParkingRequirement[] $parking_requirements
 * @property \App\Model\Entity\Product[] $products
 * @property \App\Model\Entity\Subdistrict[] $subdistricts
 * @property \App\Model\Entity\Transaction[] $transactions
 */
class District extends Entity
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
        'region_id' => true,
        'slug' => true,
        'enabled' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'region' => true,
        'district_languages' => true,
        'parking_requirements' => true,
        'products' => true,
        'subdistricts' => true,
        'transactions' => true,
    ];
}

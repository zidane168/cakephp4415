<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CidcParentLanguage Entity
 *
 * @property int $id
 * @property int $cidc_parent_id
 * @property string $alias
 * @property string $name
 *
 * @property \App\Model\Entity\CidcParentLanguage $parent_cidc_parent_language
 * @property \App\Model\Entity\CidcParentLanguage[] $child_cidc_parent_languages
 */
class CidcParentLanguage extends Entity
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
        'alias' => true,
        'name' => true,
        'cidc_parent' => true,
        'child_cidc_parent_languages' => true,
    ];
}

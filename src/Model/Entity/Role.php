<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Role Entity
 *
 * @property int $id
 * @property string|null $slug
 * @property string|null $name
 * @property \Cake\I18n\FrozenTime|null $updated
 * @property int|null $updated_by
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $created_by
 *
 * @property \App\Model\Entity\Administrator[] $administrators
 * @property \App\Model\Entity\Permission[] $permissions
 */
class Role extends Entity
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
        'slug' => true,
        'name' => true,
        'modified' => true,
        'modified_by' => true,
        'created' => true,
        'created_by' => true,
        'administrators' => true,
        'permissions' => true,
    ];
}

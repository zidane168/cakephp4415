<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RolesPermission Entity
 *
 * @property int $id
 * @property int|null $role_id
 * @property int|null $permission_id
 * @property \Cake\I18n\FrozenTime|null $updated
 * @property int|null $updated_by
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $created_by
 *
 * @property \App\Model\Entity\Role $role
 * @property \App\Model\Entity\Permission $permission
 */
class RolesPermission extends Entity
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
        'role_id' => true,
        'permission_id' => true,
        'modified' => true,
        'modified_by' => true,
        'created' => true,
        'created_by' => true,
        'role' => true,
        'permission' => true,
    ];
   
}

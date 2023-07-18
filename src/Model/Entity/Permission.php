<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Permission Entity
 *
 * @property int $id
 * @property string|null $slug
 * @property string|null $name
 * @property string $p_plugin
 * @property string $p_controller
 * @property string|null $p_model
 * @property string $action
 * @property \Cake\I18n\FrozenTime|null $updated
 * @property int|null $updated_by
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $created_by
 *
 * @property \App\Model\Entity\Role[] $roles
 */
class Permission extends Entity
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
        'p_plugin' => true,
        'p_controller' => true,
        'p_model' => true,
        'action' => true,
        'modified' => true,
        'modified_by' => true,
        'created' => true,
        'created_by' => true,
        'roles' => true,
    ];
}

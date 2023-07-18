<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AdministratorManageCenter Entity
 *
 * @property int $id
 * @property int $center_id
 * @property int $administrator_id
 * @property bool $enabled
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $created_by
 * @property \Cake\I18n\FrozenTime|null $updated
 * @property int|null $updated_by
 *
 * @property \App\Model\Entity\Center $center
 * @property \App\Model\Entity\Administrator $administrator
 */
class AdministratorManageCenter extends Entity
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
        'center_id' => true,
        'administrator_id' => true,
        'enabled' => true,
        'created' => true,
        'created_by' => true,
        'updated' => true,
        'updated_by' => true,
        'center' => true,
        'administrator' => true,
    ];
}

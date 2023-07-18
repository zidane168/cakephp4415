<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SystemMessageLanguage Entity
 *
 * @property int $id
 * @property int $system_message_id
 * @property string|null $alias
 * @property string|null $title
 * @property string|null $description
 *
 * @property \App\Model\Entity\SystemMessage $system_message
 */
class SystemMessageLanguage extends Entity
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
        'system_message_id' => true,
        'alias' => true,
        'title' => true,
        'message' => true,
        'system_message' => true,
    ];
}

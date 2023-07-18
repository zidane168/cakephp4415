<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SystemMessage Entity
 *
 * @property int $id
 * @property int $cidc_class_id
 * @property int $parent_id
 * @property int $kid_id
 * @property bool $status
 * @property int|null $created
 * @property int|null $modified
 *
 * @property \App\Model\Entity\Administrator $created_by
 * @property \App\Model\Entity\Administrator $modified_by
 * @property \App\Model\Entity\CidcClass $cidc_class
 * @property \App\Model\Entity\SystemMessage $parent_system_message
 * @property \App\Model\Entity\Kid $kid
 * @property \App\Model\Entity\SystemMessageLanguage[] $system_message_languages
 * @property \App\Model\Entity\SystemMessage[] $child_system_messages
 */
class SystemMessage extends Entity
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
        'cidc_class_id' => true,
        'cidc_parent_id' => true,
        'kid_id' => true,
        'read_time' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true, 

        'cidc_class' => true,
        'cidc_parent' => true, 
        'kid' => true,
        'system_message_languages' => true, 
    ];
}

<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SickLeaveHistory Entity
 *
 * @property int $id
 * @property int $cidc_class_id
 * @property int $kid_id
 * @property \Cake\I18n\FrozenDate $date
 * @property \Cake\I18n\FrozenTime $time
 * @property string|null $reason
 * @property bool $enabled
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $created_by
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $modified_by
 *
 * @property \App\Model\Entity\CidcClass $cidc_class
 * @property \App\Model\Entity\Kid $kid
 * @property \App\Model\Entity\SickLeaveHistoryFile[] $sick_leave_history_files
 */
class SickLeaveHistory extends Entity
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
        'kid_id' => true,
        'date' => true,
        'time' => true,
        'reason' => true,
        'enabled' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'cidc_class' => true,
        'kid' => true,
        'sick_leave_history_files' => true,
        'is_official_link' => true
    ];
}

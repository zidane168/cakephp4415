<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RescheduleHistory Entity
 *
 * @property int $id
 * @property int $from_cidc_class_id
 * @property int $to_cidc_class_id
 * @property int $kid_id
 * @property \Cake\I18n\FrozenTime|null $date_from
 * @property \Cake\I18n\FrozenTime|null $date_to
 * @property int $status
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $created_by
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $modified_by
 *
 * @property \App\Model\Entity\FromCidcClass $from_cidc_class
 * @property \App\Model\Entity\ToCidcClass $to_cidc_class
 * @property \App\Model\Entity\Kid $kid
 */
class RescheduleHistory extends Entity
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
        'from_cidc_class_id' => true,
        'to_cidc_class_id' => true,
        'kid_id' => true,
        'date_from' => true,
        'date_to' => true,
        'status' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'kid' => true,
        'reason' => true,
        'from_start_time' => true,
        'from_end_time' => true,
        'to_start_time' => true,
        'to_end_time' => true,
        'reschedule_history_files' => true,
        'is_official_link' => true
    ];
}

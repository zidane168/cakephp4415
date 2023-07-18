<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RescheduleHistoryFile Entity
 *
 * @property int $id
 * @property int $reschedule_history_id
 * @property string $file_name
 * @property string|null $path
 * @property string|null $ext
 * @property int|null $size
 *
 * @property \App\Model\Entity\RescheduleHistory $reschedule_history
 */
class RescheduleHistoryFile extends Entity
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
        'reschedule_history_id' => true,
        'file_name' => true,
        'path' => true,
        'ext' => true,
        'size' => true,
        'reschedule_history' => true,
    ];
}

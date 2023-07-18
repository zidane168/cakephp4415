<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CidcHoliday Entity
 *
 * @property int $id
 * @property int $date
 * @property string|null $description
 * @property bool $enabled
 * @property int|null $created
 * @property int|null $created_by
 * @property int|null $modified
 * @property int|null $modified_by
 */
class CidcHoliday extends Entity
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
        'date' => true,
        'description' => true,
        'enabled' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
    ];
}

<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * LogApi Entity
 *
 * @property int $id
 * @property string $url
 * @property string $params
 * @property string|null $result
 * @property string|null $old_data
 * @property string|null $new_data
 * @property bool|null $status
 * @property bool $archived
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $created_by
 *
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Member $member
 */
class LogApi extends Entity
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
        'url' => true,
        'request' => true,
        'response' => true,
        'old_data' => true,
        'new_data' => true,
        'status' => true,
        'archived' => true,
        'created' => true,
        'created_by' => true,
    ];
}

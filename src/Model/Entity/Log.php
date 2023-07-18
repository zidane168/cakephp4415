<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Log Entity
 *
 * @property int $id
 * @property int|null $company_id
 * @property string $plugin
 * @property string $controller
 * @property string $action
 * @property string|null $new_data
 * @property string|null $old_data
 * @property bool $archived
 * @property string|null $remote_ip
 * @property string|null $agent
 * @property string|null $version
 * @property string|null $platform
 * @property string|null $browser
 * @property bool $enabled
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $created_by
 *
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\LogError[] $log_errors
 * @property \App\Model\Entity\LogSuccess[] $log_successes
 */
class Log extends Entity
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
        'company_id' => true,
        'plugin' => true,
        'controller' => true,
        'action' => true,
        'new_data' => true,
        'old_data' => true,
        'archived' => true,
        'remote_ip' => true,
        'agent' => true,
        'version' => true,
        'platform' => true,
        'browser' => true,
        'enabled' => true,
        'created' => true,
        'created_by' => true,
        'company' => true,
        'log_errors' => true,
        'log_successes' => true,
    ];
}

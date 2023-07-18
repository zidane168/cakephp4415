<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UserVerification Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $phone_number
 * @property int $email
 * @property int $code
 * @property int $verification_type
 * @property int $verification_method
 * @property bool $is_used
 * @property bool $enabled
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $created_by
 * @property int|null $modified_by
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\User $user
 */
class UserVerification extends Entity
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
        'user_id' => true,
        'phone_number' => true,
        'email' => true,
        'code' => true,
        'verification_type' => true,
        'verification_method' => true,
        'is_used' => true,
        'enabled' => true,
        'created' => true,
        'created_by' => true,
        'modified_by' => true,
        'modified' => true,
    ];
}

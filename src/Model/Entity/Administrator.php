<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Administrator Entity
 *
 * @property int $id
 * @property int|null $company_id
 * @property string|null $token
 * @property string|null $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $username
 * @property string|null $password
 * @property \Cake\I18n\FrozenTime|null $last_logged_in
 * @property string|null $code_forgot
 * @property \Cake\I18n\FrozenTime|null $created_code_forgot
 * @property int|null $time_input_code
 * @property int|null $time_input_pass
 * @property bool|null $enabled
 * @property \Cake\I18n\FrozenTime|null $updated
 * @property int|null $updated_by
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $created_by
 *
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Role[] $roles
 */
class Administrator extends Entity
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
        'token' => true,
        'name' => true,
        'email' => true,
        'phone' => true,
        'password' => true,
        'last_logged_in' => true,
        'code_forgot' => true,
        'created_code_forgot' => true,
        'time_input_code' => true,
        'time_input_pass' => true,
        'enabled' => true,
        'modified' => true,
        'modified_by' => true,
        'created' => true,
        'created_by' => true,
        'centers' => true,
        'roles' => true,
        'administrators_avatars' => true,
        'administrator_manage_centers' => true
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'token',
        'password',
    ];
}

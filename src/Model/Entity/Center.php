<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Center Entity
 *
 * @property int $id
 * @property string|null $code
 * @property string|null $account
 * @property string|null $username
 * @property string|null $bank_name
 * @property string $latitude
 * @property string $longitude
 * @property string|null $telephone
 * @property string|null $mobile_phone
 * @property string|null $email
 * @property string|null $whatsapp
 * @property int $district_id
 * @property int $sort
 * @property bool $enabled
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Administrator $created_by
 * @property \App\Model\Entity\Administrator $modified_by
 * @property \App\Model\Entity\District $district
 * @property \App\Model\Entity\Administrator[] $administrators
 * @property \App\Model\Entity\CenterFile[] $center_files
 * @property \App\Model\Entity\CenterLanguage[] $center_languages
 */
class Center extends Entity
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
        'code' => true,
        'account' => true,
        'username' => true,
        'bank_name' => true,
        'latitude' => true,
        'longitude' => true, 
        'district_id' => true,
        'sort' => true,
        'enabled' => true,
        'phone_us' => true,
        'fax_us' => true,
        'visit_us' => true,
        'mail_us' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'district' => true,
        'administrators' => true,
        'center_files' => true,
        'center_languages' => true,
        'staffs' => true,
    ];
}

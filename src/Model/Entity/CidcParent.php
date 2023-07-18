<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CidcParent Entity
 *
 * @property int $id
 * @property string $phone_number
 * @property bool $gender
 * @property string $email
 * @property bool $enabled
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Administrator $created_by
 * @property \App\Model\Entity\Administrator $modified_by
 */
class CidcParent extends Entity
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
        'gender' => true,
        'kid' => true, 
        'user_id' => true,  
        'address' => true, 
        'enabled' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'cidc_parent_languages' => true,
        'user'   => true
    ];
}

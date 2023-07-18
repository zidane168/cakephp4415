<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * StudentRegisterClass Entity
 *
 * @property int $id
 * @property int $cidc_class_id
 * @property int $kid_id
 * @property string $fee
 * @property bool $enabled
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $created_by
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $modified_by
 *
 * @property \App\Model\Entity\CidcClass $cidc_class
 * @property \App\Model\Entity\Kid $kid
 */
class StudentRegisterClass extends Entity
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
        'fee' => true,
        'status' => true,
        'order_id' => true,
        'enabled' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        
        // belong to
        'cidc_class' => true,
        'kid' => true,
        'order' => true, 

        // has many
        'student_register_class_receipts' => true
    ];
}

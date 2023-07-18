<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Order Entity
 *
 * @property int $id
 * @property string $order_number
 * @property string $total_fee
 * @property int $status
 * @property bool $enabled
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\StudentRegisterClass[] $student_register_classes
 */
class Order extends Entity
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
        'order_number' => true,
        'total_fee' => true,
        'status' => true,
        'enabled' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'student_register_classes' => true,
        'order_receipts' => true,
    ];
}

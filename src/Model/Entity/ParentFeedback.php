<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ParentFeedback Entity
 *
 * @property int $id
 * @property int $cidc_parent_id
 * @property string|null $feedback
 * @property bool $enabled
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $created_by
 * @property int|null $modified_by
 *
 * @property \App\Model\Entity\CidcParent $cidc_parent
 */
class ParentFeedback extends Entity
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
        'cidc_parent_id' => true,
        'feedback' => true,
        'enabled' => true,
        'created' => true,
        'modified' => true,
        'created_by' => true,
        'modified_by' => true,
        'cidc_parent' => true,
    ];
}

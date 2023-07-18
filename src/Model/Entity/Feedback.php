<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Feedback Entity
 *
 * @property int $id
 * @property bool $enabled
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\FeedbackLanguage[] $feedback_languages
 */
class Feedback extends Entity
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
        'enabled' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'feedback_languages' => true,
    ];
}

<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * FeedbackLanguage Entity
 *
 * @property int $id
 * @property int $feedback_id
 * @property string|null $alias
 * @property string|null $name
 *
 * @property \App\Model\Entity\Feedback $feedback
 */
class FeedbackLanguage extends Entity
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
        'feedback_id' => true,
        'alias' => true,
        'name' => true,
        'feedback' => true,
    ];
}

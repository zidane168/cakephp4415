<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PrivacyPolicyLanguage Entity
 *
 * @property int $id
 * @property int $privacy_policy_id
 * @property string|null $title
 * @property string|null $content
 *
 * @property \App\Model\Entity\PrivacyPolicy $privacy_policy
 */
class PrivacyPolicyLanguage extends Entity
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
        'privacy_policy_id' => true,
        'title' => true,
        'content' => true,
        'privacy_policy' => true,
        'alias' => true,
    ];
}

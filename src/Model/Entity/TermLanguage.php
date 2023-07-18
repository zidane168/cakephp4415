<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TermLanguage Entity
 *
 * @property int $id
 * @property int $term_id
 * @property string|null $title
 * @property string|null $content
 *
 * @property \App\Model\Entity\Term $term
 */
class TermLanguage extends Entity
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
        'term_id' => true,
        'title' => true,
        'content' => true,
        'term'  => true,
        'alias' => true
    ];
}

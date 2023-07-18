<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ContactLanguage Entity
 *
 * @property int $id
 * @property int $contact_id
 * @property string|null $alias
 * @property string|null $title
 * @property string|null $content
 *
 * @property \App\Model\Entity\Contact $contact
 */
class ContactLanguage extends Entity
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
        'contact_id' => true,
        'alias' => true,
        'title' => true,
        'content' => true,
        'contact' => true,
    ];
}

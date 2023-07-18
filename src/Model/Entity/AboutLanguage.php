<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AboutLanguage Entity
 *
 * @property int $id
 * @property int $about_id
 * @property string|null $alias
 * @property string|null $content
 *
 * @property \App\Model\Entity\About $about
 */
class AboutLanguage extends Entity
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
        'about_id' => true,
        'alias' => true,
        'content' => true,
        'about' => true,
    ];
}

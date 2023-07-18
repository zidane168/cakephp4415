<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Contact Entity
 *
 * @property int $id
 * @property bool $enabled
 * @property string|null $geo_lng
 * @property string|null $geo_lat
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $created_by
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $modified_by
 *
 * @property \App\Model\Entity\ContactImage[] $contact_images
 * @property \App\Model\Entity\ContactLanguage[] $contact_languages
 */
class Contact extends Entity
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
        'geo_lng' => true,
        'geo_lat' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'contact_images' => true,
        'contact_languages' => true,
    ];
}

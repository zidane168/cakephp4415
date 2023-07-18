<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * NewsLanguage Entity
 *
 * @property int $id
 * @property int $news_id
 * @property string $alias
 * @property string $title
 * @property string $content
 * @property string $author
 *
 * @property \App\Model\Entity\News $news
 */
class NewsLanguage extends Entity
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
        'news_id' => true,
        'alias' => true,
        'title' => true,
        'content' => true,
        'author' => true,
        'news' => true,
    ];
}

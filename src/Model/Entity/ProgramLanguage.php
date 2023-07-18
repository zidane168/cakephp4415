<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ProgramLanguage Entity
 *
 * @property int $id
 * @property int $program_id
 * @property string $alias
 * @property string $name
 * @property string|null $description
 *
 * @property \App\Model\Entity\Program $program
 */
class ProgramLanguage extends Entity
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
        'program_id' => true,
        'alias' => true,
        'name' => true,
        'description' => true,
        'program' => true,
    ];
}

<?php

// ---------------------------------------------------------------------------------------------------------
// -- Author:       ViLH
// -- Description:  Use 'contain' record who created_by, modified_by => CreatedBy, ModifiedBy
// ---------------------------------------------------------------------------------------------------------
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Table;

class WhoDidItBehavior extends Behavior {
    
    private static $config = [
        'createdByField'    => 'created_by',
        'modifiedByField'   => 'modified_by',
        'userModel'         => 'Administrators',
        'autoBind'          => true,
    ];

   /**
     * Constructor.
     *
     * @param \Cake\ORM\Table $table The table this behavior is attached to
     * @param array $config Configuration array for this behavior
     */
    public function __construct(Table $table, array $config = [])
    {
        $this->_table = $table;
        parent::__construct($this->_table, $config);

        if (self::$config['autoBind']) {
            if ($this->_table->hasField(self::$config['createdByField'])) {
                $this->_table->belongsTo('CreatedBy', [
                    'className'         => self::$config['userModel'],
                    'foreignKey'        => self::$config['createdByField'],
                    'propertyName'      => 'created_by',
                ]);
            }

            if ($this->_table->hasField(self::$config['modifiedByField'])) {
                $this->_table->belongsTo('ModifiedBy', [
                    'className'         => self::$config['userModel'],
                    'foreignKey'        => self::$config['modifiedByField'],
                    'propertyName'      => 'modified_by',
                ]);
            }
        }
    }
}
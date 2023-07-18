<?php
declare(strict_types=1);

namespace App\View\Cell;

use App\MyHelper\MyHelper;
use Cake\View\Cell;

/**
 * OrderStatus cell
 */
class OrderStatusCell extends Cell
{
    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];

    /**
     * Initialization logic run at the end of object construction.
     *
     * @return void
     */
    public function initialize(): void
    {
    }

    /**
     * Default display method.
     *
     * @return void
     */
    public function display($status, $is_required = true)   // must same as params from template (render)
    {
        $result = MyHelper::getStatusPaidUnpaid();
        $this->set(compact('status', 'result', 'is_required'));
    }
}

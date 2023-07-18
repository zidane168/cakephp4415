<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order[]|\Cake\Collection\CollectionInterface $orders
 */
use App\MyHelper\MyHelper;
?>

<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/order_filter', array(
                'data_search' => $data_search
            ));
            ?>
        </div>
    </div>
</div>

<div class="container-fluid card full-border">

    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __d('order', 'orders'); ?> </h3>

                    <!--
                    <?php if (isset($permissions['Orders']['add']) && ($permissions['Orders']['add'] == true)) { ?>

                        <div class="box-tools ml-auto p-1">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                                'escape' => false,
                                'class' => 'btn btn-primary button'
                            ]) ?>
                        </div>
                    <?php  }  ?> -->
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'Order'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('Orders.id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('Orders.order_number', __d('order', 'order_number')) ?></th>
                                <th><?= $this->Paginator->sort('Orders.total_fee', __d('order', 'total_fee')) ?></th>
                                <th><?= $this->Paginator->sort('Orders.status', __('status')) ?></th> 
                                <th><?= $this->Paginator->sort('Orders.created', __('created')) ?></th> 
                                <th><?= $this->Paginator->sort('Orders.modified', __('modified')) ?></th> 
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($orders as $order) : ?>
                                <tr>
                                    <td><?= $this->Number->format($order->id) ?></td>
                                    <td><?= h($order->order_number) ?></td>
                                    <td><?= $this->Number->format($order->total_fee) ?></td>
                                    <td><?= $this->element('view_paid_unpaid', array('_check' => $order->status)) ?></td>
                                    <td><?= h($order->created) ?></td>
                                    <td><?= h($order->modified) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['Orders']['view']) && ($permissions['Orders']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $order->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        if ( $order->status == MyHelper::UNPAID  &&
                                            isset($permissions['Orders']['edit']) && ($permissions['Orders']['edit'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $order->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
                                        }

                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?= $this->element('Paginator'); ?>
            </div><!-- box, box-primary -->
        </div><!-- .col-12 -->
    </div><!-- row -->
</div> <!-- container -->
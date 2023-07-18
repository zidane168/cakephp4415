<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface[]|\Cake\Collection\CollectionInterface $rescheduleHistories
 */
?>
<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/reschedule_history_filter', array(
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
                    <h3 class="box-title"> <?php echo __d('staff', 'reschedule_histories'); ?> </h3>

                    <?php if (isset($permissions['RescheduleHistories']['add']) && ($permissions['RescheduleHistories']['add'] == true)) { ?>

                        <div class="box-tools ml-auto p-1">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                                'escape' => false,
                                'class' => 'btn btn-primary button'
                            ]) ?>
                        </div>
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'Reschedule History'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('from_cidc_class_id', __d('cidcclass', 'from_cidc_class')) ?></th>
                                <th><?= $this->Paginator->sort('to_cidc_class_id', __d('cidcclass', 'to_cidc_class')) ?></th>
                                <th><?= $this->Paginator->sort('kid_id', __d('parent', 'kid')) ?></th>
                                <th><?= $this->Paginator->sort('date_from', __('date_from')) ?></th>
                                <th><?= $this->Paginator->sort('date_to', __('date_to')) ?></th>
                                <th><?= $this->Paginator->sort('status', __('status')) ?></th>
                                <th><?= $this->Paginator->sort('created', __('created')) ?></th>
                                <th><?= $this->Paginator->sort('modified', __('modified')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($rescheduleHistories as $rescheduleHistory) : ?>
                                <tr>
                                    <td><?= $this->Number->format($rescheduleHistory->id) ?></td>
                                    <td><?= $this->Html->link("[" .$rescheduleHistory->from_cidc_class_id . "] " . $cidcClasses[$rescheduleHistory->from_cidc_class_id], ['controller' => 'CidcClasses', 'action' => 'view', $rescheduleHistory->from_cidc_class_id]) ?></td>
                                    <td><?= $this->Html->link("[" .$rescheduleHistory->to_cidc_class_id . "] " . $cidcClasses[$rescheduleHistory->to_cidc_class_id], ['controller' => 'CidcClasses', 'action' => 'view', $rescheduleHistory->to_cidc_class_id]) ?></td>
                                    <td><?= $this->Html->link($kids[$rescheduleHistory->kid_id], ['controller' => 'Kids', 'action' => 'view', $rescheduleHistory->kid_id]) ?></td>
                                    <td><?= h($rescheduleHistory->date_from) ?></td>
                                    <td><?= h($rescheduleHistory->date_to) ?></td>
                                    <td><?= $this->element('view_pending_approval', array('_check' => $rescheduleHistory->status)) ?></td>
                                    <td><?= h($rescheduleHistory->created) ?></td>
                                    <td><?= h($rescheduleHistory->modified) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['RescheduleHistories']['view']) && ($permissions['RescheduleHistories']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $rescheduleHistory->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        if (isset($permissions['RescheduleHistories']['edit']) && ($permissions['RescheduleHistories']['edit'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $rescheduleHistory->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
                                        }

                                        // if (isset($permissions['RescheduleHistories']['delete']) && ($permissions['RescheduleHistories']['delete'] == true)) {

                                        //     echo $this->Form->postLink(
                                        //         '<i class="far fa-trash-alt"></i>',
                                        //         array('action' => 'delete',  $rescheduleHistory->id),
                                        //         array(
                                        //             'escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle' => 'tooltip', 'title' => __('delete'),
                                        //             'confirm' => __('delete_message',  $rescheduleHistory->id)
                                        //         )
                                        //     );
                                        // }

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
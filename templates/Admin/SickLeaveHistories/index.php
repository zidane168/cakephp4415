<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface[]|\Cake\Collection\CollectionInterface $sickLeaveHistories
 */
?>

<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/sick_leave_history_filter', array(
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
                    <h3 class="box-title"> <?php echo __('sickLeaveHistory'); ?> </h3>

                    <?php if (isset($permissions['SickLeaveHistories']['add']) && ($permissions['SickLeaveHistories']['add'] == true)) { ?>

                        <div class="box-tools ml-auto p-1">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                                'escape' => false,
                                'class' => 'btn btn-primary button'
                            ]) ?>
                        </div>
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'Sick Leave History'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('cidc_class_id', __d('center', 'cidc_class')) ?></th>
                                <th><?= $this->Paginator->sort('kid_id', __d('parent', 'kid')) ?></th>
                                <th><?= $this->Paginator->sort('date', __('date')) ?></th>
                                <th><?= $this->Paginator->sort('time', __('time')) ?></th>
                                <th><?= $this->Paginator->sort('enabled', __('enabled')) ?></th>
                                <th><?= $this->Paginator->sort('created', __('created')) ?></th>
                                <th><?= $this->Paginator->sort('modified', __('modified')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($sickLeaveHistories as $sickLeaveHistory) : ?>
                                <tr>
                                    <td><?= $this->Number->format($sickLeaveHistory->id) ?></td>
                                    <td><?= $this->Html->link($cidcClasses[$sickLeaveHistory->cidc_class_id], ['controller' => 'CidcClasses', 'action' => 'view', $sickLeaveHistory->cidc_class_id]) ?></td>
                                    <td><?= $this->Html->link($kids[$sickLeaveHistory->kid_id], ['controller' => 'Kids', 'action' => 'view', $sickLeaveHistory->kid_id]) ?></td>
                                    <td><?= h($sickLeaveHistory->date->format('Y-m-d')) ?></td>
                                    <td><?= h($sickLeaveHistory->time->format('H:i')) ?></td>
                                    <td><?= $this->element('view_check_ico', array('_check' => $sickLeaveHistory->enabled)) ?></td>
                                    <td><?= h($sickLeaveHistory->created) ?></td>
                                    <td><?= h($sickLeaveHistory->modified) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['SickLeaveHistories']['view']) && ($permissions['SickLeaveHistories']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $sickLeaveHistory->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        if (isset($permissions['SickLeaveHistories']['edit']) && ($permissions['SickLeaveHistories']['edit'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $sickLeaveHistory->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
                                        }

                                        if (isset($permissions['SickLeaveHistories']['delete']) && ($permissions['SickLeaveHistories']['delete'] == true)) {

                                            echo $this->Form->postLink(
                                                '<i class="far fa-trash-alt"></i>',
                                                array('action' => 'delete',  $sickLeaveHistory->id),
                                                array(
                                                    'escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle' => 'tooltip', 'title' => __('delete'),
                                                    'confirm' => __('delete_message',  $sickLeaveHistory->id)
                                                )
                                            );
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
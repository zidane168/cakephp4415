<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $rescheduleHistory
 */
?>

<div class="container-fluid card full-border">


    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __d('staff', 'reschedule_history'); ?> </h3>

                    <?php
                    if (isset($permissions['RescheduleHistories']['edit']) && ($permissions['RescheduleHistories']['edit'] == true)) {
                    ?>

                        <div class="box-tools ml-auto p-1">
                            <?php echo $this->Html->link('<i class="fa fa-pencil"></i> ' . __('edit'), array('action' => 'edit', $rescheduleHistory->id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                        </div>

                    <?php
                    }
                    ?>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th><?= __('Id') ?></th>
                            <td><?= $this->Number->format($rescheduleHistory->id) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('cidcclass', 'from_cidc_class') ?></th>
                            <td><?= $this->Html->link($cidcClasses[$rescheduleHistory->from_cidc_class_id], ['controller' => 'CidcClasses', 'action' => 'view', $rescheduleHistory->from_cidc_class_id]) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('cidcclass', 'to_cidc_class') ?></th>
                            <td><?= $this->Html->link($cidcClasses[$rescheduleHistory->to_cidc_class_id], ['controller' => 'CidcClasses', 'action' => 'view', $rescheduleHistory->to_cidc_class_id]) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('parent', 'kid') ?></th>
                            <td><?= $this->Html->link($kids[$rescheduleHistory->kid_id], ['controller' => 'Kids', 'action' => 'view', $rescheduleHistory->kid_id]) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('status') ?></th>
                            <td><?= $this->element('view_pending_approval', array('_check' => $rescheduleHistory->status)) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('date_from') ?></th>
                            <td><?= h($rescheduleHistory->date_from) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('date_to') ?></th>
                            <td><?= h($rescheduleHistory->date_to) ?></td>
                        </tr>

                        <tr>
                            <th><?= __d('staff', 'reason') ?></th>
                            <td><?= h($rescheduleHistory->reason) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'files') ?></th>
                            <td><?php
                                if (isset($rescheduleHistory->reschedule_history_files)) {
                                    foreach ($rescheduleHistory->reschedule_history_files as $file) {
                                ?>
                                        <a href=<?= $url . $file->path ?> download=<?= $file->file_name ?>>
                                            <button class="btn btn-primary"><?= $file->file_name ?></button>

                                        </a>
                                <?php
                                    }
                                } else {
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                        echo $this->element('admin/filter/common/enabled_info', array(
                            'object' => $rescheduleHistory,
                        ));
                        echo $this->element('admin/filter/common/admin_info', array(
                            'object' => $rescheduleHistory,
                        ));
                        ?>
                    </table>


                </div>
            </div>
        </div>
    </div>

</div>
<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $sickLeaveHistory
 */
?>

<div class="container-fluid card full-border">


    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __d('staff', 'edit_sick_leave_history'); ?> </h3>

                    <?php
                    if (isset($permissions['SickLeaveHistories']['edit']) && ($permissions['SickLeaveHistories']['edit'] == true)) {
                    ?>

                        <div class="box-tools ml-auto p-1">
                            <?php echo $this->Html->link('<i class="fa fa-pencil"></i> ' . __('edit'), array('action' => 'edit', $sickLeaveHistory->id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                        </div>

                    <?php
                    }
                    ?>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th><?= __('Id') ?></th>
                            <td><?= $this->Number->format($sickLeaveHistory->id) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'cidc_class') ?></th>
                            <td><?= $this->Html->link($cidcClasses[$sickLeaveHistory->cidc_class_id], ['controller' => 'CidcClasses', 'action' => 'view', $sickLeaveHistory->cidc_class_id]) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('parent', 'kid') ?></th>
                            <td><?= $this->Html->link($kids[$sickLeaveHistory->kid_id], ['controller' => 'Kids', 'action' => 'view', $sickLeaveHistory->kid_id]) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('date') ?></th>
                            <td><?= h($sickLeaveHistory->date->format('Y-m-d')) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('time') ?></th>
                            <td><?= h($sickLeaveHistory->time->format('H:i')) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('staff', 'reason') ?></th>
                            <td><?= h($sickLeaveHistory->reason) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'files') ?></th>
                            <td><?php
                                if (isset($sickLeaveHistory->sick_leave_history_files)) {
                                    foreach ($sickLeaveHistory->sick_leave_history_files as $file) {
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
                            'object' => $sickLeaveHistory,
                        ));
                        echo $this->element('admin/filter/common/admin_info', array(
                            'object' => $sickLeaveHistory,
                        ));
                        ?>
                    </table>



                </div>
            </div>
        </div>
    </div>

</div>
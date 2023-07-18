<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\StudentRegisterClass $studentRegisterClass
 */
?>

<div class="container-fluid card full-border">


    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __d('cidcclass', 'student_register_class'); ?> </h3>

                    <?php
                    if (isset($permissions['Student Register Classes']['edit']) && ($permissions['Student Register Classes']['edit'] == true)) {
                    ?>

                        <div class="box-tools ml-auto p-1">
                            <?php echo $this->Html->link('<i class="fa fa-pencil"></i> ' . __('edit'), array('action' => 'edit', $studentRegisterClass->id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                        </div>

                    <?php
                    }
                    ?>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th><?= __('id') ?></th>
                            <td><?= $this->Number->format($studentRegisterClass->id) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'cidc_class') ?></th>
                            <td><?= $studentRegisterClass->has('cidc_class') ? $this->Html->link($studentRegisterClass->cidc_class->name, ['controller' => 'CidcClasses', 'action' => 'view', $studentRegisterClass->cidc_class->id]) : '' ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('parent', 'kid') ?></th>
                            <td><?= $studentRegisterClass->has('kid') ? $this->Html->link($studentRegisterClass->kid->id, ['controller' => 'Kids', 'action' => 'view', $studentRegisterClass->kid->id]) : '' ?></td>
                        </tr>

                        <tr>
                            <th><?= __d('center', 'fee') ?></th>
                            <td><?= $this->Number->format($studentRegisterClass->fee) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('order', 'order_number') ?></th>
                            <td><?= h($studentRegisterClass->order->order_number) ?></td>
                        </tr> 
                        <?php
                        echo $this->element('admin/filter/common/enabled_info', array(
                            'object' => $studentRegisterClass,
                        ));
                        echo $this->element('admin/filter/common/admin_info', array(
                            'object' => $studentRegisterClass,
                        ));
                        ?>
                    </table>


                </div>
            </div>
        </div>
    </div>

</div>
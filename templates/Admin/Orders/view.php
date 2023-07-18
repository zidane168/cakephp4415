<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order $order
 */
?>

<div class="container-fluid card full-border"> 
    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __d('order', 'order'); ?> </h3>
                    <?php if (isset($permissions['Orders']['edit']) && ($permissions['Orders']['edit'] == true)) {
                    ?>

                        <div class="box-tools ml-auto p-1">
                            <?php echo $this->Html->link('<i class="fa fa-pencil"></i> ' . __('edit'), array('action' => 'edit', $order->id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                        </div>

                    <?php }  ?>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th><?= __('id') ?></th>
                            <td><?= $this->Number->format($order->id) ?></td>
                        </tr> 
                        <tr>
                            <th><?= __d('order', 'order_number') ?></th>
                            <td><?= h($order->order_number) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('order', 'total_fee') ?></th>
                            <td><?= $this->Number->format($order->total_fee) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('order', 'status') ?></th>
                            <td><?= $this->element('view_paid_unpaid', array('_check' => $order->status)) ?></td>
                        </tr>

                        <tr>
                            <th><?= __d('center', 'file') ?></th>
                            <td>
                                <?php  foreach ($order->order_receipts as $file) {  ?>
                                        <a href=<?= $url . $file->path ?> download=<?= $file->file_name ?>>
                                            <button class="btn btn-primary"><?= $file->file_name ?></button>

                                        </a>
                                <?php }   ?>
                            </td>
                        </tr>
                        <?php
                        echo $this->element('admin/filter/common/enabled_info', array(
                            'object' => $order,
                        ));
                        echo $this->element('admin/filter/common/admin_info', array(
                            'object' => $order,
                        ));
                        ?>
                    </table> 
                </div>
            </div>
        </div>
    </div> 
</div>


<div class="container-fluid card full-border"> 
    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __d('cidcclass', 'student_register_classes'); ?> </h3> 
                </div>

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'Student Register Class'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= __('id')  ?></th>
                                <th><?= __d('center', 'cidc_class') ?></th>
                                <th><?= __('date') ?></th>
                                <th><?= __('time') ?></th>
                                <th><?= __d('parent', 'kid')  ?></th>
                                <th><?= __d('center', 'fee')  ?></th> 

                                <th><?= __('created')  ?></th>
                                <th><?= __('modified')  ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($order->student_register_classes as $studentRegisterClass) : 
                                
                                $date =  $studentRegisterClass->cidc_class->start_date->format('Y-m-d') . ' -> ' . $studentRegisterClass->cidc_class->end_date->format('Y-m-d');
                                $time =  $studentRegisterClass->cidc_class->start_time->format('H:i') . ' -> ' . $studentRegisterClass->cidc_class->end_time->format('H:i');
                                ?>
                                <tr>
                                    <td><?= $this->Number->format($studentRegisterClass->id) ?></td>
                                    <td><?= $studentRegisterClass->has('cidc_class') ? $this->Html->link($studentRegisterClass->cidc_class->name, ['controller' => 'CidcClasses', 'action' => 'view', $studentRegisterClass->cidc_class->id]) : '' ?></td>
                                    <td><?= $date;  ?></td>
                                    <td><?= $time;  ?></td>
                                    <td><?= $studentRegisterClass->has('kid') ? $this->Html->link($studentRegisterClass->kid->kid_languages[0]->name, ['controller' => 'Kids', 'action' => 'view', $studentRegisterClass->kid->id]) : '' ?></td>
                                    <td><?= number_format($studentRegisterClass->fee, 2) ?></td> 

                                    <td><?= h($studentRegisterClass->created) ?></td>
                                    <td><?= h($studentRegisterClass->modified) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['StudentRegisterClasses']['view']) && ($permissions['StudentRegisterClasses']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array(
                                                'controller' => 'student_register_classes',
                                                'action' => 'view', $studentRegisterClass->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }  
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> 
</div>
<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\StudentRegisterClass[]|\Cake\Collection\CollectionInterface $studentRegisterClasses
 * 
 * is attended == 0 -> can delete, otherwise cannot , 
 * paid cannot delete, cannot edit
*/

use App\MyHelper\MyHelper;
?>
<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/student_register_class_filter', array(
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
                    <h3 class="box-title"> <?php echo __d('cidcclass', 'student_register_classes'); ?> </h3>

                    <!-- 
                    <?php if (isset($permissions['StudentRegisterClasses']['add']) && ($permissions['StudentRegisterClasses']['add'] == true)) { ?>

                        <div class="box-tools ml-auto p-1">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                                'escape' => false,
                                'class' => 'btn btn-primary button'
                            ]) ?>
                        </div>
                    <?php  }  ?> 
                    -->
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'Student Register Class'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('StudentRegisterClasses.id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('StudentRegisterClasses.cidc_class_id', __d('center', 'cidc_class')) ?></th>
                                <th><?= __('date') ?></th>
                                <th><?= __('time') ?></th>
                                <th><?= $this->Paginator->sort('StudentRegisterClasses.kid_id', __d('parent', 'kid')) ?></th>
                                <th><?= $this->Paginator->sort('StudentRegisterClasses.fee', __d('center', 'fee')) ?></th> 

                                <th><?= $this->Paginator->sort('StudentRegisterClasses.created', __('created')) ?></th>
                                <th><?= $this->Paginator->sort('StudentRegisterClasses.modified', __('modified')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($studentRegisterClasses as $studentRegisterClass) : 
                                
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
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $studentRegisterClass->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        // if (
                                        //     ($studentRegisterClass->status == MyHelper::UNPAID) &&
                                        //     (isset($permissions['StudentRegisterClasses']['edit']) && ($permissions['StudentRegisterClasses']['edit'] == true))) {
                                        //     echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $studentRegisterClass->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
                                        // }

                                        // if ( $studentRegisterClass->is_attended == 0 && isset($permissions['StudentRegisterClasses']['delete']) && ($permissions['StudentRegisterClasses']['delete'] == true)
                                        //     || ($studentRegisterClass->status == MyHelper::UNPAID)
                                            
                                        //     ) {

                                        //     echo $this->Form->postLink(
                                        //         '<i class="far fa-trash-alt"></i>',
                                        //         array('action' => 'delete',  $studentRegisterClass->id),
                                        //         array(
                                        //             'escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle' => 'tooltip', 'title' => __('delete'),
                                        //             'confirm' => __('delete_message',  $studentRegisterClass->id)
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
 
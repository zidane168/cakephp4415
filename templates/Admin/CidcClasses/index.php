<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface[]|\Cake\Collection\CollectionInterface $cidcClasses
 */
 

?>
<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/cidc_class_filter', array(
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
                    <h3 class="box-title"> <?php echo __d('center', 'cidc_classes'); ?> </h3>

                    <?php if (isset($permissions['CidcClasses']['add']) && ($permissions['CidcClasses']['add'] == true)) { ?>

                        <div class="box-tools ml-auto p-1">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                                'escape' => false,
                                'class' => 'btn btn-primary button'
                            ]) ?>
                        </div>
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'Cidc Class'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('CidcClasses.id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('CidcClasses.name', __('name')) ?></th>
                                <th><?= $this->Paginator->sort('CidcClasses.code', __('code')) ?></th>

                                <th><?= $this->Paginator->sort('CidcClasses.program_id', __d('center', 'program')) ?></th>
                                <th><?= $this->Paginator->sort('CidcClasses.course_id', __d('center', 'course')) ?></th>
                                <th><?= $this->Paginator->sort('CidcClasses.center_id', __d('center', 'center')) ?></th>
                                <th><?= $this->Paginator->sort('CidcClasses.status', __('status')) ?></th>
                                <th><?= $this->Paginator->sort('CidcClasses.id', __d('cidcclass', 'target_audience')) ?></th>
                                <th><?= $this->Paginator->sort('CidcClasses.fee', __d('center', 'fee')) ?></th>
                                <th><?= $this->Paginator->sort('CidcClasses.number_of_register', __d('cidcclass', 'number_of_register')) ?></th>
                                <th><?= $this->Paginator->sort('CidcClasses.class_type_id', __d('center', 'class_type')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php 
                             
                            foreach ($cidcClasses as $cidcClass) :
                                ?>
                                <tr>
                                    <td><?= $this->Number->format($cidcClass->id) ?></td>
                                    <td><?= h($cidcClass->name) ?></td>
                                    <td><?= h($cidcClass->code) ?></td>
                                    <td>
                                        <?= $this->Html->link(
                                            h($cidcClass->program->program_languages[0]['name']),
                                            ['controller' => 'Programs', 'action' => 'view', $cidcClass->program_id]
                                        )  ?>
                                    </td>
                                    <td>
                                        <?= $this->Html->link(
                                            h($cidcClass->course->course_languages[0]['name']),
                                            ['controller' => 'Programs', 'action' => 'view', $cidcClass->course_id]
                                        )  ?>
                                    </td>
                                    <td>
                                        <?= $this->Html->link(
                                            h($cidcClass->center->center_languages[0]['name']),
                                            ['controller' => 'Programs', 'action' => 'view', $cidcClass->center_id]
                                        )  ?>
                                    </td>
                                    <td class="vertical-middle">
                                        <?php
                                            $label = "";

                                            if ($cidcClass->status === $PENDING) { 
                                                $label = "label-pending";
                                            
                                            } elseif ($cidcClass->status === $PUBLISHED) {  
                                                $label = "label-published";
                                            
                                            } elseif ($cidcClass->status === $UNPUBLISHED) { 
                                                $label = "label-unpublished";
                                            
                                            } elseif ($cidcClass->status === $COMPLETED) {  
                                                $label = "label-completed";
                                            
                                            } 

                                            echo "<span class='" . $label . "'>" .  $status[$cidcClass->status] . "</span>"
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        $unit = $target_units[$cidcClass->target_unit];
                                        echo ("$cidcClass->target_audience_from - $cidcClass->target_audience_to $unit")
                                        ?>
                                    </td>

                                    <td><?= h(number_format($cidcClass->fee, 2)) ?></td>
                                    <td>
                                        <?php
                                        echo (number_format($cidcClass->number_of_register, 0))
                                        ?>
                                    </td>
                                    <td>
                                        <?= $this->Html->link(
                                            h($cidcClass->class_type->class_type_languages[0]['name']),
                                            ['controller' => 'ClassTypes', 'action' => 'view', $cidcClass->class_type_id]
                                        )  ?>
                                    </td>

                                    <td>

                                        <?php
                                        if ($cidcClass->status == $PUBLISHED) {
                                            echo $this->Html->link('<i class="fa fa-eye""></i>', array('controller' => 'StudentAttendedClasses' ,'action' => 'index', $cidcClass->id), array('class' => 'btn btn-info btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __d('cidc', 'attended')));
                                        }

                                        if (isset($permissions['CidcClasses']['view']) && ($permissions['CidcClasses']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $cidcClass->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        if (isset($permissions['CidcClasses']['edit']) && ($permissions['CidcClasses']['edit'] == true) && $cidcClass->status !== $PUBLISHED && $cidcClass->status !== $COMPLETED  ) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $cidcClass->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
                                        }

                                        if (isset($permissions['CidcClasses']['delete']) && ($permissions['CidcClasses']['delete'] == true) && $cidcClass->status !== $PUBLISHED && $cidcClass->status !== $COMPLETED ) {

                                            echo $this->Form->postLink(
                                                '<i class="far fa-trash-alt"></i>',
                                                array('action' => 'delete',  $cidcClass->id),
                                                array(
                                                    'escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle' => 'tooltip', 'title' => __('delete'),
                                                    'confirm' => __('delete_message',  $cidcClass->id)
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
<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface[]|\Cake\Collection\CollectionInterface $classTypes
 */
?>
<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/class_type_filter', array(
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
                    <h3 class="box-title"> <?php echo __d('center', 'class_type'); ?> </h3>

                    <?php if (isset($permissions['ClassTypes']['add']) && ($permissions['ClassTypes']['add'] == true)) { ?>

                        <!-- <div class="box-tools ml-auto p-1">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                                'escape' => false,
                                'class' => 'btn btn-primary button'
                            ]) ?>
                        </div> -->
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'Class Type'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('ClassTypes.id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('ClassTypes.id', __('name')) ?></th>
                                <!-- <th><?= h(__('enabled')) ?></th>-->
                                <th><?= $this->Paginator->sort('ClassTypes.created', __('created')) ?></th>
                                <th><?= $this->Paginator->sort('ClassTypes.modified', __('modified')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($classTypes as $classType) : ?>
                                <tr>
                                    <td><?= $this->Number->format($classType->id) ?></td>
                                    <td class="text-center"><?= h($classType->ClassTypeLanguages['name']) ?></td>
                                    <!-- <td><?= $this->element('view_check_ico', array('_check' => $classType->enabled)) ?></td> -->

                                    <td><?= h($classType->created) ?></td>
                                    <td><?= h($classType->modified) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['ClassTypes']['view']) && ($permissions['ClassTypes']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $classType->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        if (isset($permissions['ClassTypes']['edit']) && ($permissions['ClassTypes']['edit'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $classType->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
                                        }

                                        if (isset($permissions['ClassTypes']['delete']) && ($permissions['ClassTypes']['delete'] == true)) {

                                            // echo $this->Form->postLink(
                                            //     '<i class="far fa-trash-alt"></i>',
                                            //     array('action' => 'delete',  $classType->id),
                                            //     array(
                                            //         'escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle' => 'tooltip', 'title' => __('delete'),
                                            //         'confirm' => __('delete_message',  $classType->id)
                                            //     )
                                            // );
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
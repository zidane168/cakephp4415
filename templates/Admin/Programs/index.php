<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface[]|\Cake\Collection\CollectionInterface $programs
 */
?>
<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/program_filter', array(
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
                    <h3 class="box-title"> <?php echo __d('center', 'program'); ?> </h3>

                    <?php if (isset($permissions['Programs']['add']) && ($permissions['Programs']['add'] == true)) { ?>

                        <div class="box-tools ml-auto p-1">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                                'escape' => false,
                                'class' => 'btn btn-primary button'
                            ]) ?>
                        </div>
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'Program'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('Programs.id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('Programs.name', __('name')) ?></th>
                                <th><?= $this->Paginator->sort('Programs.title_color', __d('center', 'title_color')) ?></th>
                                <th><?= $this->Paginator->sort('Programs.background_color', __d('center', 'background_color')) ?></th>
                                <th><?= h(__('enabled')) ?></th>
                                <th><?= $this->Paginator->sort('Programs.created', __('created')) ?></th>
                                <th><?= $this->Paginator->sort('Programs.modified', __('modified')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($programs as $program) : ?>
                                <tr>
                                    <td><?= $this->Number->format($program->id) ?></td>
                                    <td><?= h($program['ProgramLanguages']['name']) ?></td>
                                    <td> <input disabled type="color" value="<?= $program['title_color'] ?>"> </td>
                                    <td> <input disabled type="color" value="<?= $program['background_color'] ?>"> </td>
                                    <td><?= $this->element('view_check_ico', array('_check' => $program->enabled)) ?></td>
                                    <td><?= h($program->created) ?></td>
                                    <td><?= h($program->modified) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['Programs']['view']) && ($permissions['Programs']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $program->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        if (isset($permissions['Programs']['edit']) && ($permissions['Programs']['edit'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $program->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
                                        }

                                        if (isset($permissions['Programs']['delete']) && ($permissions['Programs']['delete'] == true)) {

                                            echo $this->Form->postLink(
                                                '<i class="far fa-trash-alt"></i>',
                                                array('action' => 'delete',  $program->id),
                                                array(
                                                    'escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle' => 'tooltip', 'title' => __('delete'),
                                                    'confirm' => __('delete_message',  $program->id)
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
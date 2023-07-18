<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Center[]|\Cake\Collection\CollectionInterface $centers
 */
?>

<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/center_filter', array(
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
                    <h3 class="box-title"> <?php echo __d('center', 'centers'); ?> </h3>

                    <!--
                    <?php if (isset($permissions['Centers']['add']) && ($permissions['Centers']['add'] == true) && $is_manage_all_center_data == true) { ?>

                        <div class="p-1 ml-auto box-tools">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                                'escape' => false,
                                'class' => 'btn btn-primary button'
                            ]) ?>
                        </div>
                    <?php  }  ?> -->
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'Center'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('Centers.id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('Centers.id', __('name')) ?></th>
                                <th><?= $this->Paginator->sort('Centers.id', __('address')) ?></th>
                                <th><?= $this->Paginator->sort('Centers.phone_us',  __d('center', 'phone_us')) ?></th>
                                <th><?= $this->Paginator->sort('Centers.fax_us',    __d('center', 'fax_us')) ?></th>
                                <th><?= $this->Paginator->sort('Centers.visit_us',  __d('center', 'visit_us')) ?></th>
                                <th><?= $this->Paginator->sort('Centers.mail_us',   __d('center', 'mail_us')) ?></th>
                                <th><?= h(__('enabled')) ?></th>
                                <th><?= $this->Paginator->sort('Centers.created', __('created')) ?></th>
                                <th><?= $this->Paginator->sort('Centers.modified', __('modified')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($centers as $center) : ?>
                                <tr>
                                    <td><?= $this->Number->format($center->id) ?></td>
                                    <td><?= h($center->center_languages[0]->name) ?></td>
                                    <td><?= h($center->center_languages[0]->address) ?></td>
                                    <td><?= h($center->phone_us) ?></td>
                                    <td><?= h($center->fax_us) ?></td>
                                    <td><?= h($center->visit_us) ?></td>
                                    <td><?= h($center->mail_us) ?></td>
                                    <td><?= $this->element('view_check_ico', array('_check' => $center->enabled)) ?></td>
                                    <td><?= h($center->created) ?></td>
                                    <td><?= h($center->modified) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['Centers']['view']) && ($permissions['Centers']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $center->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        if (isset($permissions['Centers']['edit']) && ($permissions['Centers']['edit'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $center->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
                                        }

                                        if (isset($permissions['Centers']['delete']) && ($permissions['Centers']['delete'] == true)) {

                                            echo $this->Form->postLink(
                                                '<i class="far fa-trash-alt"></i>',
                                                array('action' => 'delete',  $center->id),
                                                array(
                                                    'escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle' => 'tooltip', 'title' => __('delete'),
                                                    'confirm' => __('delete_message',  $center->id)
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
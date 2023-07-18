<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CidcHoliday[]|\Cake\Collection\CollectionInterface $cidcHolidays
 */
?>

<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/cidc_holiday_filter', array(
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
                    <h3 class="box-title"> <?php echo __d('setting', 'cidc_holidays'); ?> </h3>

                    <?php if (isset($permissions['CidcHolidays']['add']) && ($permissions['CidcHolidays']['add'] == true)) { ?>

                        <div class="box-tools ml-auto p-1">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                                'escape' => false,
                                'class' => 'btn btn-primary button'
                            ]) ?>
                        </div>
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'Cidc Holiday'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('CidcHolidays.id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('CidcHolidays.date', __('date')) ?></th>
                                <th><?= $this->Paginator->sort('CidcHolidays.description', __('description')) ?></th>
                                <th><?= $this->Paginator->sort('CidcHolidays.enabled', __('enabled')) ?></th>
                                <th><?= $this->Paginator->sort('CidcHolidays.created', __('created')) ?></th>
                                <th><?= $this->Paginator->sort('CidcHolidays.modified', __('modified')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($cidcHolidays as $cidcHoliday) : ?>
                                <tr>
                                    <td><?= $this->Number->format($cidcHoliday->id) ?></td>
                                    <td><?= h($cidcHoliday->date->format('Y-m-d')) ?></td>
                                    <td><?= h($cidcHoliday->description) ?></td>
                                    <td><?= $this->element('view_check_ico', array('_check' => $cidcHoliday->enabled)) ?></td>
                                    <td><?= h($cidcHoliday->created) ?></td>
                                    <td><?= h($cidcHoliday->modified) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['CidcHolidays']['view']) && ($permissions['CidcHolidays']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $cidcHoliday->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        if (isset($permissions['CidcHolidays']['edit']) && ($permissions['CidcHolidays']['edit'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $cidcHoliday->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
                                        }

                                        if (isset($permissions['CidcHolidays']['delete']) && ($permissions['CidcHolidays']['delete'] == true)) {

                                            echo $this->Form->postLink(
                                                '<i class="far fa-trash-alt"></i>',
                                                array('action' => 'delete',  $cidcHoliday->id),
                                                array(
                                                    'escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle' => 'tooltip', 'title' => __('delete'),
                                                    'confirm' => __('delete_message',  $cidcHoliday->id)
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
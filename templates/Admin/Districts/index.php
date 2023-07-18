<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\District[]|\Cake\Collection\CollectionInterface $districts
 */
?>
<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/district_filter', array(
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
                    <h3 class="box-title"> <?php echo __d('setting', 'districts'); ?> </h3>

                    <?php if (isset($permissions['Districts']['add']) && ($permissions['Districts']['add'] == true)) { ?>

                        <div class="box-tools ml-auto p-1">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                                'escape' => false,
                                'class' => 'btn btn-primary button'
                            ]) ?>
                        </div>
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'District'); ?>" class="table table-hover table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center"><?= $this->Paginator->sort('Districts.id', __('id')) ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('Districts.id', __('name')) ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('Districts.enabled', __('enabled')) ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('Districts.created', __('created')) ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('Districts.modified', __('modified')) ?></th>
                                <th class="text-center"><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($districts as $district) : ?>
                                <tr>
                                    <td class="text-center"><?= $this->Number->format($district->id) ?></td>
                                    <td>
                                        <?=
                                        h($district->DistrictLanguages['name'])
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $this->element('view_check_ico', array('_check' => $district->enabled)) ?>
                                    </td>
                                    <td class="text-center"><?= $this->element('customize_format_datetime', ['date' => $district->created]) ?></td>
                                    <td class="text-center"><?= $this->element('customize_format_datetime', ['date' => $district->modified]) ?></td>
                                    <td class="text-center">

                                        <?php
                                        if (isset($permissions['Districts']['view']) && ($permissions['Districts']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $district->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        if (isset($permissions['Districts']['edit']) && ($permissions['Districts']['edit'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $district->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
                                        }

                                        if (isset($permissions['Districts']['delete']) && ($permissions['Districts']['delete'] == true)) {

                                            echo $this->Form->postLink(
                                                '<i class="far fa-trash-alt"></i>',
                                                array('action' => 'delete',  $district->id),
                                                array(
                                                    'escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle' => 'tooltip', 'title' => __('delete'),
                                                    'confirm' => __('delete_message',  $district->id)
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
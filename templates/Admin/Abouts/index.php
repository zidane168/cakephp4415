<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface[]|\Cake\Collection\CollectionInterface $abouts
 */
?>

<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/about_filter', array(
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
                    <h3 class="box-title"> <?php echo __d('setting', 'about'); ?> </h3>
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'About'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('Abouts.id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('Abouts.enabled', __('enabled')) ?></th>
                                <th><?= $this->Paginator->sort('Abouts.created', __('created')) ?></th>
                                <th><?= $this->Paginator->sort('Abouts.modified', __('modified')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($abouts as $about) : ?>
                                <tr>
                                    <td><?= $this->Number->format($about->id) ?></td>
                                    <td><?= $this->element('view_check_ico', array('_check' => $about->enabled)) ?></td>
                                    <td><?= h($about->created) ?></td>
                                    <td><?= h($about->modified) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['Abouts']['view']) && ($permissions['Abouts']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $about->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        if (isset($permissions['Abouts']['edit']) && ($permissions['Abouts']['edit'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $about->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
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
<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface[]|\Cake\Collection\CollectionInterface $terms
 */
?>

<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/term_filter', array(
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
                    <h3 class="box-title"> <?php echo __d('setting', 'term'); ?> </h3>

                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'Term'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('Terms.id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('Terms.id', __d('center', 'title')) ?></th>
                                <th><?= $this->Paginator->sort('Terms.enabled', __('enabled')) ?></th>
                                <th><?= $this->Paginator->sort('Terms.created', __('created')) ?></th>
                                <th><?= $this->Paginator->sort('Terms.modified', __('modified')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($terms as $term) : ?>
                                <tr>
                                    <td><?= $this->Number->format($term->id) ?></td>
                                    <td><?= h($term['TermLanguages']['title']) ?></td>
                                    <td><?= $this->element('view_check_ico', array('_check' => $term->enabled)) ?></td>
                                    <td><?= h($term->created) ?></td>
                                    <td><?= h($term->modified) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['Terms']['view']) && ($permissions['Terms']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $term->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        if (isset($permissions['Terms']['edit']) && ($permissions['Terms']['edit'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $term->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
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
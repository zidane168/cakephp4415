<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface[]|\Cake\Collection\CollectionInterface $albums
 */
?>
<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/album_filter', array(
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
                    <h3 class="box-title"> <?php echo __d('staff', 'album'); ?> </h3>

                    <?php if (isset($permissions['Albums']['add']) && ($permissions['Albums']['add'] == true)) { ?>

                        <div class="box-tools ml-auto p-1">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                                'escape' => false,
                                'class' => 'btn btn-primary button'
                            ]) ?>
                        </div>
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'Album'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('Albums.id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('Albums.cidc_class_id', __d('center', 'cidc_class')) ?></th>
                                <th class="text-center"><?php echo  __('thumbnail'); ?> </th>
                                <th><?= $this->Paginator->sort('Albums.file_name', __('file_name')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($albums as $album) : ?>
                                <tr>
                                    <td><?= $this->Number->format($album->id) ?></td>
                                    <td><?= $album->has('cidc_class_id') ? $this->Html->link($album->cidc_class->name, ['controller' => 'CidcClasses', 'action' => 'view', $album->cidc_class_id]) : '' ?></td>
                                    <td class="text-center">
                                        <?php
                                        if (isset($album['path']) && !empty($album['path'])) {
                                            echo $this->Html->image('../' . $album['path'], array(
                                                'class' => ' img-thumbnail preview'
                                            ));
                                        }
                                        ?>
                                    </td>
                                    <td><?= h($album->file_name) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['Albums']['view']) && ($permissions['Albums']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $album->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        } 
                                        if (isset($permissions['Albums']['delete']) && ($permissions['Albums']['delete'] == true)) {

                                            echo $this->Form->postLink(
                                                '<i class="far fa-trash-alt"></i>',
                                                array('action' => 'delete',  $album->id),
                                                array(
                                                    'escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle' => 'tooltip', 'title' => __('delete'),
                                                    'confirm' => __('delete_message',  $album->id)
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
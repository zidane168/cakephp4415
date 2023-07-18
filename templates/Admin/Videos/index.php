<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Video[]|\Cake\Collection\CollectionInterface $videos
 */
?>
<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/video_filter', array(
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
                    <h3 class="box-title"> <?php echo __d('staff', 'videos'); ?> </h3>

                    <?php if (isset($permissions['Videos']['add']) && ($permissions['Videos']['add'] == true)) { ?>

                        <div class="box-tools ml-auto p-1">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                                'escape' => false,
                                'class' => 'btn btn-primary button'
                            ]) ?>
                        </div>
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'Video'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('Videos.id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('Videos.cidc_class_id', __d('center', 'cidc_class')) ?></th>
                                <!-- <th><?= h(__d('staff', 'video')) ?></th> -->
                                <th><?= $this->Paginator->sort('Videos.ext', __('ext')) ?></th>
                                <th><?= $this->Paginator->sort('Videos.file_name', __('file_name')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($videos as $video) : ?>
                                <tr>
                                    <td><?= $this->Number->format($video->id) ?></td>
                                    <td><?= $video->has('cidc_class_id') ? $this->Html->link($cidcClasses[$video->cidc_class_id], ['controller' => 'CidcClasses', 'action' => 'view', $video->cidc_class_id]) : '' ?></td>
                                    <!-- <td>
                                        <iframe src=<?= $url . $video->path ?> width="100" height="100"></iframe>
                                    </td> -->
                                    <td><?= h($video->ext) ?></td>
                                    <td><?= h($video->file_name) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['Videos']['view']) && ($permissions['Videos']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $video->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        // if (isset($permissions['Videos']['edit']) && ($permissions['Videos']['edit'] == true)) { 
                                        //     echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $video->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('edit')));
                                        // }

                                        if (isset($permissions['Videos']['delete']) && ($permissions['Videos']['delete'] == true)) {

                                            echo $this->Form->postLink(
                                                '<i class="far fa-trash-alt"></i>',
                                                array('action' => 'delete',  $video->id),
                                                array(
                                                    'escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle' => 'tooltip', 'title' => __('delete'),
                                                    'confirm' => __('delete_message',  $video->id)
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
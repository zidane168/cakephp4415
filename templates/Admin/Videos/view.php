<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Video $video
 */
?>

<div class="container-fluid card full-border">


    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __('Videos'); ?> </h3>

                    <?php
                    if (isset($permissions['Videos']['edit']) && ($permissions['Videos']['edit'] == true)) {
                    ?>

                        <div class="box-tools ml-auto p-1">
                            <?php echo $this->Html->link('<i class="fa fa-pencil"></i> ' . __('edit'), array('action' => 'edit', $video->id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                        </div>

                    <?php
                    }
                    ?>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th><?= __('Id') ?></th>
                            <td><?= $this->Number->format($video->id) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'cidc_class') ?></th>
                            <td><?= $video->has('cidc_class') ? $this->Html->link($video->cidc_class->name, ['controller' => 'CidcClasses', 'action' => 'view', $video->cidc_class->id]) : '' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('ext') ?></th>
                            <td><?= h($video->ext) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('file_name') ?></th>
                            <td><?= h($video->file_name) ?></td>
                        </tr>

                        <tr>
                            <th><?= __('size') ?></th>
                            <td><?= $video->size ?></td>
                        </tr>
                        <tr>
                            <th>
                                <?= __d('staff', 'video') ?>
                            </th>
                            <td><iframe src=<?= $url . $video->path ?> width="200" height="200"></iframe></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
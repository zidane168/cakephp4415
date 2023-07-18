<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $album
 */
?>

<div class="container-fluid card full-border">


    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __('Albums'); ?> </h3>

                    <?php
                    if (isset($permissions['Albums']['edit']) && ($permissions['Albums']['edit'] == true)) {
                    ?>

                        <div class="box-tools ml-auto p-1">
                            <?php echo $this->Html->link('<i class="fa fa-pencil"></i> ' . __('edit'), array('action' => 'edit', $album->id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                        </div>

                    <?php
                    }
                    ?>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped">

                        <tr>
                            <th><?= __('Id') ?></th>
                            <td><?= $this->Number->format($album->id) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('file_name') ?></th>
                            <td><?= h($album->file_name) ?></td>
                        </tr>

                        <tr>
                            <th><?= __d('center', 'cidc_class') ?></th>
                            <td><?= $this->Number->format($album->cidc_class_id) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('width') ?></th>
                            <td><?= $this->Number->format($album->width) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('height') ?></th>
                            <td><?= $this->Number->format($album->height) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('size') ?></th>
                            <td><?= $this->Number->format($album->size) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('staff', 'album') ?></th>
                            <td><?php
                                if (isset($album['path']) && !empty($album['path'])) {
                                    echo $this->Html->image('../' . $album['path'], array(
                                        'class' => ' img-thumbnail preview'
                                    ));
                                }
                                ?></td>
                        </tr>
                    </table>

                </div>
            </div>
        </div>
    </div>

</div>
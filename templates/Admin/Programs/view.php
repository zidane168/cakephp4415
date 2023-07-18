<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $program
 */
?>

<div class="container-fluid card full-border">


    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __d('center', 'program'); ?> </h3>

                    <?php
                    if (isset($permissions['Programs']['edit']) && ($permissions['Programs']['edit'] == true)) {
                    ?>

                        <div class="box-tools ml-auto p-1">
                            <?php echo $this->Html->link('<i class="fa fa-pencil"></i> ' . __('edit'), array('action' => 'edit', $program->id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                        </div>

                    <?php
                    }
                    ?>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th><?= __('id') ?></th>
                            <td><?= $this->Number->format($program->id) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'title_color') ?></th>
                            <td> <input disabled type="color" value="<?= $program['title_color'] ?>"> </td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'background_color') ?></th>
                            <td> <input disabled type="color" value="<?= $program['background_color'] ?>"> </td>
                        </tr>

                        <?php
                        echo $this->element('admin/filter/common/enabled_info', array(
                            'object' => $program,
                        ));
                        echo $this->element('admin/filter/common/admin_info', array(
                            'object' => $program,
                        ));
                        ?>
                    </table>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin-top-15">
                                <?= $this->element('content_view', array(
                                    'languages'             => $languages,
                                    'language_input_fields' => $language_input_fields,
                                    'images' 				=> $images,
                                )); ?>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

</div>
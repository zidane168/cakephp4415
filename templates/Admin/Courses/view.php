<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $course
 */
?>

<div class="container-fluid card full-border">


    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __d('center', 'course'); ?> </h3>

                    <?php
                    if (isset($permissions['Courses']['edit']) && ($permissions['Courses']['edit'] == true)) {
                    ?>

                        <div class="box-tools ml-auto p-1">
                            <?php echo $this->Html->link('<i class="fa fa-pencil"></i> ' . __('edit'), array('action' => 'edit', $course->id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                        </div>

                    <?php
                    }
                    ?>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th><?= __('id') ?></th>
                            <td><?= $this->Number->format($course->id) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'program') ?></th>
                            <td><?= $course->has('program') ? $this->Html->link($course->program->program_languages[0]->name, ['controller' => 'Programs', 'action' => 'view', $course->program_id]) : '' ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'age_range_from') ?></th>
                            <td><?= $this->Number->format($course->age_range_from) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'age_range_to') ?></th>
                            <td><?= $this->Number->format($course->age_range_to) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'unit') ?></th>
                            <td><?= h($units[$course->unit]) ?></td>
                        </tr>

                        <?php
                        echo $this->element('admin/filter/common/enabled_info', array(
                            'object' => $course,
                        ));
                        echo $this->element('admin/filter/common/admin_info', array(
                            'object' => $course,
                        ));
                        ?>
                    </table>


                </div>
            </div>
        </div>
    </div>

</div>
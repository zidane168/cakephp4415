<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface[]|\Cake\Collection\CollectionInterface $courses
 */
?>
<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/course_filter', array(
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
                    <h3 class="box-title"> <?php echo __d('center', 'course'); ?> </h3>

                    <?php if (isset($permissions['Courses']['add']) && ($permissions['Courses']['add'] == true)) { ?>

                        <div class="box-tools ml-auto p-1">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                                'escape' => false,
                                'class' => 'btn btn-primary button'
                            ]) ?>
                        </div>
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'Course'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('Courses.id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('Courses.id', __('name')) ?></th>
                                <th><?= $this->Paginator->sort('Courses.program_id', __d('center', 'program')) ?></th>
                                <th><?= $this->Paginator->sort('Courses.sort', __d('center', 'sort')) ?></th>
                                <th><?= $this->Paginator->sort('Courses.age_range_from', __d('center', 'age_range_from')) ?></th>
                                <th><?= $this->Paginator->sort('Courses.age_range_to', __d('center', 'age_range_to')) ?></th>
                                <th><?= $this->Paginator->sort('Courses.unit', __d('center', 'unit')) ?></th>
                                <th><?= $this->Paginator->sort('Courses.enabled', __('enabled')) ?></th>
                                <th><?= $this->Paginator->sort('Courses.created', __('created')) ?></th>
                                <th><?= $this->Paginator->sort('Courses.modified', __('modified')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">

                            <?php
                            $arr = $courses->toArray();
                            $flag_controller = "";
                            foreach ($courses as $course) : ?>
                                <tr>
                                    <td><?= $this->Number->format($course->id) ?></td>
                                    <td><?= h($course['CourseLanguages']['name']) ?></td>
                                    <?php
                                    $first_time_items = array();
                                    $num_row_span = 1;
                                    if (strtolower($course->program->program_languages[0]->name) != strtolower($flag_controller)) {
                                        $flag_controller = $course->program->program_languages[0]->name;

                                        $first_time_items = array_filter($arr, function ($item) use ($flag_controller) {
                                            return strtolower($item->program->program_languages[0]->name) == strtolower($flag_controller);
                                        });
                                        $num_row_span = count($first_time_items);

                                    ?>

                                        <td class="text-center" style="vertical-align: middle" rowspan="<?= $num_row_span; ?>">
                                            <?= h($course->program->program_languages[0]->name); ?>
                                        </td>

                                    <?php } ?>
                                    <td><?= $this->Number->format($course->sort) ?></td>
                                    <td><?= $this->Number->format($course->age_range_from) ?></td>
                                    <td><?= $this->Number->format($course->age_range_to) ?></td>
                                    <td><?= h($units[$course->unit]) ?></td>
                                    <td class="text-center">
                                        <?= $this->element('view_check_ico', array('_check' => $course->enabled)) ?>
                                    </td>
                                    <td><?= h($course->created) ?></td>
                                    <td><?= h($course->modified) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['Courses']['view']) && ($permissions['Courses']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $course->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        if (isset($permissions['Courses']['edit']) && ($permissions['Courses']['edit'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $course->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
                                        }

                                        if (isset($permissions['Courses']['delete']) && ($permissions['Courses']['delete'] == true)) {

                                            echo $this->Form->postLink(
                                                '<i class="far fa-trash-alt"></i>',
                                                array('action' => 'delete',  $course->id),
                                                array(
                                                    'escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle' => 'tooltip', 'title' => __('delete'),
                                                    'confirm' => __('delete_message',  $course->id)
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
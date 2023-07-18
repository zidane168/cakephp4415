<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $cidcClass
 */

use Cake\I18n\Number;

?>

<div class="container-fluid card full-border">


    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __d('center', 'cidc_class'); ?> </h3>

                    <?php
                    if (isset($permissions['Cidc Classes']['edit']) && ($permissions['Cidc Classes']['edit'] == true)) {
                    ?>

                        <div class="box-tools ml-auto p-1">
                            <?php echo $this->Html->link('<i class="fa fa-pencil"></i> ' . __('edit'), array('action' => 'edit', $cidcClass->id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                        </div>

                    <?php
                    }
                    ?>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th><?= __('id') ?></th>
                            <td><?= $this->Number->format($cidcClass->id) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('name') ?></th>
                            <td><?= h($cidcClass->name) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('code') ?></th>
                            <td><?= h($cidcClass->code) ?></td>
                        </tr>

                        <tr>
                            <th><?= __d('center', 'program') ?></th>
                            <td><?= $this->Html->link(
                                    h($cidcClass->program->program_languages[0]['name']),
                                    ['controller' => 'Programs', 'action' => 'view', $cidcClass->program_id]
                                )  ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'course') ?></th>
                            <td><?= $this->Html->link(
                                    h($cidcClass->course->course_languages[0]['name']),
                                    ['controller' => 'Courses', 'action' => 'view', $cidcClass->course_id]
                                )  ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'center') ?></th>
                            <td><?= $this->Html->link(
                                    h($cidcClass->center->center_languages[0]['name']),
                                    ['controller' => 'Centers', 'action' => 'view', $cidcClass->center_id]
                                )  ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?= __('status') ?></th>
                            <td>
                                <?php
                                echo ($status[$cidcClass->status])
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'fee') ?></th>
                            <td><?= h(number_format($cidcClass->fee, 2)) ?></td>
                        </tr>

                        <tr>
                            <th><?= __d('center', 'date_of_lesson') ?></th>
                            <td><?= h($date_for_lessons) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'class_type') ?></th>
                            <td><?= $this->Html->link(
                                    h($cidcClass->class_type->class_type_languages[0]['name']),
                                    ['controller' => 'ClassTypes', 'action' => 'view', $cidcClass->class_type_id]
                                )  ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?= __d('cidcclass', 'target_audience') ?></th>
                            <td><?php
                                $unit = $target_units[$cidcClass->target_unit];
                                echo ("$cidcClass->target_audience_from - $cidcClass->target_audience_to $unit") ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'target_unit') ?></th>
                            <td><?= h($target_units[$cidcClass->target_unit]) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'minimum_of_students') ?></th>
                            <td><?= $this->Number->format($cidcClass->minimum_of_students) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'maximum_of_students') ?></th>
                            <td><?= $this->Number->format($cidcClass->maximum_of_students) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('cidcclass', 'number_of_lessons') ?></th>
                            <td><?= $this->Number->format($cidcClass->number_of_lessons) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'start_date') ?></th>
                            <td><?= h($cidcClass->start_date->format('Y-m-d')) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'end_date') ?></th>
                            <td><?= h($cidcClass->end_date->format('Y-m-d')) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'start_time') ?></th>
                            <td><?= h($cidcClass->start_time->format('H:i')) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'end_time') ?></th>
                            <td><?= h($cidcClass->end_time->format('H:i')) ?></td>
                        </tr>

                        <?php
                        echo $this->element('admin/filter/common/enabled_info', array(
                            'object' => $cidcClass,
                        ));
                        echo $this->element('admin/filter/common/admin_info', array(
                            'object' => $cidcClass,
                        ));
                        ?>
                    </table>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin-top-15">
                                <?= $this->element('content_view', array(
                                    'languages'             => $languages,
                                    'language_input_fields' => $language_input_fields,
                                    //  'images' 				=> $images,
                                )); ?>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

</div>
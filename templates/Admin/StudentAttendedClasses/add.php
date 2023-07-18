<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\StudentAttendedClass $studentAttendedClass
 */
?>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <?= __('Student Attended Classes'); ?> </h3>
                </div>

                <div class="card-body">
                    <?= $this->Form->create($studentAttendedClass) ?>
                    <fieldset>
                        <div class="row">
                            <div class="col-4">
                                <?php
                                echo $this->Form->control('cidc_class_id', [
                                    'empty' => __('please_select'),
                                    'required' => true,
                                    'escape' => false,
                                    'id' => 'cidc_class_id',
                                    'data-live-search' => true,
                                    'class' => 'selectpicker form-control',
                                    'options' => $cidcClasses,
                                    'label' => "<font class='red'> * </font>" . __d('center', 'cidc_class'),
                                ]); ?>
                            </div>
                            <div class="col-8">
                                <label>
                                    <?php echo (__('information')) ?>
                                </label>
                                <div class="box-body">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <th style="width: 35%"><?= __('id') ?></th>
                                            <td id="cidc_class_id_view"></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('name') ?></th>
                                            <td id="cidc_class_name_view"></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('code') ?></th>
                                            <td id="cidc_class_code_view"></td>
                                        </tr>

                                        <tr>
                                            <th><?= __('date') ?></th>
                                            <td id="date_view"></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('time') ?></th>
                                            <td id="time_view"></td>
                                        </tr>
                                        <tr>
                                            <th><?= __d('center', 'program') ?></th>
                                            <td id="program_view"></td>
                                        </tr>
                                        <tr>
                                            <th><?= __d('center', 'course') ?></th>
                                            <td id="course_view"></td>
                                        </tr>
                                        <tr>
                                            <th><?= __d('center', 'center') ?></th>
                                            <td id="center_view"></td>
                                        </tr>
                                        <tr>
                                            <th><?= __d('center', 'class_type') ?></th>
                                            <td id="class_type_view"></td>
                                        </tr>
                                        <tr>
                                            <th><?= __d('center', 'target_audience') ?></th>
                                            <td id="target_audience_view"></td>
                                        </tr>
                                        <tr>
                                            <th><?= __d('cidcclass', 'capacity') ?></th>
                                            <td id="min_max_students_view"></td>
                                        </tr>
                                        <tr>
                                            <th><?= __d('cidcclass', 'student_register_classes') ?></th>
                                            <td id="number_of_register_view"></td>
                                        </tr>
                                        <tr>
                                            <th><?= __d('cidcclass', 'number_of_lessons') ?></th>
                                            <td id="number_of_lessons_view"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php

                        echo $this->Form->control('kid_id', [
                            'empty' => __('please_select'),
                            'required' => true,
                            'escape' => false,
                            'id' => 'kid_id',
                            'data-live-search' => true,
                            'class' => 'selectpicker form-control',
                            'options' => $kids,
                            'label' => "<font class='red'> * </font>" . __d('parent', 'kid'),
                        ]);
                        // echo $this->element('datetime_picker', array(
                        //     'id'                => 'date',
                        //     'field_name'        => 'date',
                        //     'placeholder'       => __d('parent', 'date'),
                        //     'label'             => __d('parent', 'date'),
                        //     'class'             => 'date',
                        //     'format'            => 'YYYY-MM-DD',
                        // ));

                        echo $this->Form->control('date_id', [
                            'escape' => false,
                            'id' => 'date_id',
                            'label'    => __d('parent', 'date'),
                            'class' => 'form-control',
                            'empty' => __('please_select')
                        ]);

                        ?>

                        <div class="row mt-10">
                            <div class="col-2">
                                <?= $this->Form->button(__('submit'), ['class' => 'btn btn-large btn-primary form-control']) ?>
                            </div>
                        </div>
                    </fieldset>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo $this->Html->script('CakeAdminLTE/pages/admin_class_date', array('inline' => false));
?>
<script type="text/javascript">
    $(document).ready(function() {
        ADMIN_CLASS_DATE.url_get_date_by_class_id = '<?= $this->Url->build(['prefix' => 'Api/V1',  'controller' => 'CidcClasses', 'action' => 'dataselectdate']);   ?>';
        ADMIN_CLASS_DATE.current_language = '<?= $current_language; ?>';
        ADMIN_CLASS_DATE.init_page();
    });
</script>
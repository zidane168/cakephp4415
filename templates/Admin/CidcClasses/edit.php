<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $cidcClass
 */
    echo $this->Html->css('schedule.css?v=' . date('U'));
?>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <?= __d('center', 'edit_cidc_class'); ?> </h3>
                </div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($cidcClass, ['id' => 'cidcClass']) ?>
                    <fieldset>

                        <?php
                        echo $this->Form->control('name', [
                            'required' => true,
                            'label' => '<font class="red"> * </font>' . __('name'),
                            'class' => 'form-control',
                            'escape' => false, 
                        ]);
                        ?>

                        <div class="row">
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('program_id', [
                                    'empty' => __('please_select'),
                                    'required' => true,
                                    'escape' => false,
                                    'id' => 'program_id',
                                    'data-live-search' => true,
                                    'class' => 'selectpicker form-control',
                                    'options' => $programs,
                                    'label' => "<font class='red'> * </font>" . __d('center', 'program'),
                                ]);
                                ?>
                            </div>
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('course_id', [
                                    'empty' => __('please_select'),
                                    'required' => true,
                                    'escape' => false,
                                    'id' => 'course_id',
                                    'data-live-search' => true,
                                    'class' => 'selectpicker form-control',
                                    'options' => $courses,
                                    'label' => "<font class='red'> * </font>" . __d('center', 'course'),
                                ]);
                                ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-4">
                                <?php
                                echo $this->Form->control('target_audience_from', [
                                    'class' => 'form-control',
                                    'required' => true,
                                    'escape' => false,
                                    'id'    => 'target_audience_from',
                                    'label' => "<font class='red'> * </font>" . __d('center', 'target_audience_from'),
                                    'min'   => 1
                                ]);
                                ?>
                            </div>
                            <div class="col-4">
                                <?php
                                echo $this->Form->control('target_audience_to', [
                                    'class' => 'form-control', 
                                    'required' => true,
                                    'escape' => false,
                                    'id'    => 'target_audience_to',
                                    'label' => "<font class='red'> * </font>" . __d('center', 'target_audience_to'),
                                    'min'   => 1]); 
                                ?>
                            </div>
                            <div class="col-4">
                                <?php
                                echo $this->Form->control('target_unit', [
                                    'required' => true,
                                    'escape' => false,
                                    'data-live-search' => true,
                                    'class' => 'selectpicker form-control',
                                    'options' => $target_units,
                                    'label' => "<font class='red'> * </font>" . __d('center', 'target_unit'),
                                ]); ?>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('minimum_of_students', [
                                    'class' => 'form-control',
                                    'required' => true,
                                    'id'    => 'minimum_of_students',
                                    'escape' => false,
                                    'label' => "<font class='red'> * </font>" . __d('center', 'minimum_of_students'),
                                    'min'   => 1,
                                    'max'   => 50
                                ]); 
                                ?>
                            </div>
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('maximum_of_students', [
                                    'class' => 'form-control',
                                    'required' => true,
                                    'escape' => false,
                                    'id'    => 'maximum_of_students',
                                    'label' => "<font class='red'> * </font>" . __d('center', 'maximum_of_students'),
                                    'min'   => 1,
                                    'max'   => 50
                                ]); 
                                ?>
                            </div>
                        </div>

                        <?php
                        echo $this->Form->control('status', [
                            'empty' => __('please_select'),
                            'required' => false,
                            'escape' => false,
                            'class' => 'selectpicker form-control',
                            'options' => $status,
                            'label' => __('status')
                        ]);
                        echo $this->Form->control('fee', [
                            'escape' => false,
                            'required' => true,
                            'min'       => 1,
                            'label'    => "<font class='red'> * </font>" . __d('center', 'fee'),
                            'class' => 'form-control'
                        ]);
                        echo $this->Form->control('class_type_id', [
                            'empty' => __('please_select'),
                            'required' => true,
                            'escape' => false,
                            'data-live-search' => true,
                            'class' => 'selectpicker form-control',
                            'options' => $classTypes,
                            'label' => "<font class='red'> * </font>" . __d('center', 'class_type'),
                        ]); 
                        ?>
                       
                        <div class="row">
                            <div class="col-6">
                                <?php 
                                echo $this->element('datetime_picker', array(
                                    'id'                => 'start_date',
                                    'field_name'        => 'start_date',
                                    'placeholder'       => __d('center', 'start_date'),
                                    'label'             => __d('center', 'start_date'),
                                    'class'             => 'date',
                                    'required'          => 'required',
                                    'format'            => 'YYYY-MM-DD',
                                    'value'             => $cidcClass->start_date->format('Y-m-d'),
                                ));
                                echo $this->element('datetime_picker', array(
                                    'id'                => 'start_time',
                                    'field_name'        => 'start_time',
                                    'placeholder'       => __d('center', 'start_time'),
                                    'label'             => __d('center', 'start_time'),
                                    'class'             => 'time',
                                    'required'          => 'required',
                                    'format'            => 'HH:mm',
                                    'value'             => $cidcClass->start_time->format('H:i'), 
                                ));
                                ?>
                            </div>
                            <div class="col-6">
                                <?php
                             
                                echo $this->element('datetime_picker', array(
                                    'id'                => 'end_date',
                                    'field_name'        => 'end_date',
                                    'placeholder'       => __d('center', 'end_date'),
                                    'label'             => __d('center', 'end_date'),
                                    'class'             => 'date',
                                    'required'          => 'required',
                                    'format'            => 'YYYY-MM-DD',
                                    'value'             => $cidcClass->end_date->format('Y-m-d'),
                                ));
                                echo $this->element('datetime_picker', array(
                                    'id'                => 'end_time',
                                    'field_name'        => 'end_time',
                                    'placeholder'       => __d('center', 'end_time'),
                                    'label'             => __d('center', 'end_time'),
                                    'class'             => 'time',
                                    'required'          => 'required',
                                    'format'            => 'HH:mm',
                                    'value'             => $cidcClass->end_time->format('H:i'),

                                ));

                                ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6" style="margin-bottom: 20px">

                                <?php echo $this->Form->control('number_of_lessons', [
                                    'class' => 'form-control',
                                    'required' => 'required',  
                                    'escape' => false, 
                                    'label' => "<font class='red'> * </font>" . __d('cidcclass', 'number_of_lessons'),
                                    'min'   => 1,
                                    'max'   => 1000,
                                ]);
                                ?>  

                                <label> <?php echo __d('center', 'date_of_lesson'); ?> </label>
                                <?php
                                foreach ($weekends as $key => $value) { ?>
                                    <div style="margin-left: 20px;">
                                        <input class=<?= 'weekend_' . $key ?> type="checkbox" name="date_of_lessons[]" value=<?php echo $key ?>> <?php echo $value; ?>
                                    </div>
                                <?php }
                                ?>

                                <div class="schedule-color"> 
                                    <div class="hk_public_holiday"> </div>
                                    <div class="ml-10"> <?= __d('cidcclass', 'hongkong_public_holiday') ?> </div>
                                </div>

                                <div class="schedule-color"> 
                                    <div  class="class_registered_day"> </div>
                                    <div class="ml-10"> <?= __d('cidcclass', 'class_registered_day') ?> </div>
                                </div>
                            </div>

                            <div class="col-md-12 col-lg-6 ">
                                <div id="datepicker-inline"></div>
                            </div>
                        </div>

                        <?php
                        echo $this->Form->control('center_id', [
                            'empty' => __('please_select'),
                            'required' => true,
                            'escape' => false,
                            'data-live-search' => true,
                            'class' => 'selectpicker form-control',
                            'options' => $centers,
                            'label' => "<font class='red'> * </font>" . __d('center', 'center'),
                        ]);
                        echo $this->element('language_input_column', array(
                            'languages_model'           => $languages_model,
                            'languages_edit_model'      => $languages_edit_model,
                            'languages_list'            => $languages_list,
                            'language_input_fields'     => $language_input_fields,
                            'languages_edit_data'       => $languages_edit_data,
                        ));
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
    echo $this->Html->script('CakeAdminLTE/pages/admin_cidc_classes.js?v=' . date('U'), array('inline' => false)); 
    echo $this->Html->script('CakeAdminLTE/pages/admin_schedule.js?v=' . date('U'), array('inline' => false));
    echo $this->Html->script('CakeAdminLTE/pages/admin_cidc_classes_validator.js?v=' . date('U'), array('inline' => false));
?>
 
<script> 

    $(document).ready(function() {

        ADMIN_CIDC_CLASSES.init();
        ADMIN_CIDC_CLASSES.url_get_course_by_program = '<?= $this->Url->build(['prefix' => 'Api/V1',  'controller' => 'Courses', 'action' => 'dataselect']);   ?>';

        ADMIN_CIDC_CLASSES.current_language = '<?= $current_language; ?>';

        ADMIN_CIDC_CLASSES.submit(); 
        ADMIN_SCHEDULE.holidays = <?= $holidays ?>;  
        ADMIN_SCHEDULE.init_edit_control();    


        let arr = <?php echo json_encode($cidcClass->date_of_lessons) ?>;
        if(arr.length != 0) {
            arr.forEach((item) => {  
                $(`.weekend_${item.day}`).prop('checked', true).trigger('change')
            })
        } 
        
    })
</script>
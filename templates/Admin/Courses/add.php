<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $course
 */
?>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <?= __d('center', 'add_course'); ?> </h3>
                </div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($course) ?>
                    <fieldset>

                        <div class="row">
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('program_id', [
                                    'empty' => __('please_select'),
                                    'required' => true,
                                    'escape' => false,
                                    'data-live-search' => true,
                                    'class' => 'selectpicker form-control',
                                    'options' => $programs,
                                    'label' => "<font class='red'> * </font>" . __d('center', 'program'),
                                ]);
                                ?>
                            </div>
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('sort', [
                                    'class' => 'form-control', 
                                    'min'   => 1,
                                    'label' => __d('center', 'sort')
                                ]); 
                                ?>
                            </div>
                        </div> 

                        <div class="row">
                            <div class="col-4">
                                <?php 
                                    echo $this->Form->control('age_range_from', [
                                        'class' => 'form-control', 
                                        'min'   => 1,
                                        'label' => __d('center', 'age_range_from')
                                    ]); 
                                ?>
                            </div>

                            <div class="col-4">
                                <?php 
                                    echo $this->Form->control('age_range_to', [
                                        'class' => 'form-control', 
                                        'min'   => 1,
                                        'label' => __d('center', 'age_range_to')
                                    ]);
                                ?>
                            </div>

                            <div class="col-4">
                                <?php 
                                    echo $this->Form->control('unit', [ 
                                        'required' => true,
                                        'escape' => false,
                                        'data-live-search' => true,
                                        'class' => 'selectpicker form-control',
                                        'options' => $units,
                                        'label' => "<font class='red'> * </font>" . __d('center', 'unit'),
                                    ]);
                                ?>
                            </div>
                        </div>

                        <?php
                        echo $this->element('language_input_column', array(
                            'languages_model'       => $languages_model,
                            'languages_list'        => $languages_list,
                            'language_input_fields' => $language_input_fields,
                            'languages_edit_data'   => isset($this->request->data[$languages_model]) ? $this->request->data[$languages_model] : false,
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
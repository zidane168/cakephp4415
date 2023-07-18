<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Staff $staff
 */
?>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <?= __d('staff', 'edit_staff'); ?> </h3>
                </div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($staff) ?>
                    <fieldset>

                        <?php
                        echo $this->Form->control('phone_number', [
                            'class' => 'form-control',
                            'escape' => false,
                            'readonly' => true,
                            'required' => true,
                            'label' => "<font class='red'> * </font>" . __d('parent', 'phone_number')
                        ]);

                        echo $this->Form->control('email', [
                            'class' => 'form-control',
                            'escape' => false, 
                            'label' =>  __('email')
                        ]);
                        echo $this->Form->control('gender', [
                            'empty' => __('please_select'),
                            'required'          => true,
                            'escape'            => false,
                            'class' => 'selectpicker form-control',
                            'label' => "<font class='red'> * </font>" . __d('parent', 'gender'),
                            'options' => $genders
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
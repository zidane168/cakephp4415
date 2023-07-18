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
                    <h3 class="card-title"> <?= __d('staff', 'add_staff'); ?> </h3>
                </div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($staff) ?>
                    <fieldset>

                        <?php

                        echo $this->Form->control('center_id', [
                            'empty' => __('please_select'),
                            'required' => true,
                            'escape' => false,
                            'class' => 'selectpicker form-control',
                            'options' => $centers,
                            'label' => "<font class='red'> * </font>" . __d('center', 'center'),
                        ]);
                        echo $this->Form->control('phone_number', [
                            'class' => 'form-control',
                            'escape' => false,
                            'required' => true,
                            'label' => "<font class='red'> * </font>" . __d('parent', 'phone_number')
                        ]);
                        echo $this->Form->control('email', [
                            'class' => 'form-control',
                            'escape' => false, 
                            'label' => __('email')
                        ]);

                        echo $this->Form->control('password', [
                            'class' => 'form-control',
                            'escape' => false,
                            'autocomplete' => 'new-password',
                            'required' => true,
                            'label' => "<font class='red'> * </font>" . __('password')
                        ]);
                        echo $this->Form->control('gender', [
                            'empty' => __('please_select'),
                            'required' => true,
                            'escape' => false,
                            'class' => 'selectpicker form-control',
                            'options' => $genders,
                            'label' => "<font class='red'> * </font>" . __d('parent', 'gender'),
                        ]);
                        echo $this->element('language_input_column', array(
                            'languages_model'       => $languages_model,
                            'languages_list'        => $languages_list,
                            'language_input_fields' => $language_input_fields,
                            'languages_edit_data'    => false
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
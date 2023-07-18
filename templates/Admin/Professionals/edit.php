<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Professional $professional
 */
?>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <?= __d('professional', 'edit_professional'); ?> </h3>
                </div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($professional, ['type' => 'file']) ?>
                    <fieldset>

                        <?php
                        echo $this->Form->control('gender', [
                            'empty' => __('please_select'),
                            'required'  => true,
                            'escape'  => false,
                            'class'   => 'selectpicker form-control',
                            'options' => $genders,
                            'label'   => "<font class='red'> * </font>" . __d('parent', 'gender'),
                        ]);
                        echo $this->Form->control('type', [
                            'empty' => __('please_select'),
                            'required'  => true,
                            'escape'  => false,
                            'class'   => 'selectpicker form-control',
                            'options' => $types,
                            'label'   => "<font class='red'> * </font>" . __d('staff', 'type'),
                        ]);
                        echo $this->element('language_input_column', array(
                            'languages_model'       => $languages_model,
                            'languages_list'        => $languages_list,
                            'language_input_fields' => $language_input_fields,
                            'languages_edit_data'   => $languages_edit_data
                        ));

                        echo ("Certification");
                        echo $this->element('multi_language_input_column', array(
                            'languages_model'        => $languages_model_cerification,
                            'languages_list'         => $languages_list,
                            'language_input_fields'  => $language_input_fields_certification,
                            'languages_edit_data'    => $professional_certification,
                            'add_language_input_url' => $add_language_input_url,
                            'total_item'            => 5
                        ));
                        echo $this->element('images_upload_no_type', array(
                            'add_new_images_url'    => $add_new_images_url,
                            'images_model'          => $images_model,
                            'base_model'            => $model,
                            'images_edit_data'      => $images_edit_data,
                            'total_image'           => 1,
                        ));
                        ?>
                        <input type="hidden" value="1" name="numberElementCertification" />

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
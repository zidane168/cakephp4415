<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $contact
 */
?>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <?= __d('setting', 'add_contact'); ?> </h3>
                </div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($contact, ['type' => 'file']) ?>
                    <fieldset>

                        <?php
                        echo $this->element('language_input_column', array(
                            'languages_model'       => $languages_model,
                            'languages_list'        => $languages_list,
                            'language_input_fields' => $language_input_fields,
                            'languages_edit_data'   => false,
                        ));
                        echo $this->element('images_upload_no_type', array(
                            'add_new_images_url'    => $add_new_images_url,
                            'images_model'          => $images_model,
                            'base_model'            => $model,
                            'total_image'           => 1,
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
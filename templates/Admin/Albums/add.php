<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $album
 */
?>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <?= __d('staff', 'add_album'); ?> </h3>
                </div>

                <div class="card-body">
                    <?= $this->Form->create($album, ['type' => 'file']) ?>
                    <fieldset>

                        <?php
                        echo $this->Form->control('cidc_class_id', [
                            'empty' => __('please_select'),
                            'id' => 'cidc_class_id',
                            'required' => true,
                            'escape' => false,
                            'data-live-search' => true,
                            'class' => 'selectpicker form-control',
                            'options' => $cidcClasses,
                            'label' => "<font class='red'> * </font>" . __d('center', 'cidc_class'),
                        ]);

                        echo $this->element('images_upload_no_type', array(
                            'add_new_images_url'    => $add_new_images_url,
                            'images_model'          => $images_model,
                            'base_model'            => $model,
                            'total_image'           => 5,
                            'label'                 => __('add'),
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
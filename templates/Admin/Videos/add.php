<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Video $video
 */
?>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <?= __d('staff', 'add_video'); ?> </h3>
                </div>

                <div class="card-body">
                    <?= $this->Form->create($video, ['type' => 'file']) ?>
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

                        echo $this->element('multi_videos_upload_container', array(
                            'images_model' => $images_model,
                            'accept_file' => array(
                                '.mov', '.mp4', '.wmv', '.avi',
                                '.avchd', '.flv', '.f4v', '.swf',
                                '.mkv', '.hmtl5'
                            ),
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
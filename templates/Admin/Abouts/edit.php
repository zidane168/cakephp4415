<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $about
 */ 

	echo $this->Html->script('ckeditor/ckeditor.js?v=' . date('U'));	 
?> 

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <?= __d('setting', 'edit_about'); ?> </h3>
                </div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($about) ?>
                    <fieldset>

                        <?php
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
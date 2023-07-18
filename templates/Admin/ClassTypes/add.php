<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $classType
 */
?>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <?= __d('center', 'add_class_type'); ?> </h3>
                </div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($classType) ?>
                    <fieldset>

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
<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Center $center
 */
?>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <?= __d('center', 'edit_center'); ?> </h3>
                </div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($center, ['type' => 'file']) ?>
                    <fieldset>

                        <div class="row">
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('latitude', [
                                    'escape' => false,
                                    'required' => false,
                                    'type' => 'text',
                                    'id'    => 'latitude',
                                    'label' => __('latitude'),
                                    'class' => 'form-control'
                                ]);
                                ?>
                            </div>
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('longitude', [
                                    'escape' => false,
                                    'required' => false,
                                    'type' => 'text',
                                    'id'    => 'longitude',
                                    'label' => __('longitude'),
                                    'class' => 'form-control'
                                ]);
                                ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-4">
                            <?php
                                echo $this->Form->control('code', [
                                    'escape' => false,
                                    'required' => false,
                                    'label' => "<font class='red'> * </font>" . __('code'),
                                    'class' => 'form-control'
                                ]);
                            ?>
                            </div>
                            <div class="col-4">
                                <?php  
                                echo $this->Form->control('sort', [
                                    'escape' => false,
                                    'required' => false,
                                    'label' => "<font class='red'> * </font>" . __('sort'),
                                    'class' => 'form-control',
                                    'min' => 1
                                ]);
                                ?>
                            </div>
                            <div class="col-4">
                            <?php
                                echo $this->Form->control('district_id', [
                                    'empty' => __('please_select'),
                                    'required' => true,
                                    'escape' => false,
                                    'data-live-search' => true,
                                    'class' => 'selectpicker form-control',
                                    'options' => $districts,
                                    'label' => "<font class='red'> * </font>" . __d('setting', 'district'),
                                ]);
                            ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('phone_us', [
                                    'escape' => false,
                                    'required' => false,
                                    'id'    => 'phone_us',
                                    'label' => __d('center', 'phone_us'),
                                    'class' => 'form-control'
                                ]);
                                ?>
                            </div>
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('fax_us', [
                                    'escape' => false,
                                    'required' => false,
                                    'id'    => 'fax_us',
                                    'label' => __d('center', 'fax_us'),
                                    'class' => 'form-control'
                                ]);
                                ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('visit_us', [
                                    'escape' => false,
                                    'required' => false,
                                    'label' => __d('center', 'visit_us'),
                                    'class' => 'form-control'
                                ]);
                                ?>
                            </div>
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('mail_us', [
                                    'escape' => false,
                                    'required' => false,
                                    'label' => __d('center', 'mail_us'),
                                    'class' => 'form-control'
                                ]);
                                ?>
                            </div>
                        </div> 

                        <?php 

                        echo $this->element('multi_images_upload_container_edit', array(
                            'images_model' => $images_model,
                            'accept_file' => array(
                                '.pdf',
                            ),
                        ));

                        echo $this->element('language_input_column', array(
                            'languages_model'           => $languages_model,
                            'languages_edit_model'      => $languages_edit_model,
                            'languages_list'            => $languages_list,
                            'language_input_fields'     => $language_input_fields,
                            'languages_edit_data'       => $languages_edit_data,
                        ));
                        ?>

                        <div class="center-banking">
                            <?php
                            echo $this->Form->control('account', [
                                'escape' => false,
                                'required' => false,
                                'label' => "<font class='red'> * </font>" . __('account'),
                                'class' => 'form-control'
                            ]);

                            echo $this->Form->control('username', [
                                'escape' => false,
                                'required' => false,
                                'label' => "<font class='red'> * </font>" . __('username'),
                                'class' => 'form-control'
                            ]);
                            echo $this->Form->control('bank_name', [
                                'escape' => false,
                                'required' => false,
                                'label' => "<font class='red'> * </font>" . __('bank_name'),
                                'class' => 'form-control'
                            ]);

                            ?>
                        </div>

                        <div class="row">
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
echo $this->Html->script('common.js?v=' . date('U'));
?>

<script>
    $(document).ready(function() {
        COMMON.check_number_rules();
    })
</script>
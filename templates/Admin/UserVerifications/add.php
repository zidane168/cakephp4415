<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\PatientVerification $userVerification
 */

use Cake\Core\Configure;

?>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <?= __d('user', 'add_user_verification'); ?> </h3>
                </div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($userVerification) ?>
                    <fieldset>

                        <div class="row">
                            <div class="col-6">
                                <?php

                                echo $this->Form->control('verification_type_id', [
                                    'label' => "<font class='red'> * </font>" .  __d('user', 'verification_type'),
                                    'empty' => __('please_select'),
                                    'required' => true,
                                    'escape' => false,
                                    'data-live-search' => true,
                                    'class' => 'selectpicker form-control',
                                    'options' => $verification_types,
                                ]); ?>
                            </div>
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('phone_number', [
                                    'class' => 'form-control',
                                    'label' => __d('parent', 'phone_number'),
                                ]);
                                ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('verification_method_id', [
                                    'class' => 'form-control',
                                    'label' => "<font class='red'> * </font>" . __d('user', 'verification_method'),
                                    'empty' => __('please_select'),
                                    'required' => true, 
                                    'value' => 1, 
                                    'escape' => false,
                                    'data-live-search' => true,
                                    'class' => 'selectpicker form-control',
                                    'options' => $verification_methods,
                                ]);
                                ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('code', [
                                    'id'    => 'code',
                                    'class' => 'form-control red bold',
                                    'required' => 'required',
                                    'escape' => false,
                                    'label' => "<font class='red'> * </font>" . __d('user', 'code'),
                                ]);
                                ?>

                            </div>
                            <div class="col-6">
                                <label style="margin-top: 45px"> </label>
                                <button class="btn btn-warning" type="button" id="btn_generate_number"> <?= __d('user', 'generate_code'); ?> </button>
                            </div>
                        </div>

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

<?php echo $this->Html->script('CakeAdminLTE/pages/admin_verification.js?v=' . date('U')); ?>

<script>
    // generate number
    $(document).ready(function() {

        ADMIN_VERIFICATION.number = '<?= Configure::read('sms.verify_digit_length'); ?>';
        ADMIN_VERIFICATION.generate_number();
    })
</script>
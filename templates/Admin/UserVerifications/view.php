<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\PatientVerification $userVerification
 */
?>

<div class="container-fluid card full-border">

    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __d('user', 'user_verification'); ?> </h3>

                    <?php
                    if (isset($permissions['UserVerifications']['edit']) && ($permissions['UserVerifications']['edit'] == true)) {
                    ?>

                        <div class="p-1 ml-auto box-tools">
                            <?php echo $this->Html->link('<i class="fa fa-pencil"></i> ' . __('edit'), array('action' => 'edit', $userVerification->id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                        </div>

                    <?php
                    }
                    ?>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th><?= __('id') ?></th>
                            <td><?= $this->Number->format($userVerification->id) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('email') ?></th>
                            <td><?= h($userVerification->email) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('parent', 'phone_number') ?></th>
                            <td><?= $this->Com->formatPhoneNumber($userVerification->phone_number) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('user', 'code') ?></th>
                            <td><?= h($userVerification->code) ?></td>
                        </tr>

                        <tr>
                            <th><?= __d('user', 'verification_type') ?></th>
                            <td><?= h($verification_types[$userVerification->verification_type]) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('user', 'verification_method') ?></th>
                            <td><?= h($verification_methods[$userVerification->verification_method]) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('user', 'is_used') ?></th>
                            <td>
                                <?= $this->element('is_used', array('_check'  => $this->Number->format($userVerification->is_used))) ?>
                            </td>
                        </tr>

                        <?php
                        echo $this->element('admin/filter/common/enabled_info', array(
                            'object' => $userVerification,
                        ));
                        echo $this->element('admin/filter/common/admin_info', array(
                            'object' => $userVerification,
                        ));
                        ?>
                    </table>


                </div>
            </div>
        </div>
    </div>
</div>
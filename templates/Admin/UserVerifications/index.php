<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\PatientVerification[]|\Cake\Collection\CollectionInterface $userVerifications
 */
?>
<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/user_verification_filter', array(
                'data_search' => $data_search
            ));
            ?>
        </div>
    </div>
</div>
<div class="container-fluid card full-border">

    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __d('user', 'user_verification'); ?> </h3>

                    <?php if (isset($permissions['UserVerifications']['add']) && ($permissions['UserVerifications']['add'] == true)) { ?>

                        <div class="p-1 ml-auto box-tools">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                                'escape' => false,
                                'class' => 'btn btn-primary button'
                            ]) ?>
                        </div>
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'PatientVerification'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('UserVerifications.id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('UserVerifications.phone',             __('phone')) ?></th>
                                <th><?= $this->Paginator->sort('UserVerifications.verification_method',     __d('user', 'verification_method')) ?></th>
                                <th><?= $this->Paginator->sort('UserVerifications.verification_type',       __d('user', 'verification_type')) ?></th>
                                <th><?= $this->Paginator->sort('UserVerifications.email',             __('email')) ?></th>
                                <th><?= $this->Paginator->sort('UserVerifications.code',              __d('user', 'code')) ?></th>
                                <th><?= $this->Paginator->sort('UserVerifications.is_used',           __d('user', 'is_used')) ?></th>
                                <th><?= $this->Paginator->sort('UserVerifications.enabled',           __('enabled')) ?></th>
                                <th><?= $this->Paginator->sort('UserVerifications.created',           __('created')) ?></th>
                                <th><?= $this->Paginator->sort('UserVerifications.modified',          __('modified')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($userVerifications as $memberVerification) : ?>
                                <tr>
                                    <td><?= $this->Number->format($memberVerification->id) ?></td>

                                    <td><?= $this->Com->formatPhoneNumber($memberVerification->phone_number) ?></td>
                                    <td>
                                        <?= $this->element('admin/filter/common/member_verification_methods', array(
                                            'verification_methods' => $verification_methods,
                                            'memberVerification' => $memberVerification,
                                        ));  ?>
                                    </td>

                                    <td>
                                        <?= $this->element('admin/filter/common/member_verification_types', array(
                                            'verification_types' => $verification_types,
                                            'memberVerification' => $memberVerification,
                                        ));  ?>
                                    </td>

                                    <td><?= h($memberVerification->email) ?></td>
                                    <td class="bold red">
                                        <?= h($memberVerification->code) ?>
                                    </td>
                                    <td>
                                        <?= $this->element('view_check_ico', array('_check'  => $this->Number->format($memberVerification->is_used))) ?>
                                    </td>
                                    <td>
                                        <?= $this->element('view_check_ico', array('_check'  => $this->Number->format($memberVerification->enabled))) ?>

                                    </td>
                                    <td><?= $this->element('customize_format_datetime', ['date' => $memberVerification->created])
                                        ?></td>
                                    <td><?= $this->element('customize_format_datetime', ['date' => $memberVerification->modified])
                                        ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['UserVerifications']['view']) && ($permissions['UserVerifications']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $memberVerification->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        if (isset($permissions['UserVerifications']['edit']) && ($permissions['UserVerifications']['edit'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $memberVerification->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
                                        }

                                        if (isset($permissions['UserVerifications']['delete']) && ($permissions['UserVerifications']['delete'] == true)) {

                                            echo $this->Form->postLink(
                                                '<i class="far fa-trash-alt"></i>',
                                                array('action' => 'delete',  $memberVerification->id),
                                                array(
                                                    'escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle' => 'tooltip', 'title' => __('delete'),
                                                    'confirm' => __('delete_message',  $memberVerification->id)
                                                )
                                            );
                                        }

                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?= $this->element('Paginator'); ?>
            </div><!-- box, box-primary -->
        </div><!-- .col-12 -->
    </div><!-- row -->
</div> <!-- container -->
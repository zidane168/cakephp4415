<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Staff[]|\Cake\Collection\CollectionInterface $staffs
 */
?>
<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/staff_filter', array(
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
                    <h3 class="box-title"> <?php echo __d('staff', 'staffs'); ?> </h3>

                    <?php if (isset($permissions['Staffs']['add']) && ($permissions['Staffs']['add'] == true)) { ?>

                        <div class="p-1 ml-auto box-tools">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                                'escape' => false,
                                'class' => 'btn btn-primary button'
                            ]) ?>
                        </div>
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'Staff'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('Staff.id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('Staff.id', __d('center', 'center')) ?></th>
                                <th><?= $this->Paginator->sort('StaffLanguages.name', __('name')) ?></th>
                                <th><?= $this->Paginator->sort('Users.phone_number', __('phone')) ?></th>
                                <th><?= $this->Paginator->sort('Users.email', __('email')) ?></th>
                                <th><?= $this->Paginator->sort('Staff.gender', __d('parent', 'gender')) ?></th>
                                <th><?= $this->Paginator->sort('Users.enabled', __('enabled')) ?></th>
                                <th><?= $this->Paginator->sort('Staff.created', __('created')) ?></th>
                                <th><?= $this->Paginator->sort('Staff.modified', __('modified')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($staffs as $staff) : ?>
                                <tr>
                                    <td><?= $this->Number->format($staff->id) ?></td>
                                    <td><?= $staff->has('center') ? $this->Html->link($staff->center->center_languages[0]->name, ['controller' => 'Centers', 'action' => 'view', $staff->center_id]) : '' ?></td>
                                    <td><?= h($staff['StaffLanguages']['name']) ?></td>
                                    <td><?= $this->Com->formatPhoneNumber($staff['Users']['phone_number']) ?></td>
                                    <td><?= h($staff['Users']['email']) ?></td>
                                    <td><?= h($genders[$staff->gender]) ?></td>
                                    <td><?= $this->element('view_check_ico', array('_check' => $staff['Users']['enabled'])) ?></td>
                                    <td><?= h($staff->created) ?></td>
                                    <td><?= h($staff->modified) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['Staffs']['view']) && ($permissions['Staffs']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $staff->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        if (isset($permissions['Staffs']['edit']) && ($permissions['Staffs']['edit'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $staff->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
                                        }

                                        if (isset($permissions['Staffs']['delete']) && ($permissions['Staffs']['delete'] == true)) {
 
                                            $icon = '<i class="fa-solid fa-trash-arrow-up"></i>';
                                            $color = 'btn-success';
                                            $message = __('enabled_message',  $staff->id);
                                            $tooltip = __('enabled');

                                            if ($staff['Users']['enabled'] == true) {
                                                $icon = '<i class="far fa-trash-alt"></i>';
                                                $color = 'btn-danger';
                                                $message = __('disabled_message',  $staff->id);
                                                $tooltip = __('disabled');
                                            }  

                                            echo $this->Form->postLink(
                                                $icon,
                                                array('action' => 'enabled_disabled_feature',  $staff->id),
                                                array(
                                                    'escape' => false, 'class' => 'btn ' . $color . ' btn-sm m-r-btn', 'data-toggle' => 'tooltip', 'title' => $tooltip,
                                                    'confirm' => $message,
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
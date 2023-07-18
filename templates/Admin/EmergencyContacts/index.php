<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface[]|\Cake\Collection\CollectionInterface $emergencyContacts
 */
?>

<div class="container-fluid card full-border">

    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __d('setting', 'emergency_contact'); ?> </h3>

                    <?php if (isset($permissions['EmergencyContacts']['add']) && ($permissions['EmergencyContacts']['add'] == true)) { ?>

                        <div class="box-tools ml-auto p-1">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                                'escape' => false,
                                'class' => 'btn btn-primary button'
                            ]) ?>
                        </div>
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php __d('setting', 'emergency_contact') ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('EmergencyContacts.id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('EmergencyContacts.phone_number', __d('parent', 'phone_number')) ?></th>
                                <th><?= $this->Paginator->sort('EmergencyContacts.id', __('name')) ?></th>
                                <th><?= $this->Paginator->sort('EmergencyContacts.enabled', __('enabled')) ?></th>
                                <th><?= $this->Paginator->sort('EmergencyContacts.created', __('created')) ?></th>
                                <th><?= $this->Paginator->sort('EmergencyContacts.modified', __('modified')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($emergencyContacts as $emergencyContact) : ?>
                                <tr>
                                    <td><?= $this->Number->format($emergencyContact->id) ?></td>
                                    <td><?= h($emergencyContact->phone_number) ?></td>
                                    <td class="text-center"><?= h($emergencyContact->EmergencyContactLanguages['name']) ?></td>
                                    <td><?= $this->element('view_check_ico', array('_check' => $emergencyContact->enabled)) ?></td>

                                    <td><?= h($emergencyContact->created) ?></td>
                                    <td><?= h($emergencyContact->modified) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['EmergencyContacts']['view']) && ($permissions['EmergencyContacts']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $emergencyContact->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        if (isset($permissions['EmergencyContacts']['edit']) && ($permissions['EmergencyContacts']['edit'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $emergencyContact->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
                                        }

                                        if (isset($permissions['EmergencyContacts']['delete']) && ($permissions['EmergencyContacts']['delete'] == true)) {

                                            echo $this->Form->postLink(
                                                '<i class="far fa-trash-alt"></i>',
                                                array('action' => 'delete',  $emergencyContact->id),
                                                array(
                                                    'escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle' => 'tooltip', 'title' => __('delete'),
                                                    'confirm' => __('delete_message',  $emergencyContact->id)
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
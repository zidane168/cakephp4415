<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Administrator[]|\Cake\Collection\CollectionInterface $administrators
 */
?>

<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/administrator_filter', array(
                'data_search' => $data_search
            ));
            ?>
        </div>
    </div>
</div>

<div class="container-fluid card full-border">

    <div class="row">
        <div class="col-12">
            <div class="box box-primary table-responsive">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __d('administrator', 'administrators'); ?> </h3>

                    <?php if (isset($permissions['Administrators']['add']) && ($permissions['Administrators']['add'] == true)) { ?>

                        <div class="box-tools ml-auto p-1">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], ['escape' => false,    'class' => 'btn btn-primary button float-right']) ?>
                        </div>
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body"></div>
                <table id="<?php echo str_replace(' ', '', 'Administrator'); ?>" class="table table-hover table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center"><?= $this->Paginator->sort('Administrators.id',    __('id')) ?></th>
                            <th class="text-center"><?php echo '<a href="#">' . __('avatar') . '</a>'; ?> </th>
                            <th class="text-center"><?= $this->Paginator->sort('Administrators.email', __('email')) ?></th>
                            <th class="text-center"><?= __d('role', 'role') ?></th>
                            <th class="text-center"><?= __d('role', 'manage_center') ?></th>
                            <th class="text-center"><?= $this->Paginator->sort('Administrators.last_logged_in', __d('administrator', 'last_logged_in')) ?></th>
                            <th class="text-center"><?= $this->Paginator->sort('Administrators.enabled', __('enabled')) ?></th>
                            <th class="text-center"><?= $this->Paginator->sort('Administrators.created', __('created')) ?></th>
                            <th class="text-center"><?= $this->Paginator->sort('Administrators.modified', __('modified')) ?></th>
                            <th class="text-center"><?= __('operation') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($administrators as $administrator) : ?>
                            <tr>
                                <td class="text-center"><?= $this->Number->format($administrator->id) ?></td>
                                <td class="text-center">
                                    <?php
                                    if (isset($administrator['administrators_avatars']) && !empty($administrator['administrators_avatars'])) {
                                        echo $this->Html->image('../' . $administrator['administrators_avatars'][0]["path"], array(
                                            'class' => ' img-thumbnail preview'
                                        ));
                                    }
                                    ?>
                                </td>

                                <td class="text-center"><?= h($administrator->email) ?></td>

                                <td class="text-left">
                                    <?php foreach ($administrator->roles as $role) { ?>
                                        <li> <?= h($role->name); ?> </li>
                                    <?php } ?>
                                </td>

                                <td class="text-left">
                                    <?php foreach ($administrator->administrator_manage_centers as $center) { ?>
                                        <li> <?= h($center->center->center_languages[0]->name); ?> </li>
                                    <?php } ?>
                                </td>

                                <td class="text-center"><?= $this->element('customize_format_datetime', ['date' => $administrator->last_logged_in]) ?></td>
                                <td class="text-center">
                                    <?= $this->element('view_check_ico', array('_check' => $administrator->enabled)) ?>
                                </td>

                                <td class="text-center"><?= $this->element('customize_format_datetime', ['date' => $administrator->created]) ?></td>
                                <td class="text-center"><?= $this->element('customize_format_datetime', ['date' => $administrator->modified]) ?></td>
                                <td class="text-center">

                                    <?php
                                    if (isset($permissions['Administrators']['view']) && ($permissions['Administrators']['view'] == true)) {
                                        echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $administrator->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                    }

                                    if (isset($permissions['Administrators']['edit']) && ($permissions['Administrators']['edit'] == true)) {
                                        echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $administrator->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
                                    }

                                    if (isset($permissions['Administrators']['delete']) && ($permissions['Administrators']['delete'] == true)) {

                                        echo $this->Form->postLink(
                                            '<i class="far fa-trash-alt"></i>',
                                            array('action' => 'delete',  $administrator->id),
                                            array(
                                                'escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle' => 'tooltip', 'title' => __('delete'),
                                                'confirm' => __('delete_message',  $administrator->id)
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
            <!-- 
                <div class="paginator">
                    <ul class="pagination">
                        <?= $this->Paginator->first('<< ' . __('first')) ?>
                        <?= $this->Paginator->prev('< ' . __('previous')) ?>
                        <?= $this->Paginator->numbers() ?>
                        <?= $this->Paginator->next(__('next') . ' >') ?>
                        <?= $this->Paginator->last(__('last') . ' >>') ?>
                    </ul>
                    <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
                </div>
                -->

            <?= $this->element('Paginator'); ?>
        </div><!-- box, box-primary -->
    </div><!-- .col-12 -->
</div><!-- row -->
</div> <!-- container -->
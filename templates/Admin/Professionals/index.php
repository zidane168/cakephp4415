<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Professional[]|\Cake\Collection\CollectionInterface $professionals
 */
?>
<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/professional_filter', array(
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
                    <h3 class="box-title"> <?php echo __d('professional', 'professionals'); ?> </h3>

                    <?php if (isset($permissions['Professionals']['add']) && ($permissions['Professionals']['add'] == true)) { ?>

                        <div class="box-tools ml-auto p-1">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                                'escape' => false,
                                'class' => 'btn btn-primary button'
                            ]) ?>
                        </div>
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'Professional'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('Professionals.id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('Professionals.id', __('name')) ?></th>
                                <th><?= $this->Paginator->sort('Professionals.gender', __d('parent', 'gender')) ?></th>
                                <th><?= $this->Paginator->sort('Professionals.type', __d('professional', 'type')) ?></th>
                                <th><?= h(__('enabled')) ?></th>
                                <th><?= $this->Paginator->sort('Professionals.created', __('created')) ?></th>
                                <th><?= $this->Paginator->sort('Professionals.modified', __('modified')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($professionals as $professional) : ?>
                                <tr>
                                    <td><?= $this->Number->format($professional->id) ?></td>
                                    <td><?= h($professional['ProfessionalLanguages']['name']) ?></td>
                                    <td><?= h($genders[$professional->gender]) ?></td>
                                    <td><?= h($types[$professional->type]) ?></td>
                                    <td><?= $this->element('view_check_ico', array('_check' => $professional->enabled)) ?></td>
                                    <td><?= h($professional->created) ?></td>
                                    <td><?= h($professional->modified) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['Professionals']['view']) && ($permissions['Professionals']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $professional->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        if (isset($permissions['Professionals']['edit']) && ($permissions['Professionals']['edit'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $professional->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
                                        }

                                        if (isset($permissions['Professionals']['delete']) && ($permissions['Professionals']['delete'] == true)) {

                                            echo $this->Form->postLink(
                                                '<i class="far fa-trash-alt"></i>',
                                                array('action' => 'delete',  $professional->id),
                                                array(
                                                    'escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle' => 'tooltip', 'title' => __('delete'),
                                                    'confirm' => __('delete_message',  $professional->id)
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
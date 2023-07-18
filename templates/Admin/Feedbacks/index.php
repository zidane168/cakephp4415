<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Relationship[]|\Cake\Collection\CollectionInterface $feedbacks
 */
?>

<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/feedback_filter', array(
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
                    <h3 class="box-title"> <?php echo __d('setting','feedbacks'); ?> </h3>

                    <?php if (isset($permissions['Feedbacks']['add']) && ($permissions['Feedbacks']['add'] == true)) { ?>

                        <div class="box-tools ml-auto p-1">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                                'escape' => false,
                                'class' => 'btn btn-primary button'
                            ]) ?>
                        </div>
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'Feedback'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('Feedbacks.id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('Feedbacks.id', __('name')) ?></th>
                                <th><?= $this->Paginator->sort('Feedbacks.enabled', __('enabled')) ?></th>
                                <th><?= $this->Paginator->sort('Feedbacks.created', __('created')) ?></th>
                                <th><?= $this->Paginator->sort('Feedbacks.modified', __('modified')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($feedbacks as $feedback) : ?>
                                <tr>
                                    <td><?= $this->Number->format($feedback->id) ?></td>
                                    <td class="text-center"><?= h($feedback->FeedbackLanguages['name']) ?></td>
                                    <td><?= $this->element('view_check_ico', array('_check' => $feedback->enabled)) ?></td>
                                    <td><?= h($feedback->created) ?></td>
                                    <td><?= h($feedback->modified) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['Feedbacks']['view']) && ($permissions['Feedbacks']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $feedback->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        if (isset($permissions['Feedbacks']['edit']) && ($permissions['Feedbacks']['edit'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $feedback->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
                                        }

                                        if (isset($permissions['Feedbacks']['delete']) && ($permissions['Feedbacks']['delete'] == true)) {

                                            echo $this->Form->postLink(
                                                '<i class="far fa-trash-alt"></i>',
                                                array('action' => 'delete',  $feedback->id),
                                                array(
                                                    'escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle' => 'tooltip', 'title' => __('delete'),
                                                    'confirm' => __('delete_message',  $feedback->id)
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
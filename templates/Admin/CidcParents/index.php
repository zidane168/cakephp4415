<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface[]|\Cake\Collection\CollectionInterface $cidcParents
 */
?>

<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/cidc_parent_filter', array(
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
                    <h3 class="box-title"> <?php echo __d('parent', 'cidc_parents'); ?> </h3>

                    <?php if (isset($permissions['CidcParents']['add']) && ($permissions['CidcParents']['add'] == true)) { ?>

                        <div class="p-1 ml-auto box-tools">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                                'escape' => false,
                                'class' => 'btn btn-primary button'
                            ]) ?>
                        </div>
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'Cidc Parent'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('CidcParents.id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('CidcParentLanguages.name', __('name')) ?></th>
                                <th class="text-center"><?php echo '<a href="#">' . __('thumbnail') . '</a>'; ?> </th>
                                <th><?= $this->Paginator->sort('CidcParents.id', __('phone')) ?></th>
                                <th><?= $this->Paginator->sort('CidcParents.id', __('email')) ?></th>
                                <th><?= $this->Paginator->sort('CidcParents.gender', __d('parent', 'gender')) ?></th>
                                <th><?= h(__('enabled')) ?></th>
                                <th><?= $this->Paginator->sort('CidcParents.created', __('created')) ?></th>
                                <th><?= $this->Paginator->sort('CidcParents.modified', __('modified')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($cidcParents as $cidcParent) : ?>
                                <tr>
                                    <td><?= $this->Number->format($cidcParent->id) ?></td>
                                    <td><?= h($cidcParent['CidcParentLanguages']['name']) ?></td>
                                    <td class="text-center">
                                        <?php
                                        if (isset($cidcParent['cidc_parent_images']) && !empty($cidcParent['cidc_parent_images'])) {
                                            echo $this->Html->image('../' . $cidcParent['cidc_parent_images'][0]["path"], array(
                                                'class' => ' img-thumbnail preview'
                                            ));
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?= 
                                            $this->Com->formatPhoneNumber($cidcParent->user->phone_number);
                                            // $cidcParentsModel->format_phone_number($cidcParent->user->phone_number) 
                                        ?>
                                    </td>
                                    
                                    <td><?= h($cidcParent->user->email) ?></td>
                                    <td><?= h($genders[$cidcParent->gender]) ?></td>
                                    <td><?= $this->element('view_check_ico', array('_check' => $cidcParent->user->enabled)) ?></td>
                                    <td><?= h($cidcParent->created) ?></td>
                                    <td><?= h($cidcParent->modified) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['CidcParents']['view']) && ($permissions['CidcParents']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $cidcParent->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        if (isset($permissions['CidcParents']['edit']) && ($permissions['CidcParents']['edit'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $cidcParent->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
                                        }

                                        if (isset($permissions['CidcParents']['delete']) && ($permissions['CidcParents']['delete'] == true)) {

                                            $icon = '<i class="fa-solid fa-trash-arrow-up"></i>';
                                            $color = 'btn-success';
                                            $message = __('enabled_message',  $cidcParent->id);
                                            $tooltip = __('enabled');

                                            if ($cidcParent->user->enabled == true) {
                                                $icon = '<i class="far fa-trash-alt"></i>';
                                                $color = 'btn-danger';
                                                $message = __('disabled_message',  $cidcParent->id);
                                                $tooltip = __('disabled');
                                            }

                                            echo $this->Form->postLink(
                                                $icon,
                                                array('action' => 'enabled_disabled_feature',  $cidcParent->id),
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
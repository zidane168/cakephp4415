<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Kid[]|\Cake\Collection\CollectionInterface $kids
 */
?>
<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/kid_filter', array(
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
                    <h3 class="box-title"> <?php echo __d('parent', 'kids'); ?> </h3>

                    <?php if (isset($permissions['Kids']['add']) && ($permissions['Kids']['add'] == true)) { ?>

                        <div class="box-tools ml-auto p-1">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                                'escape' => false,
                                'class' => 'btn btn-primary button'
                            ]) ?>
                        </div>
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'Kid'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('Kids.id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('Kids.id', __('name')) ?></th>
                                <th><?= $this->Paginator->sort('Kids.parent_id', __d('parent', 'cidc_parent')) ?></th>
                                <th><?= $this->Paginator->sort('Kids.relationship_id', __d('setting', 'relationship')) ?></th>
                                <th><?= $this->Paginator->sort('Kids.gender', __d('parent', 'gender')) ?></th>
                                <th><?= $this->Paginator->sort('Kids.dob', __d('parent', 'dob')) ?></th>
                                <th><?= $this->Paginator->sort('Kids.number_of_siblings', __d('parent', 'number_of_siblings')) ?></th>
                                <th><?= $this->Paginator->sort('Kids.caretaker', __d('parent', 'caretaker')) ?></th>
                                <th><?= $this->Paginator->sort('Kids.enabled', __('enabled')) ?></th>
                                <th><?= $this->Paginator->sort('Kids.created', __('created')) ?></th>
                                <th><?= $this->Paginator->sort('Kids.modified', __('modified')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($kids as $kid) : ?>
                                <tr>
                                    <td><?= $this->Number->format($kid->id) ?></td>
                                    <td><?= h($kid['KidLanguages']['name']) ?></td>
                                    <td><?= $kid->has('cidc_parent') ? $this->Html->link($kid->cidc_parent->cidc_parent_languages[0]->name, ['controller' => 'CidcParents', 'action' => 'view', $kid->cidc_parent_id]) : '' ?></td>
                                    <td><?= $kid->has('relationship') ? $this->Html->link($kid->relationship->relationship_languages[0]->name, ['controller' => 'Relationships', 'action' => 'view', $kid->relationship_id]) : '' ?></td>
                                    <td><?= h($genders[$kid->gender]) ?></td>
                                    <td><?= h($kid->dob ? $kid->dob->format('Y-m-d') : null) ?></td>
                                    <td><?= $this->Number->format($kid->number_of_siblings) ?></td>
                                    <td><?= h($kid->caretaker) ?></td>
                                    <td><?= $this->element('view_check_ico', array('_check' => $kid->enabled)) ?></td>
                                    <td><?= h($kid->created) ?></td>
                                    <td><?= h($kid->modified) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['Kids']['view']) && ($permissions['Kids']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $kid->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        if (isset($permissions['Kids']['edit']) && ($permissions['Kids']['edit'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $kid->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
                                        }

                                        if (isset($permissions['Kids']['delete']) && ($permissions['Kids']['delete'] == true)) {

                                            echo $this->Form->postLink(
                                                '<i class="far fa-trash-alt"></i>',
                                                array('action' => 'delete',  $kid->id),
                                                array(
                                                    'escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle' => 'tooltip', 'title' => __('delete'),
                                                    'confirm' => __('delete_message',  $kid->id)
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
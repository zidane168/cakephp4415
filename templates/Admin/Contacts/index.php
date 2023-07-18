<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface[]|\Cake\Collection\CollectionInterface $contacts
 */
?>
<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/contact_filter', array(
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
                    <h3 class="box-title"> <?php echo __d('setting', 'contacts'); ?> </h3>

                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'Contact'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('Contacts.id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('Contacts.enabled', __('title')) ?></th>
                                <th><?= $this->Paginator->sort('Contacts.enabled', __('enabled')) ?></th>
                                <th><?= $this->Paginator->sort('Contacts.created', __('created')) ?></th>
                                <th><?= $this->Paginator->sort('Contacts.modified', __('modified')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($contacts as $contact) : ?>
                                <tr>
                                    <td><?= $this->Number->format($contact->id) ?></td>
                                    <td class="text-center"> <?= h($contact['ContactLanguages']['title']) ?></td>
                                    <td><?= $this->element('view_check_ico', array('_check' => $contact->enabled)) ?></td>
                                    <td><?= h($contact->created) ?></td>
                                    <td><?= h($contact->modified) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['Contacts']['view']) && ($permissions['Contacts']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $contact->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        if (isset($permissions['Contacts']['edit']) && ($permissions['Contacts']['edit'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $contact->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
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
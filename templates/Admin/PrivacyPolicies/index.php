<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface[]|\Cake\Collection\CollectionInterface $privacyPolicies
 */
?>


<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/private_policy_filter', array(
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
                    <h3 class="box-title"> <?php echo __d('cidcclass', 'privacy_policies'); ?> </h3>
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'Privacy Policy'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('PrivacyPolicies.id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('PrivacyPolicies.id', __d('center', 'title')) ?></th>
                                <th><?= $this->Paginator->sort('PrivacyPolicies.enabled', __('enabled')) ?></th>
                                <th><?= $this->Paginator->sort('PrivacyPolicies.created', __('created')) ?></th>
                                <th><?= $this->Paginator->sort('PrivacyPolicies.modified', __('modified')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($privacyPolicies as $privacyPolicy) : ?>
                                <tr>

                                    <td><?= $this->Number->format($privacyPolicy->id) ?></td>
                                    <td><?= h($privacyPolicy['PrivacyPolicyLanguages']['title']) ?></td>
                                    <td><?= $this->element('view_check_ico', array('_check' => $privacyPolicy->enabled)) ?></td>
                                    <td><?= h($privacyPolicy->created) ?></td>
                                    <td><?= h($privacyPolicy->modified) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['PrivacyPolicies']['view']) && ($permissions['PrivacyPolicies']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $privacyPolicy->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        }

                                        if (isset($permissions['PrivacyPolicies']['edit']) && ($permissions['PrivacyPolicies']['edit'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $privacyPolicy->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
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
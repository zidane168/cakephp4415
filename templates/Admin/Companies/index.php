<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Company[]|\Cake\Collection\CollectionInterface $companies
 */
?>

<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/company_filter', array(
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
                    <h3 class="box-title"> <?php echo __d('company', 'company'); ?> </h3>

                    <?php if (isset($permissions['Companies']['add']) && ($permissions['Companies']['add'] == true)) { ?>

                        <div class="box-tools ml-auto p-1">
                            <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                                'escape' => false,
                                'class' => 'btn btn-primary button float-right'
                            ]) ?>
                        </div>
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body"></div>
                <table id="<?php echo str_replace(' ', '', 'Company'); ?>" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="text-center"><?= $this->Paginator->sort('Companies.id',        __('id')) ?></th>
                            <th class="text-center"><?= $this->Paginator->sort('Companies.email',     __('email')) ?></th>
                            <th class="text-center"><?= $this->Paginator->sort('Companies.name',     __('name')) ?></th>
                            <th class="text-center"><?= $this->Paginator->sort('Companies.facebook',  __('facebook')) ?></th>
                            <th class="text-center"><?= $this->Paginator->sort('Companies.enabled',           __('enabled')) ?></th>
                            <th class="text-center"><?= $this->Paginator->sort('Companies.created',           __('created')) ?></th>
                            <th class="text-center"><?= $this->Paginator->sort('Companies.created_by',        __('created_by')) ?></th>
                            <th class="text-center"><?= $this->Paginator->sort('Companies.modified',          __('modified')) ?></th>
                            <th class="text-center"><?= $this->Paginator->sort('Companies.modified_by',       __('modified_by')) ?></th>
                            <th class="text-center"><?= __('operation') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($companies as $company) : ?>
                            <tr>
                                <td class="text-center"><?= $this->Number->format($company->id) ?></td>
                                <td class="text-center"><?= h($company->email) ?></td>
                                <td class="text-center"><?= h($company['CompanyLanguages']['name']) ?></td>
                                <td class="text-center"><?= h($company->facebook) ?></td>

                                <td class="text-center">
                                    <?= $this->element('view_check_ico', array('_check' => $company->enabled)) ?>
                                </td>
                                <td class="text-center"><?= $this->element('customize_format_datetime', ['date' => $company->created])
                                                        ?></td>
                                <td class="text-center">
                                    <?= $company->created_by ? h($company->created_by['name']) : '' ?>
                                </td>
                                <td class="text-center"><?= $this->element('customize_format_datetime', ['date' => $company->modified]) ?></td>
                                <td class="text-center">
                                    <?= $company->modified_by ? h($company->modified_by['name']) : '' ?>
                                </td>
                                <td class="text-center">

                                    <?php
                                    if (isset($permissions['Companies']['view']) && ($permissions['Companies']['view'] == true)) {
                                        echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $company->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                    }

                                    if (isset($permissions['Companies']['edit']) && ($permissions['Companies']['edit'] == true)) {
                                        echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $company->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('edit')));
                                    }

                                    if (isset($permissions['Companies']['delete']) && ($permissions['Companies']['delete'] == true)) {

                                        echo $this->Form->postLink(
                                            '<i class="far fa-trash-alt"></i>',
                                            array('action' => 'delete',  $company->id),
                                            array(
                                                'escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle' => 'tooltip', 'title' => __('delete'),
                                                'confirm' => __('delete_message',  $company->id)
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
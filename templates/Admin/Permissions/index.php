<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Permission[]|\Cake\Collection\CollectionInterface $permissions
 */
?>

<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/permission_filter', array(
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
                    <h3 class="box-title"> <?php echo __d('permission', 'permissions'); ?> </h3>

                    <?php if (isset($permissions['Permissions']['add']) && ($permissions['Permissions']['add'] == true)) { ?>

                        <div class="box-tools ml-auto p-1">
                            <?= $this->Html->link(__('add'), ['action' => 'addall'], ['class' => 'btn btn-primary button float-right']) ?>
                        </div>
                    <?php   }  ?>
                </div> <!-- box-header -->

                <div class="box-body "></div>
                <table id="<?php echo str_replace(' ', '', 'Permission'); ?>" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center"><?= $this->Paginator->sort('Permissions.p_controller', __('p_controller')) ?></th>
                            <th class="text-center"><?= $this->Paginator->sort('Permissions.p_model', __('p_model')) ?></th>
                            <th class="text-center"><?= $this->Paginator->sort('Permissions.name', __('name')) ?></th>
                            <th class="text-center"><?= $this->Paginator->sort('Permissions.slug', __('slug')) ?></th>

                            <th class="text-center"><?= $this->Paginator->sort('action', __('action')) ?></th>
                            <th class="text-center"><?= __('operation') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $arr = $permissions_records->toArray();

                        $flag_controller = "";
                        foreach ($permissions_records as $item) {
                            $permission = $item;
                        ?>
                            <tr>
                                <?php
                                $first_time_items = array();
                                $num_row_span = 1;
                                if (strtolower($permission->p_controller) != strtolower($flag_controller)) {
                                    $flag_controller = $permission->p_controller;

                                    $first_time_items = array_filter($arr, function ($item) use ($flag_controller) {
                                        return strtolower($item['p_controller']) == strtolower($flag_controller);
                                    });
                                    $num_row_span = count($first_time_items);

                                ?>

                                    <td class="text-center" style="vertical-align: middle" rowspan="<?= $num_row_span; ?>">
                                        <?= h($permission->p_controller); ?>
                                    </td>

                                <?php } ?>

                                <td class="text-center"><?= h($item->p_model) ?></td>
                                <td class="text-center"><?= h($item->name) ?></td>
                                <td class="text-center"><?= h($item->slug) ?></td>
                                <td class="text-center"><?= h($item->action) ?></td>

                                <?php
                                if (isset($permissions['Permissions']['delete']) && ($permissions['Permissions']['delete'] == true)) {
                                    if (count($first_time_items) > 0) {
                                        $num_row_span = count($first_time_items);
                                ?>
                                        <td class="text-center" style="vertical-align: middle" rowspan="<?= $num_row_span; ?>">
                                            <?php echo $this->Form->create(
                                                NULL,
                                                array(
                                                    $this->Url->build(['controller' => 'permissions', 'action' => 'delete_all', 'prefix' => 'Admin']),
                                                    'type' => 'get',
                                                    'onsubmit' => "return confirm('是否確認刪除這個p_model?')"
                                                )
                                            );
                                            ?>

                                            <div class="form-group">
                                                <?php
                                                foreach ($first_time_items as $per) {

                                                    echo $this->Form->control('id', array(
                                                        'type' => 'hidden',
                                                        'class' => 'form-control',
                                                        'name' => 'ids[]',
                                                        'value' => $per->id,
                                                    ));
                                                }
                                                // echo $this->Form->button('<i class="glyphicon glyphicon-trash"></i>',
                                                //     [
                                                //         'escape' => false,
                                                //         'type' => 'submit', 
                                                //         'class' => 'btn btn-large btn-danger',
                                                //     ]);

                                                echo $this->Form->control(__('delete_all'), array(
                                                    'div' => false,
                                                    'label' => false,
                                                    'type' => 'submit',
                                                    'name' => 'button[delete_all]',
                                                    'class' => 'btn btn-danger filter-button',
                                                ));


                                                // echo $this->Form->submit('Delete', array(
                                                //         'escape' => false,
                                                //         'class' => 'btn btn-large btn-danger')); 
                                                ?>
                                            </div>
                                            <?= $this->Form->end(); ?>
                                        </td>
                                    <?php } ?>
                                <?php } ?>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div>
            <?php echo $this->element('Paginator'); ?>
        </div><!-- box, box-primary -->
    </div><!-- .col-12 -->
</div><!-- row -->
</div> <!-- container -->


<?php
// echo $this->Form->postLink(  
//     '<i class="far fa-trash-alt"></i>',  array('action' => 'delete',  $item->id), 
//     array('escape' => false, 'class' => 'btn btn-danger btn-xs m-r-btn', 'data-toggle'=>'tooltip', 'title' => __('delete')), 
//     array('confirm' => __('Are you sure you want to delete # %s?',  $item->id )));


?>
<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Permission $role
 */
?>
<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"><?= __d('role', 'role'); ?></h3>
                    <?php if (isset($permissions['Roles']['add']) && ($permissions['Roles']['add'] == true)) { ?>
                        
                        <div class="box-tools ml-auto p-1">
                            <?php echo $this->Html->link('<i class="fa fa-pencil"></i> '.__('edit'), array('action' => 'edit', $role->id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                        </div>    
                    <?php  }  ?>
                </div>
            </div>

            <div class="box box-body">
                <table id="Roles" class="table table-bordered table-striped">

                    <tr>
                        <th><?= __('id') ?></th>
                        <td><?= $this->Number->format($role->id) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('slug') ?></th>
                        <td><?= h($role->slug) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('name') ?></th>
                        <td><?= h($role->name) ?></td>
                    </tr>
                  
                    <tr>
                        <th><?= __('modified_by') ?></th>
                        <td><?= $role->modified_by ? h($role->modified_by['name']) : '' ?></td>
                    </tr>
                    <tr>
                        <th><?= __('created_by') ?></th>
                        <td><?= $role->created_by ? h($role->created_by['name']) : '' ?></td>
                    </tr>
                    <tr>
                        <th><?= __('modified') ?></th>
                        <td><?= h($role->updated) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('created') ?></th>
                        <td><?= h($role->created) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"><?= __d('permission', 'permission'); ?></h3>
                </div>
            </div>

            <div class="box-body table-responsive"></div>
                <table id="<?php echo str_replace(' ', '', 'Permission'); ?>" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center"><?= $this->Paginator->sort('p_controller',  __d('permission', 'p_controller')) ?></th>
                            <th class="text-center"><?= $this->Paginator->sort('p_model',       __d('permission', 'p_model')) ?></th>
                            <th class="text-center"><?= __('add'); ?> </th>
                            <th class="text-center"><?= __('edit'); ?> </th>
                            <th class="text-center"><?= __('delete'); ?> </th>
                            <th class="text-center"><?= __('view'); ?> </th>
                        </tr>
                    </thead>
                    <tbody>
<?php foreach($permissions_matrix as $item) { ?>
                        <tr>
                            <td class="text-center"><?= h($item['p_controller']) ?></td>
                            <td class="text-center"><?= h($item['p_model']) ?> </td>
                            <td class="text-center"><?= $this->element('view_check_ico',array('_check' => $item['add'])) ?> </td>
                            <td class="text-center"><?= $this->element('view_check_ico',array('_check' => $item['edit'])) ?> </td>
                            <td class="text-center"><?= $this->element('view_check_ico',array('_check' => $item['delete'])) ?> </td>
                            <td class="text-center"><?= $this->element('view_check_ico',array('_check' => $item['view'])) ?> </td>
                        </tr>
<?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>        

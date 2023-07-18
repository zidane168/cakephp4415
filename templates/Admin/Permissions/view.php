<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Permission $permission
 */
?>
<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"><?= __('administrator'); ?></h3>
                    <?php if (isset($permissions['Administrators']['add']) && ($permissions['Administrators']['add'] == true)) { ?>
                        
                        <div class="box-tools ml-auto p-1">
                            <?php echo $this->Html->link('<i class="fa fa-pencil"></i> '.__('edit'), array('action' => 'edit', $administrator->id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                        </div>    
                    <?php  }  ?>
                </div>
            </div>

            <div class="box box-body">
                <h3><?= h($permission->name) ?></h3>
                <table id="Administrators" class="table table-bordered table-striped">

                    <tr>
                        <th><?= __('Id') ?></th>
                        <td><?= $this->Number->format($permission->id) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Slug') ?></th>
                        <td><?= h($permission->slug) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Name') ?></th>
                        <td><?= h($permission->name) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('P Plugin') ?></th>
                        <td><?= h($permission->p_plugin) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('P Controller') ?></th>
                        <td><?= h($permission->p_controller) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('P Model') ?></th>
                        <td><?= h($permission->p_model) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Action') ?></th>
                        <td><?= h($permission->action) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('modified_by') ?></th>
                        <td><?= $permission->modified_by ? h($permission->modified_by['name']) : '' ?></td>
                    </tr>
                    <tr>
                        <th><?= __('created_by') ?></th>
                        <td><?= $permission->created_by ? h($permission->created_by['name']) : '' ?></td>
                    </tr>
                    <tr>
                        <th><?= __('updated') ?></th>
                        <td><?= h($permission->updated) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('created') ?></th>
                        <td><?= h($permission->created) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>        

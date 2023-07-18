<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Role[]|\Cake\Collection\CollectionInterface $roles
 */
?>

<div class="container-fluid card full-border">

	<div class="row">
		<div class="col-12">
			<div class="box box-primary">
				<div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __d('role', 'role'); ?> </h3>
				
                    <?php if (isset($permissions['Roles']['add']) && ($permissions['Roles']['add'] == true)) { ?>
                    
                    <div class="box-tools ml-auto p-1">
                        <?= $this->Html->link(__('add'), ['action' => 'add'], ['class' => 'btn btn-primary button float-right']) ?>
                    </div>    
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body table-responsive"></div>
                    <table id="<?php echo str_replace(' ', '', 'Role'); ?>" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center"><?= $this->Paginator->sort('Roles.id', __('id')) ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('Roles.slug', __('slug')) ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('Roles.name', __('name')) ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('Roles.created', __('created')) ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('Roles.modified', __('modified')) ?></th>
                                
                                <!-- <th class="text-center"><?= $this->Paginator->sort('Roles.created_by', __('created_by')) ?></th> -->
                                <th class="text-center"><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($roles as $role): ?>
                            <tr>
                                <td class="text-center"><?= $this->Number->format($role->id) ?></td>
                                <td class="text-center"><?= h($role->slug) ?></td>
                                <td class="text-center"><?= h($role->name) ?></td>
                               
                                <td class="text-center"><?= $this->element('customize_format_datetime', ['date' => $role->created] ) ?></td>            
                                <td class="text-center"><?= $this->element('customize_format_datetime', ['date' => $role->modified] ) ?></td>                                        
                                <!-- <td class="text-center"><?= $role->created_by ?  $role->created_by['name'] : '' ?></td> -->
                                <td class="text-center">
                        
                                    <?php 
                                    if (isset($permissions['Roles']['view']) && ($permissions['Roles']['view'] == true)) { 
                                        echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $role->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('view')));
                                    } 
                                
                                    if (isset($permissions['Roles']['edit']) && ($permissions['Roles']['edit'] == true)) { 
                                        echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $role->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('edit')));
                                    }

                                    if (isset($permissions['Roles']['delete']) && ($permissions['Roles']['delete'] == true)) { 
                                        
                                        echo $this->Form->postLink(  
                                            '<i class="far fa-trash-alt"></i>',  array('action' => 'delete',  $role->id), 
                                            array(
                                                'escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle'=>'tooltip', 'title' => __('delete'), 
                                                'confirm' => __('delete_message',  $role->id)
                                            ));
                
                                    }
                                    
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
				</div>
                <!-- 
                <div class="paginator">
                    <ul class="pagination">
                        <?= $this->Paginator->first('<< ' . __('first')) ?>
                        <?= $this->Paginator->prev('< ' . __('previous')) ?>
                        <?= $this->Paginator->numbers() ?>
                        <?= $this->Paginator->next(__('next') . ' >') ?>
                        <?= $this->Paginator->last(__('last') . ' >>') ?>
                    </ul>
                    <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
                </div>
                -->
						
				<?= $this->element('Paginator'); ?>
			</div><!-- box, box-primary -->
		</div><!-- .col-12 -->
	</div><!-- row -->
</div> <!-- container -->
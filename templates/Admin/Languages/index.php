<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Language[]|\Cake\Collection\CollectionInterface $languages
 */
?>

<div class="container-fluid card full-border">

	<div class="row">
		<div class="col-12">
			<div class="box box-primary">
				<div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __('language'); ?> </h3>
				
                    <?php if (isset($permissions['Language']['add']) && ($permissions['Language']['add'] == true)) { ?>
                    
                    <div class="box-tools ml-auto p-1">
                        <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                            'escape' => false,
                            'class' => 'btn btn-primary button float-right']) ?>
                    </div>    
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body"></div>
                    <table id="<?php echo str_replace(' ', '', 'Language'); ?>" class="table  table-responsive table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center"><?= $this->Paginator->sort('id', __('id')) ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('alias', __('alias')) ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('name', __('name')) ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('is_default', __('is_default')) ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('enabled', __('enabled')) ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('modified', __('modified')) ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('modified_by', __('modified_by')) ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('created', __('created')) ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('created_by', __('created_by')) ?></th>
                                <th class="text-center"><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($languages as $language): ?>
                            <tr>
                                <td class="text-center"><?= $this->Number->format($language->id) ?></td>
                                    <td class="text-center"><?= h($language->alias) ?></td>
                                    <td class="text-center"><?= h($language->name) ?></td>
                                    <td class="text-center"><?= h($language->is_default) ?></td>
                                    <td class="text-center"><?= h($language->enabled) ?></td>
                                    <td class="text-center"><?= h($language->modified) ?></td>
                                        <td class="text-center"><?= $this->Number->format($language->modified_by) ?></td>
                                    <td class="text-center"><?= h($language->created) ?></td>
                                        <td class="text-center"><?= $this->Number->format($language->created_by) ?></td>
                                <td class="text-center">
                    	
                                    <?php 
                                    // if (isset($permissions['Language']['view']) && ($permissions['Language']['view'] == true)) { 
                                        echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $language->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('view')));
                                    // } 
                                 
                                    // if (isset($permissions['Language']['edit']) && ($permissions['Language']['edit'] == true)) { 
                                        echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $language->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('edit')));
                                    // }

                                    // if (isset($permissions['Language']['delete']) && ($permissions['Language']['delete'] == true)) { 
	                                    
                                        echo $this->Form->postLink(  
                                            '<i class="far fa-trash-alt"></i>',  array('action' => 'delete',  $language->id), 
                                            array('escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle'=>'tooltip', 'title' => __('delete')), 
                                            array('confirm' => __('Are you sure you want to delete # %s?',  $language->id )));
				
                                    // }
		                            
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
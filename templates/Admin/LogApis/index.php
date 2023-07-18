<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\LogApi[]|\Cake\Collection\CollectionInterface $logApis
 */
?>

<div class="container-fluid card full-border">

	<div class="row">
		<div class="col-12">
			<div class="box box-primary table-responsive">
				<div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __('logApi'); ?> </h3>
				
                    <?php if (isset($permissions['Log Apis']['add']) && ($permissions['Log Apis']['add'] == true)) { ?>
                    
                    <div class="box-tools ml-auto p-1">
                        <?= $this->Html->link("<i class='fas fa-plus'> </i> " . __('add'), ['action' => 'add'], [
                            'escape' => false,
                            'class' => 'btn btn-primary button']) ?>
                    </div>    
                    <?php  }  ?>
                </div> <!-- box-header -->

                <div class="box-body"></div>
                    <table id="<?php echo str_replace(' ', '', 'Log Api'); ?>" class="table table-hover table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center"><?= $this->Paginator->sort('id', __('id')) ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('url', __('url')) ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('status', __('status')) ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('archived', __('archived')) ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('created', __('created')) ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('created_by', __('created_by')) ?></th>
                                <th class="text-center"><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logApis as $logApi): ?>
                            <tr>
                                                <td class="text-center"><?= $this->Number->format($logApi->id) ?></td>
                                                    <td class="text-center"><?= h($logApi->url) ?></td>
                                                    <td class="text-center"><?= h($logApi->status) ?></td>
                                                    <td class="text-center"><?= h($logApi->archived) ?></td>
                                                    <td class="text-center"><?= h($logApi->created) ?></td>
                                                        <td class="text-center"><?= $this->Number->format($logApi->created_by) ?></td>
                                <td class="text-center">
                    	
                                <?php 
                                    if (isset($permissions['Log Apis']['view']) && ($permissions['Log Apis']['view'] == true)) { 
                                        echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $logApi->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('view')));
                                    } 
                                 
                                    if (isset($permissions['Log Apis']['edit']) && ($permissions['Log Apis']['edit'] == true)) { 
                                        echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $logApi->id), array('class' => 'btn btn-warning btn-sm m-r-btn', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('edit')));
                                    }

                                    if (isset($permissions['Log Apis']['delete']) && ($permissions['Log Apis']['delete'] == true)) { 
	                                    
                                        echo $this->Form->postLink(  
                                            '<i class="far fa-trash-alt"></i>',  array('action' => 'delete',  $logApi->id), 
                                            array('escape' => false, 'class' => 'btn btn-danger btn-sm m-r-btn', 'data-toggle'=>'tooltip', 'title' => __('delete')), 
                                            array('confirm' => __('Are you sure you want to delete # %s?',  $logApi->id )));
				
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
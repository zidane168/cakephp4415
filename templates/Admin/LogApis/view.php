<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\LogApi $logApi
 */
?>

<div class="container-fluid card full-border">


<div class="row">
    <div class="col-12">
        <div class="box box-primary">
            <div class="box-header d-flex">
				<h3 class="box-title"><?php echo "Log Api" ?></h3>

	            <?php 
            		if (isset($permissions['Log Api']['edit']) && ($permissions['Log Api']['edit'] == true)) { 
				?>

				<div class="box-tools ml-auto p-1">
	                <?php echo $this->Html->link('<i class="fa fa-pencil"></i> ' . __('edit'), array('action' => 'edit', $logApi->id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
	            </div>
                
                <?php 
                    }
				?>
			</div>

	        <div class="box-body">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th><?= __('Url') ?></th>
                        <td><?= h($logApi->url) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Id') ?></th>
                        <td><?= $this->Number->format($logApi->id) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Created By') ?></th>
                        <td><?= $this->Number->format($logApi->created_by) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Created') ?></th>
                        <td><?= h($logApi->created) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Status') ?></th>
                        <td><?= $logApi->status ? __('Yes') : __('No'); ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Archived') ?></th>
                        <td><?= $logApi->archived ? __('Yes') : __('No'); ?></td>
                    </tr>
                </table>


                <div class="text">
                    <strong><?= __('Params') ?></strong>
                    <blockquote>
                        <?= $this->Text->autoParagraph(h($logApi->params)); ?>
                    </blockquote>
                </div>
                <div class="text">
                    <strong><?= __('Result') ?></strong>
                    <blockquote>
                        <?= $this->Text->autoParagraph(h($logApi->result)); ?>
                    </blockquote>
                </div>
                <div class="text">
                    <strong><?= __('Old Data') ?></strong>
                    <blockquote>
                        <?= $this->Text->autoParagraph(h($logApi->old_data)); ?>
                    </blockquote>
                </div>
                <div class="text">
                    <strong><?= __('New Data') ?></strong>
                    <blockquote>
                        <?= $this->Text->autoParagraph(h($logApi->new_data)); ?>
                    </blockquote>
                </div>
            </div>
        </div>
    </div>
</div>


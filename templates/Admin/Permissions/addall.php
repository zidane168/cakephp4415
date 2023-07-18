
<div class="row">
    <div class="col-12">
		<div class="card card-primary">
			<div class="card-header">
				<h3 class="card-title"><?php echo __d('permission', 'add_permission'); ?></h3>
			</div>

			<div class="card-body">
                <?= $this->Form->create($permission) ?>
					<fieldset>
                      
						<div class="form-group">
                            <?php 
                                echo $this->Form->control('name', array(
                                    'class' => 'form-control', 
                                    'id' => 'txtName',
                                    'required' => 'required',
									'escape' => false,		
                                    'label' => "<font class='red'> * </font>" . __('name')
                                )); 
                            ?>
						</div>
                        
						<div class="form-group">
							<?php echo $this->Form->control('p_controller', array(
								'class' => 'form-control',
                                'required' => 'required',
								'escape' => false,		
								'label'=>  "<font class='red'> * </font>" . __d('permission', 'p_controller'))); ?>
						</div>

						<div class="form-group">
							<?php echo $this->Form->control('p_model', array(
								'class' => 'form-control',
                                'required' => 'required',
								'escape' => false,		/// this row for active HTML code "<font class='red'> * </font>" .
								'label'=> "<font class='red'> * </font>" . __d('permission', 'p_model'))); ?>
                        </div>
                        
						<div class="pull-right">
							<?php echo $this->Form->submit(__('submit'), array(
								'id' => 'btnAdded',
								'class' => 'btn btn-large btn-primary')); ?>
						</div>

					</fieldset>

				<?php echo $this->Form->end(); ?>

			</div>
			
		</div>

	</div>
</div>
<?php
    // echo $this->Html->script('CakeAdminLTE/pages/admin_permission', array('inline' => false));
?>
<script type="text/javascript">
	$(document).ready(function(){
        // ADMIN_PERMISSION.slugs = JSON.parse('<?php // $slugs; ?>');
        // ADMIN_PERMISSION.names = JSON.parse('<?php // $names; ?>');

        // ADMIN_PERMISSION.init_page();
	});
</script>

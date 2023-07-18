
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
                            <div>
                                <label><?= '<font color="red">*</font> ' . __('slug') ?></label>
                                <div class="input-group">
                                    <span class="input-group-addon" >perm-admin-</span>
                                    <?php 
                                        echo $this->Form->control('slug', array(
                                            'class' => 'form-control',
                                            'id' => 'txtSlug',
                                            'label' => false,
                                            'type' => 'text',
                                            'required' => 'required',
                                        ));
                                    ?>
                                </div>
                            </div>
                            <!-- /.input group -->
                            <label id="slug_error" style="color:red"> </label>
                        </div>
                        
						<div class="form-group d-flex">
                            <span class="red"> * </span>
                            <?php 
                                echo $this->Form->control('name', array(
                                    'class' => 'form-control', 
                                    'id' => 'txtName',
                                    'required' => 'required',
                                    'label' => __('name')
                                )); 
                            ?>
                            <label id="name_error" style="color:red"> </label>
						</div>
						
						<div class="form-group d-flex">
                            <span class="red"> * </span>
                            <?php
                                echo $this->Form->control('action_id', array(
                                    'class' => 'form-control selectpicker',
                                    'data-live-search' => true,
                                    'empty' => __("please_select"),
                                    'required' => 'required',
                                    'escape' => false,		
                                    'label' =>  "<font class='red'> * </font> " . __d('administration','action'),
                                ));
                            ?>
                        </div>
                        
						<div class="form-group d-flex">
                            <span class="red"> * </span>
							<?php echo $this->Form->control('p_controller', array(
								'class' => 'form-control',
                                'required' => 'required',
								'label'=>  __d('administration','p_controller'))); ?>
						</div>

						<div class="form-group d-flex">
                            <span class="red"> * </span>
							<?php echo $this->Form->control('p_model', array(
								'class' => 'form-control',
                                'required' => 'required',
								'label'=> __d('administration','p_model'))); ?>
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
    echo $this->Html->script('CakeAdminLTE/pages/admin_permission', array('inline' => false));
?>
<script type="text/javascript">
	$(document).ready(function(){
        ADMIN_PERMISSION.slugs = JSON.parse('<?= $slugs; ?>');
        ADMIN_PERMISSION.names = JSON.parse('<?= $names; ?>');

        ADMIN_PERMISSION.init_page();
	});
</script>

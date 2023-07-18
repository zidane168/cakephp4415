<div class="row filter-panel">
	<div class="col-md-12">
		<?php 
			echo $this->Form->create(NULL, array(
                $this->Url->build(['controller' => 'companies', 'action' => 'index', 'admin' => true]), 
                'class' => 'form_filter',
                'type' => 'get',
			));
        ?>
 
		<div class="action-buttons-wrapper">
			<div class="row">
                <div class="col-md-4">
                    <?php 
                        echo $this->Form->control('name', array(
                            'class' => 'form-control',
                            'label' => __('name'),
                            'value' => isset($data_search['name']) && !empty($data_search['name']) ? $data_search['name'] : '', 
                        )); 
                    ?>
                </div>
                <div class="col-md-4">
                    <?php 
                        echo $this->Form->control('email', array(
                            'class' => 'form-control',
                            'label' => __('email'),
                            'value' => isset($data_search['email']) && !empty($data_search['email']) ? $data_search['email'] : '', 
                        )); 
                    ?>
                </div>
            
             
				<div class="col-md-4">
					<div><label><?php echo __('enabled'); ?></label></div>
					<div class="btn-group btn-group-sm" data-toggle="buttons" >
						<label class="btn btn-default">
							<input type="radio" name="status" value="" autocomplete="off" 
								<?php echo !isset($data_search['status']) || $data_search['status'] === "" ? 'checked="checked"' : ''; ?>>
							<?php echo __('all'); ?>
						</label>
						<label class="btn btn-default">
							<input type="radio" name="status" value="1" autocomplete="off" 
								<?php echo isset($data_search['status']) && $data_search['status']  === "1" ? 'checked="checked"' : '';?> >
							<?php echo __('yes'); ?>
						</label>
						<label class="btn btn-default">
							<input type="radio" name="status" value="0" autocomplete="off" 
								<?php echo isset($data_search['status']) && $data_search['status'] === "0" ? 'checked="checked"' : ''; ?> >
								<?php echo __('no'); ?>
						</label>
					</div>
				</div>
            </div>

            <div class="row" style="margin-top: 20px">
                <div class="col-md-12 ">
                    <div class="d-flex justify-content-end">
                        <?php
                            echo $this->Form->submit(__('submit'), array(
                                'class' => 'btn btn-primary',
                            ));

                            echo "&nbsp;";

                            echo  $this->Html->link(__('reset'), array(
                                'controller' => 'companies',
                                'prefix' => 'Admin',    // viet hoa T_T
                                'action' => 'index',
                               
                            ), array(
                                'class' => 'btn btn-danger filter-button'    // add class to link
                            ));


                            echo "&nbsp;";

                            // echo $this->Form->control(__('export'), array(
                            //     'div' => false,
                            //     'label' => false,
                            //     'type' => 'submit',
                            //     'name' => 'button[export]',
                            //     'class' => 'btn btn-success filter-button',
                            // ));     
                        
                            // echo "&nbsp;"; 
                            // echo $this->Form->control(__('export_excel'), array(
                            //     'div' => false,
                            //     'label' => false,
                            //     'type' => 'submit',
                            //     'name' => 'button[exportExcel]',
                            //     'class' => 'btn btn-warning filter-button',
                            // ));            
                            
                        ?>
                    </div>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
		</div>

	</div>
</div>

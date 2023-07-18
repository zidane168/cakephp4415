<div class="row filter-panel">
	<div class="col-md-12">
		<?php 
			echo $this->Form->create(NULL, array(
                $this->Url->build(['controller' => 'permissions', 'action' => 'index', 'admin' => true]), 
                'class' => 'form_filter',
                'type' => 'get',
			));
        ?>
 
		<div class="action-buttons-wrapper">
			<div class="row">
                <div class="col-md-3">
                    <?php 
                        echo $this->Form->control('p_controller', array(
                            'class' => 'form-control',
                            'label' => __d('permission','p_controller'),
                            'value' => isset($data_search['p_controller']) && !empty($data_search['p_controller']) ? $data_search['p_controller'] : '', 
                        )); 
                    ?>
                </div>
              
                <div class="col-md-3">
                    <?php 
                        echo $this->Form->control('p_model', array(
                            'class' => 'form-control',
                            'label' => __d('permission','p_model'),
                            'value' => isset($data_search['p_model']) && !empty($data_search['p_model']) ? $data_search['p_model'] : '', 
                        )); 
                    ?>
                </div>
            
                <div class="col-md-3">
                    <?php 
                        echo $this->Form->control('name', array(
                            'class' => 'form-control',
                            'label' => __('name'),
                            'value' => isset($data_search['name']) && !empty($data_search['name']) ? $data_search['name'] : '', 
                        )); 
                    ?>
                </div>

                <div class="col-md-3">
                    <?php 
                        echo $this->Form->control('slug', array(
                            'class' => 'form-control',
                            'label' => __('slug'),
                            'value' => isset($data_search['slug']) && !empty($data_search['slug']) ? $data_search['slug'] : '', 
                        )); 
                    ?>
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
                                'controller' => 'permissions',
                                'prefix' => 'Admin',    // viet hoa T_T
                                'action' => 'index',
                               
                            ), array(
                                'class' => 'btn btn-danger filter-button'    // add class to link
                            ));


                            echo "&nbsp;";

                            echo $this->Form->control(__('export'), array(
                                'div' => false,
                                'label' => false,
                                'type' => 'submit',
                                'name' => 'button[export]',
                                'class' => 'btn btn-success filter-button',
                            ));     
                        
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

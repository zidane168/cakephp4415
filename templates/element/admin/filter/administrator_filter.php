<div class="row filter-panel">
	<div class="col-md-12">
		<?php 
			echo $this->Form->create(NULL, array(
                $this->Url->build(['controller' => 'administrators', 'action' => 'index', 'admin' => true]), 
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
                        echo $this->Form->control('role_id', array(
                            'class' => 'form-control',
                            'empty' => __("please_select"),
                            'options' => $roles,
                            'value' =>  isset($data_search['role_id']) && !empty($data_search['role_id']) ? $data_search['role_id'] : '',
                            'label' => __d('role', 'role'),
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
                                'controller' => 'administrators',
                                'prefix' => 'Admin',    // viet hoa T_T
                                'action' => 'index',
                               
                            ), array(
                                'class' => 'btn btn-danger filter-button'    // add class to link
                            ));

                            echo $this->element('admin/filter/common/export_info');    
                            
                        ?>
                    </div>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
		</div>

	</div>
</div>

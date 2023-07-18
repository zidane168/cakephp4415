<div class="row filter-panel">
    <div class="col-md-12">
        <?php
        echo $this->Form->create(NULL, array(
            $this->Url->build(['controller' => 'Centers', 'action' => 'index', 'admin' => true]),
            'class' => 'form_filter',
            'type' => 'get',
        ));
        ?>

        <div class="action-buttons-wrapper">
            <div class="row">
                <div class="col-md-4">
                    <?php
                    echo $this->Form->control('order_number', array(
                        'class' => 'form-control',
                        'label' => __('name'),
                        'value' => isset($data_search['order_number']) && !empty($data_search['order_number']) ? $data_search['order_number'] : '',
                    ));
                    ?>
                </div>
                <div class="col-md-4">
                    <?php echo $this->cell('OrderStatus', [ 
                        'status' => isset($data_search['status']) ? $data_search['status'] : null,  // must correct position cell param on cell
                        'is_required' => false, 
                    ]);?>
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
                            'controller' => 'Centers',
                            'prefix' => 'Admin',    // viet hoa T_T
                            'action' => 'index',

                        ), array(
                            'class' => 'btn btn-danger filter-button'    // add class to link
                        )); 

                        ?>
                    </div>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>

    </div>
</div>
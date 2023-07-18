<div class="row filter-panel">
    <div class="col-md-12">
        <?php
        echo $this->Form->create(NULL, array(
            $this->Url->build(['controller' => 'RescheduleHistories', 'action' => 'index', 'admin' => true]),
            'class' => 'form_filter',
            'type' => 'get',
        ));
        ?>

        <div class="action-buttons-wrapper">
            <div class="row">
                <div class="col-md-4">
                    <?php
                    echo $this->Form->control('from_cidc_class_id', [
                        'empty' => __('please_select'),
                        'data-live-search' => true,
                        'class' => 'selectpicker form-control',
                        'options' => $cidcClasses,
                        'label' =>  __d('cidcclass', 'from_cidc_class'),
                        'value' => isset($data_search['from_cidc_class_id']) && !empty($data_search['from_cidc_class_id']) ? $data_search['from_cidc_class_id'] : array()

                    ]);
                    ?>
                </div>

                <div class="col-md-4">
                    <?php
                    echo $this->Form->control('to_cidc_class_id', [
                        'empty' => __('please_select'),
                        'data-live-search' => true,
                        'class' => 'selectpicker form-control',
                        'options' => $cidcClasses,
                        'label' =>  __d('cidcclass', 'to_cidc_class'),
                        'value' => isset($data_search['to_cidc_class_id']) && !empty($data_search['to_cidc_class_id']) ? $data_search['to_cidc_class_id'] : array()

                    ]);
                    ?>
                </div>

                <div class="col-md-4">
                    <div><label><?php echo __('enabled'); ?></label></div>
                    <div class="btn-group btn-group-sm" data-toggle="buttons">
                        <label class="btn btn-default">
                            <input type="radio" name="status" value="" autocomplete="off" <?php echo !isset($data_search['status']) || $data_search['status'] === "" ? 'checked="checked"' : ''; ?>>
                            <?php echo __('all'); ?>
                        </label>
                        <label class="btn btn-default">
                            <input type="radio" name="status" value="1" autocomplete="off" <?php echo isset($data_search['status']) && $data_search['status']  === "1" ? 'checked="checked"' : ''; ?>>
                            <?php echo __('yes'); ?>
                        </label>
                        <label class="btn btn-default">
                            <input type="radio" name="status" value="0" autocomplete="off" <?php echo isset($data_search['status']) && $data_search['status'] === "0" ? 'checked="checked"' : ''; ?>>
                            <?php echo __('no'); ?>
                        </label>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-4">
                    <?php
                    echo $this->Form->control('kid_id', [
                        'empty' => __('please_select'),
                        'data-live-search' => true,
                        'class' => 'selectpicker form-control',
                        'options' => $kids,
                        'label' => __d('parent', 'kid'),
                        'value' => isset($data_search['kid_id']) && !empty($data_search['kid_id']) ? $data_search['kid_id'] : array()

                    ]); ?>
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
                            'controller' => 'RescheduleHistories',
                            'prefix' => 'Admin',
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
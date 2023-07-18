<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $rescheduleHistory
 */
?>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <?= __d('staff', 'edit_reschedule_history'); ?> </h3>
                </div>

                <div class="card-body">
                    <?= $this->Form->create($rescheduleHistory, ['type' => 'file']) ?>
                    <fieldset>
                        <div class="row">
                            <div class="col-12">
                                <?php
                                echo $this->Form->control('kid_id', [
                                    'empty' => __('please_select'),
                                    'required' => true,
                                    'id'    => 'kid_id',
                                    'escape' => false,
                                    'disabled' => 'disabled',
                                    'data-live-search' => true,
                                    'options' => $kids,
                                    'class' => 'selectpicker form-control',
                                    'label' => "<font class='red'> * </font>" . __d('parent', 'kid'),
                                ]);
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('from_cidc_class_id', [
                                    'empty' => __('please_select'),
                                    'required' => true,
                                    'escape' => false,
                                    'id' => 'from_cidc_class_id',
                                    'disabled' => 'disabled',
                                    'data-live-search' => true,
                                    'class' => 'selectpicker form-control',
                                    'options' => $cidcClasses,
                                    'label' => "<font class='red'> * </font>" . __d('cidcclass', 'from_cidc_class'),
                                ]); ?>
                            </div>

                            <div class="col-6">
                                <?php
                                echo $this->Form->control('to_cidc_class_id', [
                                    'empty' => __('please_select'),
                                    'required' => true,
                                    'escape' => false,
                                    'id' => 'to_cidc_class_id',
                                    'disabled' => 'disabled',
                                    'data-live-search' => true,
                                    'class' => 'selectpicker form-control',
                                    'options' => $cidcClasses,
                                    'label' => "<font class='red'> * </font>" . __d('cidcclass', 'to_cidc_class'),
                                ]);
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div id="from_cidc_class_info" class="cidc_class_info"></div>

                            </div>

                            <div class=" col-6">
                                <div id="to_cidc_class_info" class="cidc_class_info"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('from_date_id', [
                                    'escape' => false,
                                    'id' => 'from_date_id',
                                    'label'    => __d('cidcclass', 'date_from'),
                                    'class' => 'selectpicker form-control',
                                    'disabled' => 'disabled',
                                    'value' => 0,
                                    'options' => $rescheduleHistory->date_from ? [$rescheduleHistory->date_from->format('Y-m-d')] : null,
                                    'empty' => __('please_select')
                                ]);
                                ?>
                            </div>

                            <div class="col-6">
                                <?php
                                echo $this->Form->control('to_date_id', [
                                    'escape' => false,
                                    'id' => 'to_date_id',
                                    'label'    => __d('cidcclass', 'date_to'),
                                    'value' => 0,
                                    'disabled' => 'disabled',
                                    'options' => $rescheduleHistory->date_to ? [$rescheduleHistory->date_to->format('Y-m-d')] : null,
                                    'class' => 'selectpicker form-control',
                                    'empty' => __('please_select')
                                ]);
                                ?>
                            </div>
                        </div>
                        <?php
                        echo $this->Form->control('reason', [
                            'class' => 'form-control',
                            'disabled' => 'disabled',
                            'label' => __d('staff', 'reason')
                        ]);
                        echo $this->Form->control('status', [
                            'empty' => __('please_select'),
                            'required' => true,
                            'escape' => false,
                            'id' => 'status',
                            'data-live-search' => true,
                            'class' => 'selectpicker form-control',
                            'options' => $statuses,
                            'label' => "<font class='red'> * </font>" . __d('cidcclass', 'status'),
                        ]);
                        echo $this->element('multi_images_upload_container_edit', array(
                            'images_model' => $images_model,
                            'accept_file' => array(
                                '.docx', '.doc', '.png', '.jpeg', '.pdf', '.jpg'
                            ),
                        ));
                        ?>


                        <div class="row mt-10">
                            <div class="col-2">
                                <?= $this->Form->button(__('submit'), ['class' => 'btn btn-large btn-primary form-control']) ?>
                            </div>
                        </div>
                    </fieldset>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
echo $this->Html->script('CakeAdminLTE/pages/admin_reschedule', array('inline' => false));
?>
<script type="text/javascript">
    $(document).ready(function() {
        ADMIN_RESCHEDULE.url_get_date_by_class_id = '<?= $this->Url->build(['prefix' => 'Api/V1',  'controller' => 'CidcClasses', 'action' => 'getListDateByClass']);   ?>';
        ADMIN_RESCHEDULE.url_get_detail_class_by_id = '<?= $this->Url->build(['prefix' => 'Api/V1',  'controller' => 'CidcClasses', 'action' => 'getDetailClassByIdUI']);   ?>';
        ADMIN_RESCHEDULE.url_get_register_classes_by_kid_id = '<?= $this->Url->build(['prefix' => 'Api/V1',  'controller' => 'CidcClasses', 'action' => 'getClassesByKidIdUI']);   ?>';
        ADMIN_RESCHEDULE.url_get_class_by_from_class_id = '<?= $this->Url->build(['prefix' => 'Api/V1',  'controller' => 'CidcClasses', 'action' => 'getClassesByFromClasssIdUI']);   ?>';
        ADMIN_RESCHEDULE.current_language = '<?= $current_language; ?>';
        ADMIN_RESCHEDULE.edit_page();
    });
</script>
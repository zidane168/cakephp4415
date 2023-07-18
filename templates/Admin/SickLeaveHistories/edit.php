<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $sickLeaveHistory
 */

use App\Model\Entity\SickLeaveHistory;

?>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <?= __('Sick Leave Histories'); ?> </h3>
                </div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($sickLeaveHistory, ['type' => 'file']) ?>
                    <fieldset>

                        <div class="row">
                            <div class="col-6">
                                <?php

                                echo $this->Form->control('cidc_class_id', [
                                    'empty' => __('please_select'),
                                    'id' => 'cidc_class_id',
                                    'required' => true,
                                    'escape' => false,
                                    'data-live-search' => true,
                                    'class' => 'selectpicker form-control',
                                    'options' => $cidcClasses,
                                    'label' => "<font class='red'> * </font>" . __d('center', 'cidc_class'),
                                ]);
                                ?>
                            </div>
                            <div class="col-6">
                                <div id="cidc_class_info" class="cidc_class_info">
                                </div>
                            </div>
                        </div>
                        <?php
                        echo $this->Form->control('kid_id', [
                            'empty' => __('please_select'),
                            'required' => true,
                            'id'    => 'kid_id',
                            'escape' => false,
                            'data-live-search' => true,
                            'options' => $kids,
                            'class' => 'selectpicker form-control',
                            'label' => "<font class='red'> * </font>" . __d('parent', 'kid'),
                        ]);
                        ?>
                        <div class="row">
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('date_id', [
                                    'escape' => false,
                                    'id' => 'date_id',
                                    'label'    => __('date'),
                                    'class' => 'form-control',
                                    'value' => 0,
                                    'options' => [$sickLeaveHistory->date->format('Y-m-d')],
                                    'empty' => __('please_select')
                                ]); ?>
                            </div>
                            <div class="col-6">
                                <?php
                                echo $this->element('datetime_picker', array(
                                    'id'                => 'time',
                                    'field_name'        => 'time',
                                    'placeholder'       => __('time'),
                                    'label'             => __('time'),
                                    'class'             => 'time',
                                    'required'          => 'required',
                                    'format'            => 'HH:mm',
                                    'value'             => $sickLeaveHistory->time->format('H:i')
                                ));
                                ?>
                            </div>

                        </div>

                        <?php
                        echo $this->Form->control('reason', [
                            'class' => 'form-control',
                            'label' => __d('staff', 'reason')
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
echo $this->Html->script('CakeAdminLTE/pages/admin_sick_leave', array('inline' => false));
?>
<script type="text/javascript">
    $(document).ready(function() {
        ADMIN_SICK_LEAVE.url_get_date_by_class_id = '<?= $this->Url->build(['prefix' => 'Api/V1',  'controller' => 'CidcClasses', 'action' => 'getListDateByClass']);   ?>';
        ADMIN_SICK_LEAVE.url_get_kids_register_by_class_id = '<?= $this->Url->build(['prefix' => 'Api/V1',  'controller' => 'CidcClasses', 'action' => 'getKidsRegisterClassUI']);   ?>';
        ADMIN_SICK_LEAVE.url_get_detail_class_by_id = '<?= $this->Url->build(['prefix' => 'Api/V1',  'controller' => 'CidcClasses', 'action' => 'getDetailClassByIdUI']);   ?>';
        ADMIN_SICK_LEAVE.current_language = '<?= $current_language; ?>';
        ADMIN_SICK_LEAVE.edit_init();
    });
</script>
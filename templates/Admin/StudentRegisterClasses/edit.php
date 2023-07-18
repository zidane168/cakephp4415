<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\StudentRegisterClass $studentRegisterClass
 */
?>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <?= __d('cidcclass', 'edit_student_register_class'); ?> </h3>
                </div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($studentRegisterClass, ['type' => 'file']) ?>
                    <fieldset>

                        <div class="row">
                            <div class="col-12">
                                <?php
                                echo $this->Form->control('cidc_class_id', [
                                    'empty' => __('please_select'),
                                    'required' => true,
                                    'id'        => 'cidc_class_id',
                                    'escape' => false,
                                    'data-live-search' => true,
                                    'class' => 'selectpicker form-control',
                                    'options' => $cidcClasses,
                                    'label' => "<font class='red'> * </font>" . __d('center', 'cidc_class'),
                                ]);
                                ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
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
                            'options' => $kids,
                            'data-live-search' => true,
                            'class' => 'selectpicker form-control',
                            'label' => "<font class='red'> * </font>" . __d('parent', 'kid'),
                        ]);
                        echo $this->Form->control('status', [
                            'empty' => __('please_select'),
                            'required' => true,
                            'escape' => false,
                            'data-live-search' => true,
                            'class' => 'selectpicker form-control',
                            'options' => $statuses,
                            'label' => "<font class='red'> * </font>" . __d('cidcclass', 'status'),
                        ]);
                        echo $this->element('multi_images_upload_container_edit', array(
                            'images_model' => $images_model,
                            'accept_file' => array(
                                '.pdf', '.png', '.jpeg', '.jpg'
                            ),
                        ));
                        ?>

                        <div class="row mt-10">
                            <div class="col-2">
                                <?= $this->Form->button(__('submit'), [
                                    'id' => 'submit',
                                    'class' => 'btn btn-large btn-primary form-control']) ?>
                            </div>
                        </div>
                    </fieldset>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->Html->script('CakeAdminLTE/pages/admin_student_register_class.js?v=' . date('U'), array('inline' => false)); ?>
<script src="https://cdn.socket.io/4.6.1/socket.io.min.js" > </script>

<script>
    $(document).ready(function() {
        ADMIN_STUDENT_REGISTER_CLASS.url_get_detail_class_by_id = '<?= $this->Url->build(['prefix' => 'Api/V1',  'controller' => 'CidcClasses', 'action' => 'getDetailClassByIdUI']);   ?>';
        ADMIN_STUDENT_REGISTER_CLASS.url_get_list_kids_no_register_class = '<?= $this->Url->build(['prefix' => 'Api/V1',  'controller' => 'CidcClasses', 'action' => 'getKidsNoRegisterClassUI']);   ?>';
        ADMIN_STUDENT_REGISTER_CLASS.current_language = '<?= $current_language ?>'
        ADMIN_STUDENT_REGISTER_CLASS.init();
    });

    const socket = io('<?= $socket_server_url ?>');
    socket.on('connect', function() {
        console.log('Connected');  
            
        socket.on('disconnect', function() {
            console.log('Disconnected');
        });

        const btnSubmit = document.getElementById('submit');
        btnSubmit.addEventListener('click', function() {
 
            let kidId = $('#kid_id').val()
            let status = $('#status').val()
 
            if (status == 1) {  // PAID  
                socket.emit('infoNewNotification', {  kidId: kidId, numberMessage: 1 }, () => { })
            }
        })

    });
</script>
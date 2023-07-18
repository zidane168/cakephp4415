<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $emergencyContact
 */
?>

<div class="container-fluid card full-border">


    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __('Emergency Contacts'); ?> </h3>

                    <?php
                    if (isset($permissions['Emergency Contacts']['edit']) && ($permissions['Emergency Contacts']['edit'] == true)) {
                    ?>

                        <div class="box-tools ml-auto p-1">
                            <?php echo $this->Html->link('<i class="fa fa-pencil"></i> ' . __('edit'), array('action' => 'edit', $emergencyContact->id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                        </div>

                    <?php
                    }
                    ?>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th><?= __('Id') ?></th>
                            <td><?= $this->Number->format($emergencyContact->id) ?></td>
                        </tr>

                        <tr>
                            <th><?= __d('parent', 'phone_number') ?></th>
                            <td><?= h($emergencyContact->phone_number) ?></td>
                        </tr>


                        <?php
                        echo $this->element('admin/filter/common/enabled_info', array(
                            'object' => $emergencyContact,
                        ));
                        echo $this->element('admin/filter/common/admin_info', array(
                            'object' => $emergencyContact,
                        ));
                        ?>
                    </table>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin-top-15">
                                <?= $this->element('content_view', array(
                                    'languages'             => $languages,
                                    'language_input_fields' => $language_input_fields,
                                    //  'images' 				=> $images,
                                )); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
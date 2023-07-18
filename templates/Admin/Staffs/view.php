<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Staff $staff
 */
?>

<div class="container-fluid card full-border">


    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __d('staff', 'staff'); ?> </h3>

                    <?php
                    if (isset($permissions['Staffs']['edit']) && ($permissions['Staffs']['edit'] == true)) {
                    ?>

                        <div class="p-1 ml-auto box-tools">
                            <?php echo $this->Html->link('<i class="fa fa-pencil"></i> ' . __('edit'), array('action' => 'edit', $staff->id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                        </div>

                    <?php
                    }
                    ?>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th><?= __d('parent', 'phone_number') ?></th>
                            <td><?= $this->Com->formatPhoneNumber($staff->user->phone_number) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('email') ?></th>
                            <td><?= h($staff->user->email) ?></td>
                        </tr>

                        <tr>
                            <th><?= __d('parent', 'gender') ?></th>
                            <td><?= h($genders[$staff->gender]) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('enabled') ?></th>
                            <td>
                                <?= $this->element('view_check_ico', array('_check' => $staff->user->enabled)) ?>
                            </td>
                        </tr>

                        <?php

                        echo $this->element('admin/filter/common/admin_info', array(
                            'object' => $staff,
                        ));
                        ?>
                    </table>


                </div>
            </div>
        </div>
    </div>

</div>
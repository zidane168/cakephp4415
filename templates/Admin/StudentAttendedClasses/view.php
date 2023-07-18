<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\StudentAttendedClass $studentAttendedClass
 */
?>

<div class="container-fluid card full-border">


<div class="row">
    <div class="col-12">
        <div class="box box-primary">
            <div class="box-header d-flex">
                <h3 class="box-title"> <?php echo __('Student Attended Classes'); ?> </h3>

	            <?php 
            		if (isset($permissions['Student Attended Classes']['edit']) && ($permissions['Student Attended Classes']['edit'] == true)) { 
				?>

				<div class="box-tools ml-auto p-1">
	                <?php echo $this->Html->link('<i class="fa fa-pencil"></i> ' . __('edit'), array('action' => 'edit', $studentAttendedClass->id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
	            </div>
                
                <?php 
                    }
				?>
			</div>

	        <div class="box-body">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th><?= __('Cidc Class') ?></th>
                        <td><?= $studentAttendedClass->has('cidc_class') ? $this->Html->link($studentAttendedClass->cidc_class->name, ['controller' => 'CidcClasses', 'action' => 'view', $studentAttendedClass->cidc_class->id]) : '' ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Kid') ?></th>
                        <td><?= $studentAttendedClass->has('kid') ? $this->Html->link($studentAttendedClass->kid->id, ['controller' => 'Kids', 'action' => 'view', $studentAttendedClass->kid->id]) : '' ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Created By') ?></th>
                        <td><?= $studentAttendedClass->has('created_by') ? $this->Html->link($studentAttendedClass->created_by->name, ['controller' => 'Administrators', 'action' => 'view', $studentAttendedClass->created_by->id]) : '' ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Modified By') ?></th>
                        <td><?= $studentAttendedClass->has('modified_by') ? $this->Html->link($studentAttendedClass->modified_by->name, ['controller' => 'Administrators', 'action' => 'view', $studentAttendedClass->modified_by->id]) : '' ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Id') ?></th>
                        <td><?= $this->Number->format($studentAttendedClass->id) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Status') ?></th>
                        <td><?= $this->Number->format($studentAttendedClass->status) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Date') ?></th>
                        <td><?= h($studentAttendedClass->date) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Created') ?></th>
                        <td><?= h($studentAttendedClass->created) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Modified') ?></th>
                        <td><?= h($studentAttendedClass->modified) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Enabled') ?></th>
                        <td><?= $studentAttendedClass->enabled ? __('Yes') : __('No'); ?></td>
                    </tr>

                    <?php
                    echo $this->element('admin/filter/common/enabled_info', array(
                        'object' => $region,
                    ));
                    echo $this->element('admin/filter/common/admin_info', array(
                        'object' => $region,
                    ));
                    ?>
                </table>


                </div>
            </div>
        </div>
    </div>

</div>


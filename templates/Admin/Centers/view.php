<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Center $center
 */
?>

<div class="container-fluid card full-border">


<div class="row">
    <div class="col-12">
        <div class="box box-primary">
            <div class="box-header d-flex">
                <h3 class="box-title"> <?php echo __d('center', 'center'); ?> </h3>

	            <?php 
            		if (isset($permissions['Centers']['edit']) && ($permissions['Centers']['edit'] == true)) { 
				?>

				<div class="p-1 ml-auto box-tools">
	                <?php echo $this->Html->link('<i class="fa fa-pencil"></i> ' . __('edit'), array('action' => 'edit', $center->id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
	            </div>
                
                <?php 
                    }
				?>
			</div>

	        <div class="box-body">
                <table class="table table-bordered table-striped">
                <tr>
                        <th><?= __('id') ?></th>
                        <td><?= $this->Number->format($center->id) ?></td>
                    </tr>
                    <tr>
                        <th><?= __d('center','code') ?></th>
                        <td><?= h($center->code) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('account') ?></th>
                        <td><?= h($center->account) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('username') ?></th>
                        <td><?= h($center->username) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('bank_name') ?></th>
                        <td><?= h($center->bank_name) ?></td>
                    </tr>
                    <tr>
                        <th><?= __d('center', 'phone_us') ?></th>
                        <td><?= h($center->phone_us) ?></td>
                    </tr>
                    <tr>
                        <th><?= __d('center', 'fax_us') ?></th>
                        <td><?= h($center->fax_us) ?></td>
                    </tr>
                    <tr>
                        <th><?= __d('center', 'visit_us') ?></th>
                        <td><?= h($center->visit_us) ?></td>
                    </tr>
                    <tr>
                        <th><?=  __d('center', 'mail_us') ?></th>
                        <td><?= h($center->mail_us) ?></td>
                    </tr>
                    <tr>
                        <th><?= __d('setting', 'district') ?></th>
                        <td><?= $center->has('district') ? $this->Html->link($center->district->district_languages[0]->name, ['controller' => 'Districts', 'action' => 'view', $center->district->id]) : '' ?></td>
                    </tr>
                    
                    <tr>
                        <th><?= __('latitude') ?></th>
                        <td><?= $this->Number->format($center->latitude) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('longitude') ?></th>
                        <td><?= $this->Number->format($center->longitude) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('sort') ?></th>
                        <td><?= $this->Number->format($center->sort) ?></td>
                    </tr>

                    <?php
                    echo $this->element('admin/filter/common/enabled_info', array(
                        'object' => $center,
                    ));
                    echo $this->element('admin/filter/common/admin_info', array(
                        'object' => $center,
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


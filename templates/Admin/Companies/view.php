<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Company $company
 */
?>


<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"><?= __d('company', 'company'); ?></h3>
                
                    <?php if (isset($permissions['Companies']['add']) && ($permissions['Companies']['add'] == true)) { ?>
                        
                        <div class="box-tools ml-auto p-1">
                            <?php echo $this->Html->link('<i class="fa fa-pencil"></i> '.__('edit'), array('action' => 'edit', $company->id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
	                    </div>    
                    <?php  }  ?>
                </div>
            </div>

            <div class="box box-body">
                <table id="Companies" class="table table-bordered table-striped">

                    <tr>
                        <th><?= __('id') ?></th>
                        <td><?= $this->Number->format($company->id) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('email') ?></th>
                        <td><?= h($company->email) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Facebook') ?></th>
                        <td><?= h($company->facebook) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Instagram') ?></th>
                        <td><?= h($company->instagram) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Twitter') ?></th>
                        <td><?= h($company->twitter) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Website') ?></th>
                        <td><?= h($company->website) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Weixin') ?></th>
                        <td><?= h($company->weixin) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Wechat') ?></th>
                        <td><?= h($company->wechat) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Weibo') ?></th>
                        <td><?= h($company->weibo) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Whatsapp') ?></th>
                        <td><?= h($company->whatsapp) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Youtube') ?></th>
                        <td><?= h($company->youtube) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Contact Person') ?></th>
                        <td><?= h($company->contact_person) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Contact Job Title') ?></th>
                        <td><?= h($company->contact_job_title) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Contact Email') ?></th>
                        <td><?= h($company->contact_email) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Contact Phone') ?></th>
                        <td><?= h($company->contact_phone) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('modified_by') ?></th>
                        <td>
                            <?= $company->modified_by ? h($company->modified_by['name']) : '' ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?= __('created_by') ?></th>
                        <td>
                            <?= $company->created_by ? h($company->created_by['name']) : '' ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?= __('modified') ?></th>
                        <td><?= $this->element('customize_format_datetime', ['date' => $company->modified] ) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('created') ?></th>
                        <td><?= $this->element('customize_format_datetime', ['date' => $company->created] ) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('enabled') ?></th>
                        <td><?= $company->enabled ? __('Yes') : __('No'); ?></td>
                    </tr>
                </table>

                <div class="row">
					<div class="col-12">
						<?php echo $this->element('content_view',array(
							'languages'             => $languages,
							'language_input_fields' => $language_input_fields,
						)); ?>
					</div>
				</div> <!-- end row -->
            </div>
        </div>
    </div>
</div>

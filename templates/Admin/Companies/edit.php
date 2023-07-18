<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Company $company
 */
?>

<div class="container-fluid">

	<div class="row">
		<div class="col-12">
			<div class="card card-primary">
				<div class="card-header">
					<h3 class="card-title"> <?=__d('company', 'edit_company'); ?> </h3>
				</div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($company) ?>
                    <fieldset>
                        <legend><?= __('Edit Company') ?></legend>
                        <?php
                            echo $this->Form->control('email', ['class' => 'form-control']);
                            echo $this->Form->control('facebook', ['class' => 'form-control']);
                            echo $this->Form->control('instagram', ['class' => 'form-control']);
                            echo $this->Form->control('twitter', ['class' => 'form-control']);
                            echo $this->Form->control('website', ['class' => 'form-control']);
                            echo $this->Form->control('weixin', ['class' => 'form-control']);
                            echo $this->Form->control('wechat', ['class' => 'form-control']);
                            echo $this->Form->control('weibo', ['class' => 'form-control']);
                            echo $this->Form->control('whatsapp', ['class' => 'form-control']);
                            echo $this->Form->control('youtube', ['class' => 'form-control']);
                            echo $this->Form->control('remark', ['class' => 'form-control']);
                            echo $this->Form->control('contact_person', ['class' => 'form-control']);
                            echo $this->Form->control('contact_job_title', ['class' => 'form-control']);
                            echo $this->Form->control('contact_email', ['class' => 'form-control']);
                            echo $this->Form->control('contact_phone', ['class' => 'form-control']);
                            echo $this->Form->control('enabled', ['class' => '']);
                        ?>

                        <?php 
                            echo $this->element('language_input_column', array(
                                'languages_model'           => $languages_model,
                                'languages_edit_model'      => $languages_edit_model,
                                'languages_list'            => $languages_list,
                                'language_input_fields'     => $language_input_fields,
                                'languages_edit_data'       => $languages_edit_data, 
                            )); 
                        ?>

                        <div class="row mt-10">
                            <div class="col-2">
                                <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-large btn-primary form-control']) ?>
                            </div>
                        </div>
                </fieldset>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>


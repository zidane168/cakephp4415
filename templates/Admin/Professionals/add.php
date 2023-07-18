<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Professional $professional
 */
?>

<div class="container-fluid">
	<?= $this->Form->create($professional, ['type' => 'file']) ?>
	<fieldset>

		<div class="row">
			<div class="col-12">
				<div class="card card-primary">
					<div class="card-header">
						<h3 class="card-title">
							<?= __d('professional', 'add_professional'); ?>
						</h3>
					</div>

					<div class="card-body table-responsive">

						<?php
						echo $this->Form->control('gender', [
							'empty' => __('please_select'),
							'required'  => true,
							'escape'  => false,
							'class'   => 'selectpicker form-control',
							'options' => $genders,
							'label'   => "<font class='red'> * </font>" . __d('parent', 'gender'),
						]);

						echo $this->Form->control('type', [
							'empty' => __('please_select'),
							'required'  => true,
							'escape'  => false,
							'value' => 1,
							'class'   => 'selectpicker form-control',
							'options' => $types,
							'label'   => "<font class='red'> * </font>" . __d('professional', 'type'),
						]);

						echo "<br />";
						echo $this->element('language_input_column', array(
							'languages_model'       => $languages_model,
							'languages_list'        => $languages_list,
							'language_input_fields' => $language_input_fields,
							'languages_edit_data'   => false
						));

						?>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-12">
				<div class="card card-primary">
					<div class="card-header">
						<h3 class="card-title">
							<?= __d('professional', 'certification_for_teacher'); ?>
						</h3>
					</div> 
					<div class="card-body table-responsive">
						<?php
						echo $this->element('images_upload_no_type', array(
							'add_new_images_url'    => $add_new_images_url,
							'images_model'          => $images_model,
							'base_model'            => $model,
							'total_image'           => 1,
							'label'					=> __d('professional', 'add_certification'),
						));

						echo $this->element('multi_language_input_column', array(
							'languages_model'        => $languages_model_cerification,
							'languages_list'         => $languages_list,
							'language_input_fields'  => $language_input_fields_certification,
							'languages_edit_data'    => false,
							'add_language_input_url' => $add_language_input_url,
							'total_item'			=> 5
						));

						?>
						<input type="hidden" value="1" name="numberElementCertification" />

						<div class="mt-10 row">
							<div class="col-2">
								<?= $this->Form->button(__('submit'), ['class' => 'btn btn-large btn-primary form-control']) ?>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>

	</fieldset>
	<?= $this->Form->end() ?>
</div>
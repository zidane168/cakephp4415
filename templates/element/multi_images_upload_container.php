<?php

use Cake\Routing\Router;

// ------------ Multiple images upload file ------------
// By: Vi.LH (Add)
// ------------ Multiple images upload file ------------

$limit_size = 3;
?>

<div class="row mbt-30 grb-dropfile">
	<div class="col-12">
		<div class="row m-0">
			<div class="col-md-6 p-0">
				<?= __('upload_file') ?>
			</div>
			<div class="col-md-6 p-0 text-right text-grey">
				<?php
				$default_accept_file = array(
					".jpg", ".jpeg", ".png",
				);
				echo isset($accept_file) && !empty($accept_file) ? implode(", ", $accept_file) : implode(", ", $default_accept_file);
				?>
			</div>
		</div>
		<div class="row m-0">
			<div class="col-md-12 p-0 import-file-area pointer">
				<p class="text-grey-light"><span class="fa fa-cloud-upload img-upload-margin"></span><?= __('upload_file_or_drag_it_here') ?></p>
				<p class="text-grey-light"> (<?= __('less_than_x_mb_per_file', $limit_size) ?>) </p>
			</div>

			<?php
			echo $this->Form->input($images_model . '..image', array(
				'div' 		=> 'col-md-6',
				'type' 		=> 'file',
				'class'		=> 'hidden',
				'accept' 	=> isset($accept_file) && !empty($accept_file)  ? implode(", ", $accept_file) : implode(", ", $default_accept_file),
				'label' 	=> false,
				'id' 		=> "upload-material",
				'multiple' 	=> 'multiple',
			));
			?>

			<div class="col-md-12 p-0 ">
				<ul class="lst-imported-file" id="lst-imported-material"> </ul>
			</div>
		</div>
	</div>
</div>

<?= $this->Html->script('CakeAdminLTE/pages/multi_images_upload_container.js?v=' . date('U'), array('inline' => false)); ?>

<script>
	$(document).ready(function() {
		MULTI_IMAGE_UPLOADS.limit_size = <?php echo $limit_size; ?>;
		MULTI_IMAGE_UPLOADS.lst_material = [];
		MULTI_IMAGE_UPLOADS.lst_material_extension_allowed = <?= isset($accept_file) && !empty($accept_file) ?  json_encode($accept_file) : json_encode($default_accept_file) ?>;
		MULTI_IMAGE_UPLOADS.common();
		MULTI_IMAGE_UPLOADS.add();

	});
</script>
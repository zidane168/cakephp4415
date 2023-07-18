<div class="form-group images-upload ">

	<?php
	if (!empty($images_edit_data)) {
		foreach ($images_edit_data as $key => $image) :
			if ($image->has('path')) {
	?>
				<div class="well well-sm">
					<div class="row images-upload-row">

						<div class="col-md-7">
							<?php
							if ($image->has('image_type_id')) {
								echo isset($imageTypes[$image->image_type_id]) ? $imageTypes[$image->image_type_id] : "";
							}
							?>
						</div>

						<div class="col-md-4">
							<?php
							// $image['path']
							// $this->Url->image('cover.jpg'); /domain/webroot/img/cover.jpg

							?>
							<img src="<?php echo $this->Url->build('/') . ($image['path']); ?>" alt="img" class='img-thumbnail preview max-width-image-upload' />
						</div>

						<?php
						if (!isset($can_remove) || (isset($can_remove) && $can_remove)) { ?>
							<div class="col-md-1 images-buttons text-right">
								<?php
								print $this->Html->link('<i class="far fa-times-circle"></i>', '#', array(
									'class' => 'btn-remove-uploaded-image',
									'data-image-id' => $image['id'],
									'escape' => false
								));
								?>
							</div>
						<?php }
						?>
					</div>
				</div>
	<?php
			}
		endforeach;
	}
	?>

	<?php
	if (strpos($this->request->getParam('action'), 'edit') === false) : ?>
		<!-- add function -->
		<div class="well well-sm">
			<div class="row images-upload-row">

				<div class="col-md-7">
					<?php
					echo $this->Form->control($images_model . '..image_type_id', array(
						'class' => 'form-control image-type',
						'empty' => __("please_select"),
						'label' => __("image_type"),
					));
					?>
				</div>

				<div class="col-md-4">
					<?php
					echo $this->Form->control($images_model . '..image', array(
						'type' => 'file',
						'accept' => "image/*",
						'label' => __("image")
					));
					?>
				</div>

				<div class="col-md-1 images-buttons text-right">
					<?php
					echo $this->Html->link('<i class="far fa-times-circle"></i>', '#', array(
						'class' => 'btn-remove-image',
						'escape' => false
					));
					?>
				</div>

				<div class="form-group-label col-md-12">
					<span class="image-type-limitation"></span>
				</div>
			</div>
		</div>
	<?php endif ?>

	<?php

	if (!isset($can_add) || (isset($can_add) && $can_add)) {   ?>
		<div class="row images-upload-row-button">
			<div class="col-md-12 text-center">
				<?php
				print $this->Html->link('<i class="fas fa-plus"></i> ' . __('add_image'), '#', array(
					'class' => 'btn btn-primary btn-new-image',
					'escape' => false
				));
				?>
			</div>
		</div>
	<?php
	}
	?>

</div><!-- .form-group -->

<script type="text/javascript" charset="utf-8">
	var article_images = {
		count: 0
	};
	max_image = '<?= isset($total_image) && !empty($total_image) && $total_image > 0 ?  $total_image : 0 ?>';

	$(document).ready(function() {

		article_images.count = $('.images-upload > .well').length;

		if (max_image > 0 && article_images.count >= max_image) {
			$('.btn-new-image').hide();
		}
		

		$('.btn-remove-image').on('click', function(e) {
			e.preventDefault();

			article_images.count--;
			$('.btn-new-image').show();
			$(this).closest(".well").remove();
		});

		$('.btn-remove-uploaded-image').on('click', function(e) {
			e.preventDefault();

			var image_id = $(this).data('image-id');

			var remove_hidden_input = '<input type="hidden" name="data[remove_image][]" value="' + image_id + '">';

			article_images.count--;
			$('.btn-new-image').show();

			$(this).parents('.images-upload').append(remove_hidden_input);
			$(this).closest(".well").remove();
		});

		$('.btn-new-image').on('click', function(e) {
			e.preventDefault();

			var url = '<?php echo $add_new_images_url; ?>';

			COMMON.call_ajax({
				type: "GET",
				url: url,
				dataType: 'html',
				cache: false,
				// headers: {
				// 	'X-CSRF-Token' : $('[name="_csrfToken"]').val()
				// },
				data: {
					count: article_images.count,
					images_model: '<?php echo $images_model; ?>',
					base_model: '<?php echo isset($base_model) ? $base_model : ''; ?>',
				},
				success: function(result) {

					var counter = (article_images.count - 1);

					if (counter < 0) {
						$('.images-upload > .images-upload-row-button').before(result);

					} else {
						$('.images-upload > .well').eq(counter).after(result);
					}

					// article_images.count++;
					article_images.count = $('.images-upload > .well').length; // count again
			
					if (max_image > 0 && article_images.count >= max_image) {
						$('.btn-new-image').hide();
					}

					$('.btn-remove-image').on('click', function(e) {
						e.preventDefault();

						article_images.count--;
						$('.btn-new-image').show();
						$(this).closest(".well").remove();
					});
				},
				error: function(result) {
					console.log(result);
				}
			});
		});
	});
</script>
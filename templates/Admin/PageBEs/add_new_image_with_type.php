<div class="well well-sm">
	<div class="row images-upload-row">

        <div class="col-md-7">
            <?php  
                echo $this->Form->control($images_model.'..image_type_id', array(
                    'class' => 'form-control image-type',
                    'empty' => __("please_select"),
                    'label' => __("image_type"),
                ));
            ?>
        </div>

        <div class="col-md-4">
            <?php 
                echo $this->Form->control($images_model.'..image', array(
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
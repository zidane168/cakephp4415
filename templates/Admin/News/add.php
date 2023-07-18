<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\News $news
 */
?>
<?php 
	echo $this->Html->script('ckeditor/ckeditor.js?v=' . date('U'));	 
    echo $this->Html->script('ckfinder/ckfinder.js?v=' . date('U'));
?> 

<div class="container-fluid">

	<div class="row">
		<div class="col-12">
			<div class="card card-primary">
				<div class="card-header">
					<h3 class="card-title"> <?=__d('news','add_news'); ?> </h3>
				</div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($news, ['type' => 'file']) ?>
                    <fieldset>
                      
                        <?php
                            echo $this->element('datetime_picker', array(
                                'id'                => 'date',
                                'field_name'        => 'date',
                                'placeholder'       => __('date'),
                                'label'             => __('date'),
                                'class'             => 'date',
                                'format'            => 'YYYY-MM-DD',
                                'required'          => 'required',
                            ));
                        ?>
 
                            
                        <?php      
                            echo "<div class='bold'>" . __('thumbnail') . "</div>";
                            echo $this->element('images_upload_no_type', array(
                                'add_new_images_url'    => $add_new_images_url,
                                'images_model'          => $images_model,
                                'base_model'            => $model,
                                'total_image'           => 1, 
                            ));

                            echo $this->element('language_input_column_upload_images', array(
                                'languages_model'       => $languages_model,
                                'languages_list'        => $languages_list,
                                'language_input_fields' => $language_input_fields,
                                'languages_edit_data'   => isset($this->request->data[$languages_model]) ? $this->request->data[$languages_model] : false,
                            ));
                          
                        ?>

                        <div class="row mt-10">
                            <div class="col-2">
                                <?= $this->Form->button(__('submit'), ['class' => 'btn btn-large btn-primary form-control']) ?>
                            </div>
                        </div>
                    </fieldset>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>


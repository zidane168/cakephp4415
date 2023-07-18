<div class="form-group languages-upload">
    <div class="welllanguage well-sm">
        <div class="row images-upload-row">
            <div class="col-md-11">
                <?php
                echo $this->element('language_input_column_for_multi', array(
                    'languages_model'       => $languages_model,
                    'languages_list'        => $languages_list,
                    'language_input_fields' => $language_input_fields,
                    'languages_edit_data'   => $languages_edit_data,
                    'index_items'           => $index_items
                ));
                ?>
            </div>

            <div class="col-md-1 images-buttons text-right">
                <?php
                echo $this->Html->link('<i class="far fa-times-circle"></i>', '#', array(
                    'class' => 'btn-remove-language',
                    'escape' => false
                ));
                ?>
            </div>

            <div class="form-group-label col-md-12">
                <span class="image-type-limitation"></span>
            </div>
        </div>
    </div>
</div>
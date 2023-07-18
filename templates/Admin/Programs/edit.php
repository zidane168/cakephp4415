<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $program
 */
?>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <?= __d('center', 'edit_program'); ?> </h3>
                </div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($program, ['type' => 'file']) ?>
                    <fieldset>


                        <div>
                            <label style="min-width: 150px;"> <?php echo __d('center', 'title_color'); ?> </label>
                            <input type="color" id="title_color" name="title_color" value="<?= $program['title_color'] ?>" list="presets_title_color">
                            <datalist id="presets_title_color">
                                <option value="#87CEEA">Light Blue</option>
                                <option value="#4CB24F">Green</option>
                                <option value="#CCDA38">Lime</option>
                                <option value="#F1E78C">Khaki</option>
                                <option value="#FEBF09">Amber</option>
                                <option value="#FD5A23">Deep Orange</option>
                                <option value="#785446">Brown</option>
                                <option value="#FFDEDD">Pale Red</option>
                                <option value="#DBFEDC">Pale Green</option>
                                <option value="#DCFEFE">Pale Blue</option>
                                <option value="#FEFDCF">Pale Yellow</option>
                                <option value="#FFFFFF">White</option>
                                <option value="#000000">Black</option>
                            </datalist>
                        </div>

                        <div>
                            <label style="min-width: 150px;"> <?php echo __d('center', 'background_color'); ?> </label>
                            <input type="color" id="background_color" name="background_color" value="<?= $program['background_color'] ?>" list="presets_background_color">
                            <datalist id="presets_background_color">
                                <option value="#87CEEA">Light Blue</option>
                                <option value="#4CB24F">Green</option>
                                <option value="#CCDA38">Lime</option>
                                <option value="#F1E78C">Khaki</option>
                                <option value="#FEBF09">Amber</option>
                                <option value="#FD5A23">Deep Orange</option>
                                <option value="#785446">Brown</option>
                                <option value="#FFDEDD">Pale Red</option>
                                <option value="#DBFEDC">Pale Green</option>
                                <option value="#DCFEFE">Pale Blue</option>
                                <option value="#FEFDCF">Pale Yellow</option>
                                <option value="#FFFFFF">White</option>
                                <option value="#000000">Black</option>
                            </datalist>
                        </div>


                        <?php
                        echo $this->element('language_input_column', array(
                            'languages_model'           => $languages_model,
                            'languages_edit_model'      => $languages_edit_model,
                            'languages_list'            => $languages_list,
                            'language_input_fields'     => $language_input_fields,
                            'languages_edit_data'       => $languages_edit_data,
                        ));

                        echo $this->element('images_upload_no_type', array(
                            'add_new_images_url'    => $add_new_images_url,
                            'images_model'          => $images_model,
                            'base_model'            => $model,
                            'images_edit_data'      => $images_edit_data,
                            'total_image'           => 1,
                        ));

                        echo $this->Form->control('enabled', [
                            'label' => __('enabled')
                        ]);
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
<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Kid $kid
 */

use Cake\Core\Configure;
?>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <?= __d('parent', 'edit_kid'); ?> </h3>
                </div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($kid, ['type' => 'file']) ?>
                    <fieldset>
                        <div class="admin-avatar">
                            <?php

                            if (
                                isset($kid['kid_images']) &&
                                !empty($kid['kid_images'])
                            ) {

                                $host_name = Configure::read('host_name');
                                $src_file = $host_name . '/' . $kid['kid_images'][0]['path'];
                            } else {
                                $src_file = 'cidckids/icon/avatar.png';
                            }

                            echo $this->Html->image($src_file, array(
                                "id"    => 'avatar',
                                "class" => "image-avatar",
                                "alt"   => 'Logo',
                                "onclick" => "document.getElementById('upload-file').click()",
                            ));
                            ?>
                            <label class="admin-avatar-label" onclick="document.getElementById('upload-file').click()">
                                <?php echo __('upload_avatar') ?>
                            </label>

                            <?php
                            echo $this->Form->control('KidImages..image', array(
                                'style'     => 'display:none',
                                'type'      => 'file',
                                'accept'     => '.jpg,  .jpeg ,  .png',
                                'label'     => false,
                                'id'         => 'upload-file',
                                'type'         => 'file',
                            ));
                            ?>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('cidc_parent_id', [
                                    'empty' => __('please_select'),
                                    'required' => true,
                                    'escape' => false,
                                    'data-live-search' => true,
                                    'class' => 'selectpicker form-control',
                                    'options' => $cidcParents,
                                    'label' => "<font class='red'> * </font>" . __d('parent', 'cidc_parent'),
                                ]);
                                ?>
                            </div>
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('relationship_id', [
                                    'empty' => __('please_select'),
                                    'required' => true,
                                    'escape' => false,
                                    'data-live-search' => true,
                                    'class' => 'selectpicker form-control',
                                    'options' => $relationships,
                                    'label' => "<font class='red'> * </font>" . __d('setting', 'relationship'),
                                ]);
                                ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('gender', [
                                    'empty' => __('please_select'),
                                    'required' => true,
                                    'escape' => false,
                                    'data-live-search' => true,
                                    'class' => 'selectpicker form-control',
                                    'options' => $genders,
                                    'label' => "<font class='red'> * </font>" . __d('parent', 'gender'),
                                ]);
                                ?>
                            </div>
                            <div class="col-6">
                                <?php
                                echo $this->element('datetime_picker', array(
                                    'id'                => 'dob',
                                    'field_name'        => 'dob',
                                    'required'          => true,
                                    'escape'            => false,
                                    'placeholder'       => __d('parent', 'dob'),
                                    'label'             => __d('parent', 'dob'),
                                    'class'             => 'date',
                                    'format'            => 'YYYY-MM-DD',
                                    'value'             => $kid->dob->format('Y-m-d')
                                ));
                                ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('number_of_siblings', [
                                    'class' => 'form-control',
                                    'min'   => 0,
                                    'label' => __d('parent', 'number_of_siblings')
                                ]);
                                ?>
                            </div>
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('caretaker', [
                                    'class' => 'form-control',
                                    'label' => __d('parent', 'caretaker')
                                ]);

                                ?>
                            </div>
                        </div>

                        <?php
                        echo $this->Form->control('special_attention_needed', [
                            'class' => 'form-control',
                            'label' => __d('parent', 'special_attention_needed')
                        ]);
                        echo $this->element('language_input_column', array(
                            'languages_model'           => $languages_model,
                            'languages_edit_model'      => $languages_edit_model,
                            'languages_list'            => $languages_list,
                            'language_input_fields'     => $language_input_fields,
                            'languages_edit_data'       => $languages_edit_data,
                        ));

                        echo $this->Form->control('enabled');
                        ?>
                        <div>
                            <label><?= h(__d('setting', 'emergency_contact')) ?></label>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('emergency_contact_id', [
                                    'empty' => __('please_select'),
                                    'required' => true,
                                    'escape' => false,
                                    'data-live-search' => true,
                                    'class' => 'selectpicker form-control',
                                    'options' => $emergencyContacts,
                                    'label' => "<font class='red'> * </font>" . __d('setting', 'emergency_contact'),
                                ]);
                                ?>
                            </div>
                            <div class="col-6">
                                <?php
                                echo $this->Form->control('relationship_id', [
                                    'empty' => __('please_select'),
                                    'required' => true,
                                    'escape' => false,
                                    'data-live-search' => true,
                                    'class' => 'selectpicker form-control',
                                    'options' => $relationships,
                                    'label' => "<font class='red'> * </font>" . __d('setting', 'relationship'),
                                ]);

                                ?>
                            </div>
                        </div>


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
<?php
echo $this->Html->script('CakeAdminLTE/pages/admin_administrator.js?v=' . date('U'), array('inline' => false));
?>

<script>
    $(document).ready(function() {
        ADMIN_ADMINISTRATOR.upload_avatar();
    })
</script>
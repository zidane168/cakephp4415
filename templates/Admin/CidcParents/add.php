<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $cidcParent
 */

use Cake\Core\Configure; 

echo $this->Html->css('timeline.css'); 

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <?php echo $this->element('timeline', array(
                'parent' => true,
                'kid' => false,
            ));
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <?= __d('parent', 'add_cidc_parent'); ?> </h3>
                </div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($cidcParent, ['type' => 'file']) ?>
                    <fieldset>
                        <div class="admin-avatar">
                            <?php

                            if (
                                isset($cidcParent['cidc_parent_images']) &&
                                !empty($cidcParent['cidc_parent_images'])
                            ) {

                                $host_name = Configure::read('host_name');
                                $src_file = $host_name . '/' . $cidcParent['cidc_parent_images'][0]['path'];
                            } else {
                                $src_file = 'cidckids/icon/avatar.png';
                            } ?>


                            
                                <?php
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


                            echo $this->Form->control('CidcParentImages..image', array(
                                'style'     => 'display:none',
                                'type'      => 'file',
                                'accept'     => '.jpg,  .jpeg ,  .png',
                                'label'     => false,
                                'id'         => 'upload-file',
                                'type'         => 'file',
                            ));
                            ?>
                        </div>
                        <?php
                        echo $this->Form->control('phone_number', [
                            'class' => 'form-control',
                            'escape' => false,
                            'required' => true,
                            'label' => "<font class='red'> * </font>" . __d('parent', 'phone_number')
                        ]);
                        echo $this->Form->control('email', [
                            'class' => 'form-control',
                            'escape' => false,  
                            'label' =>  __('email')
                        ]);

                        echo $this->Form->control('password', [
                            'class' => 'form-control',
                            'escape' => false,
                            'autocomplete' => 'new-password',
                            'required' => true,
                            'label' => "<font class='red'> * </font>" . __('password')
                        ]);

                        echo $this->Form->control('gender', [
                            'empty' => __('please_select'),
                            'required' => true,
                            'escape' => false,
                            'class' => 'selectpicker form-control',
                            'options' => $genders,
                            'label' => "<font class='red'> * </font>" . __d('parent', 'gender'),
                        ]);

                        echo $this->Form->control('address', [
                            'class' => 'form-control',
                            'escape' => false,

                            'required' => true,
                            'label' => "<font class='red'> * </font>" . __('address')
                        ]);
                        echo $this->element('language_input_column', array(
                            'languages_model'       => $languages_model,
                            'languages_list'        => $languages_list,
                            'language_input_fields' => $language_input_fields,
                            'languages_edit_data'    => false
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
<?php
echo $this->Html->script('CakeAdminLTE/pages/admin_administrator.js?v=' . date('U'), array('inline' => false));
?>

<script>
    $(document).ready(function() {
        ADMIN_ADMINISTRATOR.upload_avatar();
    })
</script>
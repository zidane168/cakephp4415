<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Administrator $administrator
 */

use Cake\Core\Configure;

?>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <?= __d('administrator', 'account_info'); ?> </h3>
                </div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($administrator, ['type' => 'file']) ?>
                    <fieldset>
                        <div class="admin-avatar">
                          <?php

                            if ( isset($administrator['administrators_avatars']) &&  
                                  !empty($administrator['administrators_avatars']) ) {  
                                
                                $host_name = Configure::read('host_name');
                                $src_file = $host_name . '/' . $administrator['administrators_avatars'][0]['path'];
                            
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
                            <label class="admin-avatar-label" 
                                    onclick="document.getElementById('upload-file').click()">
                                    <?php echo __('upload_avatar') ?>
                            </label>
                           
                            <?php 
                                echo $this->Form->control('AdministratorsAvatars..image', array(
                                    'style'     => 'display:none',
                                    'type'      => 'file',
                                    'accept' 	=> '.jpg,  .jpeg ,  .png',
                                    'label' 	=> false,
                                    'id' 		=> 'upload-file',  
                                )); 
                            ?>
                        </div>

                        <?php 
                        echo $this->Form->control('name', [
                            'label' => __('name'),
                            'required' => 'true',
                            'escape' => false,
                            'class' => 'form-control'
                        ]);

                        echo "<br>";
                        ?>
                        <div>
                            <label><?= h(__('email')) ?></label>
                        </div>
                        <div>
                            <label><?= h($administrator->email) ?></label>
                        </div>
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
</div>
<?php
    echo $this->Html->script('CakeAdminLTE/pages/admin_administrator.js?v=' . date('U'), array('inline' => false));
?>

<script>   
    $(document).ready(function() {  
        ADMIN_ADMINISTRATOR.upload_avatar(); 
    })  
</script>
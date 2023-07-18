<?php

/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $cidcParent
 */

use Cake\Core\Configure;
?>

<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __d('parent', 'cidc_parent'); ?> </h3>

                    <?php
                    if (isset($permissions['CidcParents']['edit']) && ($permissions['CidcParents']['edit'] == true)) {
                    ?>

                        <div class="p-1 ml-auto box-tools">
                            <?php echo $this->Html->link('<i class="fa fa-pencil"></i> ' . __('edit'), array('action' => 'edit', $cidcParent->id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                        </div>

                    <?php
                    }
                    ?>
                </div>
                <div class="admin-avatar">

                    <?php
                    if (
                        isset($cidcParent['cidc_parent_images']) &&
                        !empty($cidcParent['cidc_parent_images'])
                    ) {
                        $src_file = $host . '/' . $cidcParent['cidc_parent_images'][0]['path'];
                    } else {
                        $src_file = 'cidckids/icon/avatar.png';
                    }

                    // echo $this->Html->image($src_file, array(
                    //     "id"    => 'avatar',
                    //     "class" => "image-avatar-display",
                    //     "alt"   => 'Logo',
                    // ));
                    ?>  
                
                    <div class="image-square">
                        <img src="<?= $src_file; ?>" class="image-avatar-display" alt="avatar" />
                    </div>
                    <label class="name-parent col-12"><?= $cidcParent->cidc_parent_languages[0]->name ?> </label>
                </div>
                <div class="box-body">
                    <!-- <div class="row">
                        <label class="text-left title-information col-6"><?= __d('parent', 'information') ?></label>
                        <label class="text-left title-information col-6"><?= __d('parent', 'children') ?></label>
                    </div> -->
                    <div class="row parent-detail">
                        <div class="col-xs-12 col-md-6">
                            <section>
                                <label class="title-information"><?= __d('parent', 'information') ?></label>

                                <div>
                                    <div class="title"><?= __d('parent', 'phone_number') ?> </div>
                                    <div class="content"> <?=  $this->Com->formatPhoneNumber($cidcParent->user->phone_number) ?> </div>
                                </div>

                                <div style="margin-top: 12px;">
                                    <div class="title"><?= __('email') ?> </div>
                                    <div class="content"> <?= h($cidcParent->user->email) ?> </div>
                                </div>

                                <div style="margin-top: 12px;">
                                    <div class="title"><?= __d('parent', 'gender') ?> </div>
                                    <div class="content"> <?= h($genders[$cidcParent->gender]) ?> </div>
                                </div>

                                <div style="margin-top: 12px;">
                                    <div class="title"><?= __d('parent', 'address') ?> </div>
                                    <div class="content"> <?= h($cidcParent->address) ?> </div>
                                </div>
                            </section>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <section>
                                <label class="title-information"><?= __d('parent', 'children') ?></label>

                                <?php
                                if (count($cidcParent->kids) > 0) {
                                    foreach ($cidcParent->kids as $index => $kid) : ?>
                                        <div class="children-detail">
                                            <?php
                                            $default_avatar = isset($kid->kid_images) && !empty($kid->kid_images) ? $url . $kid->kid_images[0]->path : '';
                                            $url_avatar = "";
                                            $name   = $kid->kid_languages[0]->name;
                                            $relationships   = $kid->relationship->relationship_languages[0]->name;

                                            $gender = $kid->gender == 1 ? __d('parent', 'male') : __d('parent', 'female');
                                            ?>
                                            <div style="display: flex; flex-grow: 3;   align-items: center;">
                                                <div style="padding-left: 10px"> <?php echo ($index + 1) . '.'; ?> </div>

                                                <div style="padding-left: 10px">
                                                    <?php
                                                    if ($default_avatar) {
                                                        $url_avatar = $default_avatar;
                                                    } else {
                                                        $url_avatar = $url . 'webroot/img/cidckids/student/girl.png';
                                                        if ($kid->gender == 1) {
                                                            $url_avatar = $url . 'webroot/img/cidckids/student/boy.png';
                                                        }
                                                    }

                                                    echo $this->Html->image($url_avatar, array(
                                                        "class" => "image-avatar-kid-parent",
                                                        "alt"   => 'avatar',
                                                    ));

                                                    ?>
                                                </div>
                                                <div class="children-detail-name"> <?= $name; ?> </div>
                                                <div class="children-detail-relationship"> <?= $relationships; ?> </div>
                                            </div>
                                            <div class="children-detail-gender"><?= h($gender) ?> </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php } ?>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- top menu -->

<?php

use Cake\Routing\Router;
?>

<nav class="main-header  navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav" style="display:flex; align-items: center;">
        <li class="nav-item">
            <a class="nav-link" style="color: black" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item">
            <?= __d('center', 'school_management') ?>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto" display="flex; align-items: center">

        <li class="dropdown language">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <span class="hidden-xs top-menu-parent">
                    <?php
                    $session = $this->request->getSession();
                    $current_lang = $session->read('Config.language');

                    echo $this->Html->image('flags/' . $current_lang . '.png', array(
                        'class' => 'flag',
                        'style' => 'width: 40px;',
                        'alt' => __($current_lang . '_name')
                    )) . "<span style='color: #1C1F28'>" . __($current_lang . '_name') . "</span>";
                    ?>
                </span>
            </a>

            <ul class="dropdown-menu top-menu-children" style="padding-left: 10px; ">

                <?php


                echo $this->Form->create(NULL,  array(Router::fullBaseUrl(null), 'id' => 'form-language', 'type' => 'GET'));
                $language = array();
                foreach ($available_language as $lang) {
                    $language[] = ['value' => $lang, 'text' => __($lang . '_name')];
                }
                echo $this->Form->radio("rblanguage", $language, array(
                    'class' => 'btn-change-language',
                    'style' => 'margin-right: 10px;',
                    'value' =>  $current_lang,  // auto SELECTED the current language
                ));

                ?>

                <?= $this->Form->end() ?>
            </ul>
        </li>

        <li class="dropdown " style="margin-left:80px; margin-right:5px; ">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="display: flex; align-items: center">

                <?php
                $current_user = $session_administrator['current']; 

                $name = $first_character = "abc";
                if (isset($current_user->name) && !empty($current_user->name)) {
                    $url =  $this->Url->build(['controller' => 'administrators', 'action' => 'editPassword', $current_user->id, 'admin' => true]);
                    $name =  $current_user->name . "<a href=" . $url . ">   </a>  ";
                    $first_character = substr($name, 0, 1);
                }

                if (isset($current_user->administrators_avatars) && !empty($current_user->administrators_avatars)) { ?>
                    <span> 
                        <div style="aspect-ratio: 1; width: 50%" >
                            <img class="profile-image" src='<?= Router::url('/', true) . $current_user->administrators_avatars[0]->path; ?>' alt='avatar'/> 
                        </div>
                    </span>
                
                <?php } else { ?>
                    <span class="profile-image"> <?= $first_character; ?> </span>
                
                <?php } ?> 
               
                <span style='color: #1C1F28; margin-left: 5px;'> <?= $name ?> </span> 
            </a>

            <ul class="dropdown-menu top-menu-children" style="padding-left: 10px">

                <li> <a href="<?= $this->Url->build(["controller" => "administrators", "action" => "accountInfo", $current_user->id]); ?>" class="text-color-1 flex items-center">
                        <div>
                            <?php echo $this->Html->image('cidckids/account-info.png', array(
                                'style' => 'width: 20px;',
                                'alt' => 'logout',
                            )) ?>
                        </div>
                        <div class="ml-1"> <?= __d('administrator','account_info'); ?> </div>
                    </a> </li>

                <li> <a href="<?= $this->Url->build(["controller" => "administrators", "action" => "editPassword", $current_user->id]); ?>" class="text-color-1 flex items-center">
                        <div>
                            <?php echo $this->Html->image('cidckids/change-password.png', array(
                                'style' => 'width: 15px;',
                                'alt' => 'logout',
                            )) ?>
                        </div>
                        <div class="ml-1"> <?= __d('administrator','change_password'); ?> </div>
                </li>
                <li>
                    <a href="<?= $this->Url->build(["controller" => "administrators", "action" => "logout"]); ?>" class="text-color-1 flex items-center">
                        <div>
                            <?php echo $this->Html->image('cidckids/logout.png', array(
                                'style' => 'width: 20px;',
                                'alt' => 'logout',
                            )) ?>
                        </div>
                        <div class="ml-1"> <?= __('sign_out'); ?> </div>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>
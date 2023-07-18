<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- sidebar-background-primary  -->
    <!-- Brand Logo -->
    <a href="#" class="brand-link" style="height: 57px">

        <?php

        use Cake\Core\Configure;
 
        echo $this->Html->image('cidckids/logo2.png', array(
            "style" => 'width: 120px',
            "alt"   => 'Logo',
        ));
        ?>

        <span class="brand-text font-weight-light"> <?php // echo Configure::read('site.name') 
                                                    ?> </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">


        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">

                <?php
                if (isset($permissions) && $permissions) {  ?>

                    <!-- Add icons to the links using the .nav-icon class
                        with font-awesome or any other icon font library 
                    --> 

                    <?php if (isset($permissions['Centers']['view']) && ($permissions['Centers']['view'] == true)) {

                        $menu_open = ' ';
                        $active   =  ' ';
                        $active_centers  =  ' ';
                        $active_programs  =  ' ';
                        $active_courses  =  ' ';
                        $active_news  =  ' ';
                        $active_class_types  =  ' ';
                        $active_cidc_classes  =  ' ';
                        $active_order = ' ';
                        $active_student_register_class = ' ';
                        $active_student_attended_class = ' ';
                        $active_professionals  =  ' ';

                        if (
                            $this->request->getParam('controller') == 'Centers' ||
                            $this->request->getParam('controller') == 'Programs' ||
                            $this->request->getParam('controller') == 'Courses' ||
                            $this->request->getParam('controller') == 'Centers' ||
                            $this->request->getParam('controller') == 'CidcClasses' ||
                            $this->request->getParam('controller') == 'ClassTypes' ||
                            $this->request->getParam('controller') == 'Orders' ||
                            $this->request->getParam('controller') == 'StudentRegisterClasses' ||
                            $this->request->getParam('controller') == 'StudentAttendedClasses' ||
                            $this->request->getParam('controller') == 'Professionals' ||
                            $this->request->getParam('controller') == 'News'
                        ) {
                            $menu_open = ' menu-open';
                        }

                        if ($this->request->getParam('controller') == 'Centers') {
                            $active_centers  =  ' active';
                        }

                        if ($this->request->getParam('controller') == 'Programs') {
                            $active_programs  =  ' active';
                        }

                        if ($this->request->getParam('controller') == 'Courses') {
                            $active_courses  =  ' active';
                        } 

                        if ($this->request->getParam('controller') == 'CidcClasses') {
                            $active_cidc_classes  =  ' active';
                        }

                        if ($this->request->getParam('controller') == 'ClassTypes') {
                            $active_class_types  =  ' active';
                        }

                        if ($this->request->getParam('controller') == 'Orders') {
                            $active_order  =  ' active';
                        }

                        if ($this->request->getParam('controller') == 'StudentRegisterClasses') {
                            $active_student_register_class  =  ' active';
                        }

                        if ($this->request->getParam('controller') == 'StudentAttendedClasses') {
                            $active_student_attended_class  =  ' active';
                        }

                        if ($this->request->getParam('controller') == 'News') {
                            $active_news  =  ' active';
                        }

                        if ($this->request->getParam('controller') == 'Professionals') {
                            $active_professionals  =  ' active';
                        }

                    ?>

                        <li class="nav-item <?= $menu_open; ?>">
                            <a href="#" class="nav-link <?= $active; ?>">
                                <ion-icon name="home-outline"></ion-icon>
                                <p>   <?= __d('center', 'school_management'); ?> 
                                    <i class="right fas fa-angle-left"></i> 
                                </p>
                            </a>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'Centers', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active_centers; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/center.png', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'Logo',
                                        )); ?>
                                        <p> <?= __d('center', 'centers'); ?> </p>
                                    </a>
                                </li>
                            </ul>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'Programs', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active_programs; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/program.svg', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'Logo',
                                        )); ?>
                                        <p> <?= __d('center', 'programs'); ?> </p>
                                    </a>
                                </li>
                            </ul>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'Courses', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active_courses; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/course.png', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'Logo',
                                        )); ?>
                                        <p> <?= __d('center', 'courses'); ?> </p>
                                    </a>
                                </li>
                            </ul> 

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'CidcClasses', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active_cidc_classes; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/class.svg', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'Logo',
                                        )); ?>
                                        <p> <?= __d('center', 'cidc_classes'); ?> </p>
                                    </a>
                                </li>
                            </ul>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'ClassTypes', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active_class_types; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/class.png', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'Logo',
                                        )); ?>
                                        <p> <?= __d('center', 'class_types'); ?> </p>
                                    </a>
                                </li>
                            </ul>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'News', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active_news; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/news.png', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'news',
                                        )); ?>
                                        <p> <?= __d('news', 'newses'); ?> </p>
                                    </a>
                                </li>
                            </ul>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'StudentRegisterClasses', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active_student_register_class; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/student_register_class.png', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'class',
                                        )); ?>
                                        <p> <?= __d('cidcclass', 'student_register_classes'); ?> </p>
                                    </a>
                                </li>
                            </ul>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'Orders', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active_order; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/cart.png', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'class',
                                        )); ?>
                                        <p> <?= __d('order', 'orders'); ?> </p>
                                    </a>
                                </li>
                            </ul> 

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'Professionals', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active_professionals; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/professional.png', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'Logo',
                                        )); ?>
                                        <p> <?= __d('professional', 'professionals'); ?> </p>
                                    </a>
                                </li>
                            </ul>

                        </li>
                    <?php } ?>

                    <?php if (isset($permissions['CidcParents']['view']) && ($permissions['CidcParents']['view'] == true)) {

                        $menu_open = ' ';
                        $active   =  ' ';
                        $active_cidc_parents  =  ' ';
                        $active_kids  =  ' ';
                        $active_user_verifications  =  ' ';

                        if (
                            $this->request->getParam('controller') == 'CidcParents' ||
                            $this->request->getParam('controller') == 'UserVerifications' ||
                            $this->request->getParam('controller') == 'Kids'
                        ) {
                            $menu_open = ' menu-open';
                        }

                        if ($this->request->getParam('controller') == 'CidcParents') {
                            $active_cidc_parents  =  ' active';
                        }
                        if ($this->request->getParam('controller') == 'Kids') {
                            $active_kids  =  ' active';
                        }
                        if ($this->request->getParam('controller') == 'UserVerifications') {
                            $active_user_verifications  =  ' active';
                        }

                    ?>

                        <li class="nav-item <?= $menu_open; ?>">
                            <a href="#" class="nav-link <?= $active; ?>">

                                <p> <?= __d('parent', 'parent_children'); ?> <i class="right fas fa-angle-left"></i> </p>
                            </a>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'CidcParents', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active_cidc_parents; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/parent.png', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'Logo',
                                        )); ?>
                                        <p> <?= __d('parent', 'cidc_parents'); ?> </p>
                                    </a>
                                </li>
                            </ul>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'Kids', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active_kids; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/children.png', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'Logo',
                                        )); ?>
                                        <p> <?= __d('parent', 'kids'); ?> </p>
                                    </a>
                                </li>
                            </ul>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'UserVerifications', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active_user_verifications; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/user_verification.png', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'Logo',
                                        )); ?>
                                        <p> <?= __d('user', 'user_verifications'); ?> </p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php } ?>

                    <?php if (isset($permissions['SystemMessages']['view']) && ($permissions['SystemMessages']['view'] == true)) {

                        $menu_open = ' ';
                        $active   =  ' ';
                        $active_system_messages  =  ' ';

                        if ($this->request->getParam('controller') == 'SystemMessages') {
                            $menu_open = ' menu-open';
                        }

                        if ($this->request->getParam('controller') == 'SystemMessages') {
                            $active_system_messages  =  ' active';
                        }

                    ?>

                        <li class="nav-item <?= $menu_open; ?>">
                            <a href="#" class="nav-link <?= $active; ?>">
                                <p> <?= __d('user', 'notice'); ?> <i class="right fas fa-angle-left"></i> </p>
                            </a>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'SystemMessages', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active_system_messages; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/staff.svg', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'Logo',
                                        )); ?>
                                        <p> <?= __d('user', 'system_messages'); ?> </p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php } ?>

                    <?php if (isset($permissions['Staffs']['view']) && ($permissions['Staffs']['view'] == true)) {

                        $menu_open = ' ';
                        $active   =  ' ';
                        $active_staffs  =  ' ';
                        $active_albums  =  ' ';
                        $active_videos  =  ' ';
                        $active_reschedule_histories  =  ' ';
                        $active_sick_leave_histories  =  ' ';

                        if (
                            $this->request->getParam('controller') == 'Staffs' ||
                            $this->request->getParam('controller') == 'Albums' ||
                            $this->request->getParam('controller') == 'Videos' ||
                            $this->request->getParam('controller') == 'RescheduleHistories' ||
                            $this->request->getParam('controller') == 'SickLeaveHistories'

                        ) {
                            $menu_open = ' menu-open';
                        }

                        if ($this->request->getParam('controller') == 'Staffs') {
                            $active_staffs  =  ' active';
                        }

                        if ($this->request->getParam('controller') == 'Albums') {
                            $active_albums  =  ' active';
                        }

                        if ($this->request->getParam('controller') == 'Videos') {
                            $active_videos  =  ' active';
                        }
                        if ($this->request->getParam('controller') == 'RescheduleHistories') {
                            $active_reschedule_histories  =  ' active';
                        }
                        if ($this->request->getParam('controller') == 'SickLeaveHistories') {
                            $active_sick_leave_histories  =  ' active';
                        }

                    ?>

                        <li class="nav-item <?= $menu_open; ?>">
                            <a href="#" class="nav-link <?= $active; ?>">

                                <p> <?= __d('staff', 'staff'); ?> <i class="right fas fa-angle-left"></i> </p>
                            </a>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'Staffs', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active_staffs; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/staff.svg', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'Logo',
                                        )); ?>
                                        <p> <?= __d('staff', 'staffs'); ?> </p>
                                    </a>
                                </li>
                            </ul>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'Albums', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active_albums; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/album.png', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'Logo',
                                        )); ?>
                                        <p> <?= __d('staff', 'albums'); ?> </p>
                                    </a>
                                </li>
                            </ul>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'Videos', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active_videos; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/video.png', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'Logo',
                                        )); ?>
                                        <p> <?= __d('staff', 'videos'); ?> </p>
                                    </a>
                                </li>
                            </ul>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'RescheduleHistories', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active_reschedule_histories; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/reschedule.png', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'Logo',
                                        )); ?>
                                        <p> <?= __d('staff', 'reschedule_histories'); ?> </p>
                                    </a>
                                </li>
                            </ul>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'SickLeaveHistories', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active_sick_leave_histories; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/sick.png', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'Logo',
                                        )); ?>
                                        <p> <?= __d('staff', 'sick_leave_histories'); ?> </p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php } ?>

                    <!-- Start Plugin::Administration  -->
                    <?php if ((isset($permissions['Administrators']['view']) && ($permissions['Administrators']['view'] == true)) ||
                        (isset($permissions['Permissions']['view']) && ($permissions['Permissions']['view'] == true)) ||
                        (isset($permissions['Roles']['view']) && ($permissions['Roles']['view'] == true))
                    ) {

                        $menu_open = ' ';
                        $active  =  ' ';
                        $active1  =  ' ';
                        $active11 =  ' ';
                        $active2  =  ' ';
                        $active3  =  ' ';

                        if (
                            $this->request->getParam('controller') == 'Administrators' ||
                            $this->request->getParam('controller') == 'Permissions' ||
                            $this->request->getParam('controller') == 'Roles'
                        ) {
                            $menu_open = ' menu-open';
                        }

                        if ($this->request->getParam('controller') == 'Administrators') {
                            $active1  =  ' active';
                        }

                        if ($this->request->getParam('controller') == 'Permissions') {
                            $active2  =  ' active';
                        }

                        if ($this->request->getParam('controller') == 'Roles') {
                            $active3  =  ' active';
                        }
                    ?>

                        <li class="nav-item <?= $menu_open; ?>">
                            <a href="#" class="nav-link <?= $active; ?>">
                                <!-- <i class="nav-icon fas fa-tachometer-alt"></i> -->
                                <!-- <i class="nav-icon fas fa-user-cog"></i> -->
                                <p> <?= __d('administrator', 'administrator'); ?> <i class="right fas fa-angle-left"></i> </p>
                            </a>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'administrators', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active1; ?>">

                                        <i class="far fa-circle nav-icon"></i>
                                        <p> <?= __d('administrator', 'administrators'); ?> </p>
                                    </a>
                                </li>

                                <!-- <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'permissions', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active2; ?>">

                                        <i class="far fa-circle nav-icon"></i>
                                        <p> <?= __d('permission', 'permissions'); ?> </p>
                                    </a>
                                </li> -->

                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'roles', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active3; ?>">

                                        <i class="far fa-circle nav-icon"></i>
                                        <p> <?= __d('role', 'roles'); ?> </p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                    <?php } ?>

                    <?php if ((isset($permissions['Relationships']['view']) && ($permissions['Relationships']['view'] == true))) {

                        $menu_open = ' ';
                        $active  =  ' ';
                        $active_relationship =  ' ';
                        $active_emergency_contact =  ' ';
                        $active_district =  ' ';
                        $active_feedback =  ' ';
                        $active_about =  ' ';
                        $active_contact =  ' ';
                        $active_holiday =  ' ';
                        $active_policy = ' ';
                        $active_term = ' ';
                        if (
                            $this->request->getParam('controller') == 'Relationships' ||
                            $this->request->getParam('controller') == 'EmergencyContacts' ||
                            $this->request->getParam('controller') == 'Feedbacks' ||
                            $this->request->getParam('controller') == 'Abouts' ||
                            $this->request->getParam('controller') == 'Contacts' ||
                            $this->request->getParam('controller') == 'CidcHolidays' ||
                            $this->request->getParam('controller') == 'Districts' ||
                            $this->request->getParam('controller') == 'Terms' ||
                            $this->request->getParam('controller') == 'PrivacyPolicies'
                        ) {
                            $menu_open = ' menu-open';
                        }

                        if ($this->request->getParam('controller') == 'Relationships') {
                            $active_relationship  =  ' active';
                        }

                        if ($this->request->getParam('controller') == 'EmergencyContacts') {
                            $active_emergency_contact  =  ' active';
                        }

                        if ($this->request->getParam('controller') == 'Districts') {
                            $active_district  =  ' active';
                        }

                        if ($this->request->getParam('controller') == 'Feedbacks') {
                            $active_feedback  =  ' active';
                        }

                        if ($this->request->getParam('controller') == 'Abouts') {
                            $active_about  =  ' active';
                        }

                        if ($this->request->getParam('controller') == 'Contacts') {
                            $active_contact  =  ' active';
                        }

                        if ($this->request->getParam('controller') == 'CidcHolidays') {
                            $active_holiday  =  ' active';
                        }

                        if ($this->request->getParam('controller') == 'PrivacyPolicies') {
                            $active_policy  =  ' active';
                        }

                        if ($this->request->getParam('controller') == 'Terms') {
                            $active_term  =  ' active';
                        }

                    ?>

                        <li class="nav-item <?= $menu_open; ?>">
                            <a href="#" class="nav-link <?= $active; ?>">

                                <!-- <i class="nav-icon fas far fa-building"></i> -->
                                <p> <?= __d('setting', 'setting'); ?> <i class="right fas fa-angle-left"></i> </p>
                            </a>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'Relationships', 'action' => 'index']); ?>" class="nav-link <?= $active_relationship; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/relationship.png', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'Logo',
                                        )); ?>
                                        <p> <?= __d('setting', 'relationships'); ?> </p>
                                    </a>
                                </li>
                            </ul>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'EmergencyContacts', 'action' => 'index']); ?>" class="nav-link <?= $active_emergency_contact; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/emergency_contact.png', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'Logo',
                                        )); ?>
                                        <p> <?= __d('setting', 'emergency_contacts'); ?> </p>
                                    </a>
                                </li>
                            </ul>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'Districts', 'action' => 'index']); ?>" class="nav-link <?= $active_district; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/district.png', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'Logo',
                                        )); ?>
                                        <p> <?= __d('setting', 'districts'); ?> </p>
                                    </a>
                                </li>
                            </ul>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'Feedbacks', 'action' => 'index']); ?>" class="nav-link <?= $active_feedback; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/chat.png', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'Logo',
                                        )); ?>
                                        <p> <?= __d('setting', 'feedbacks'); ?> </p>
                                    </a>
                                </li>
                            </ul>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'Abouts', 'action' => 'index']); ?>" class="nav-link <?= $active_about; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/feedback.png', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'Logo',
                                        )); ?>
                                        <p> <?= __d('setting', 'abouts'); ?> </p>
                                    </a>
                                </li>
                            </ul>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'Contacts', 'action' => 'index']); ?>" class="nav-link <?= $active_contact; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/contact.png', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'Logo',
                                        )); ?>
                                        <p> <?= __d('setting', 'contacts'); ?> </p>
                                    </a>
                                </li>
                            </ul>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'CidcHolidays', 'action' => 'index']); ?>" class="nav-link <?= $active_holiday; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/holiday.png', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'Logo',
                                        )); ?>
                                        <p> <?= __d('setting', 'cidc_holidays'); ?> </p>
                                    </a>
                                </li>
                            </ul>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'PrivacyPolicies', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active_policy; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/policy.svg', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'class',
                                        )); ?>
                                        <p> <?= __d('cidcclass', 'privacy_policies'); ?> </p>
                                    </a>
                                </li>
                            </ul>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= $this->Url->build(['controller' => 'Terms', 'action' => 'index', 'admin' => true]); ?>" class="nav-link <?= $active_term; ?>">

                                        <?php echo $this->Html->image('cidckids/icon/term.png', array(
                                            "class" => "menu-icon",
                                            "alt"   => 'class',
                                        )); ?>
                                        <p> <?= __d('setting', 'terms'); ?> </p>
                                    </a>
                                </li>
                            </ul>
                        </li> 
                    <?php } ?>  
                <?php } ?> 

            </ul>
        </nav>

        <div class="text-right">
            <label class="environment"> (<?= Configure::read('env') . " " . Configure::read('web.version'); ?>) </label>
        </div>

        <!-- sidebar-menu -->
    </div>
    <!-- sidebar -->
</aside>
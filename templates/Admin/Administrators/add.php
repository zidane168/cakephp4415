<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Administrator $administrator
 */
?>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <?= __d('administrator', 'add_administrator'); ?> </h3>
                </div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($administrator) ?>
                    <fieldset>
                        <?php
                        // echo $this->Form->control('company_id', [
                        //         'class' => 'form-control', 
                        //         'options' => $companies, 

                        //         'label' => '<font class="red"> * </font>' . __d('company', 'company'),
                        //         'escape' => false,
                        //         'empty' => __('please_select'),
                        //     ]);

                        echo "<br>";


                        // $roles = [
                        //     'Value 1' => 'Label 1',
                        //     'Value 2' => 'Label 2'
                        // ];  

                        echo '<div style="style="margin-bottom: 10px;"> <span class="red"> * </span> <span  class="bold">' .  __d('role', 'role') . '</span> </div>' ;

                        echo '<div class="admin-role">';
                        echo $this->Form->select('roles', $roles, [
                            'multiple' => 'checkbox',
                        ]);
                        echo '</div>';

                        // echo $this->Form->checkbox('Role.roles', [
                        //     'class' => 'form-control',
                        //     'options' => $roles,
                        //     'label' => '<font color="red">*</font>'.__d('administration','role')
                        // ]);

                        echo "<br>";

                        echo '<div style="margin-bottom: 10px;"  ><span class="red"> * </span> <span  class="bold">' .  __d('role', 'manage_center') . '</span> </div>';
                        echo '<div class="admin-role">';
                        echo $this->Form->select('centers', $centers, [
                            'multiple' => 'checkbox',
                        ]);
                        echo '</div>';
                        echo $this->Form->control('name', [
                            'label' => '<font class="red"> * </font>' . __('name'),
                            'required' => 'true',
                            'escape' => false,
                            'class' => 'form-control'
                        ]);

                        echo "<br>";
                        echo $this->Form->control('email', [
                            'label' => '<font class="red"> * </font>' . __('email'),
                            'required' => 'true',
                            'escape' => false,
                            'class' => 'form-control'
                        ]);

                        echo "<br>";
                        echo $this->Form->control('phone', [
                            'label' =>  __('phone'),
                            'class' => 'form-control'
                        ]);

                        echo "<br>";
                        echo $this->Form->control('password', [
                            'required' => 'true',
                            'escape' => false,
                            'label' => '<font class="red"> * </font>' .  __('password'),
                            'class' => 'form-control'
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
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
                    <h3 class="card-title"> <?= __d('administrator', 'administrator'); ?> </h3>
                </div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($administrator) ?>
                    <fieldset>
                        <legend><?= __('edit') ?></legend>
                        <?php
                        echo '<div  style="margin-bottom: 10px;" > <span class="red"> * </span> <span class="bold">' .  __d('role', 'role') . '</span> </div>';

                        echo '<div class="admin-role">';
                        echo $this->Form->select('roles', $roles, [
                            'multiple' => 'checkbox',
                            'value' => $currentRoles,
                            'class' => "checkbox"
                        ]);
                        echo '</div>';


                        echo "<br>";
                        echo '<div style="margin-bottom: 10px;"> <span class="red"> * </span> <span  class="bold">' .  __d('role', 'manage_center') . '</span> </div>';

                        echo '<div class="admin-role">';
                        echo $this->Form->select('centers', $centers, [
                            'multiple' => 'checkbox',
                            'value' => $currentCenters,
                            'class' => "checkbox"
                        ]);
                        echo '</div>';
                        echo "<br>";
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
                            'readonly' => true,
                            'escape' => false,
                            'class' => 'form-control'
                        ]);

                        echo "<br>";
                        echo $this->Form->control('phone', [
                            'label' =>  __('phone'),
                            'readonly' => true,
                            'class' => 'form-control'
                        ]);
                        ?>

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
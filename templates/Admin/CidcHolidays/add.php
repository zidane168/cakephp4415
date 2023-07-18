<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CidcHoliday $cidcHoliday
 */
?>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <?= __d('setting', 'add_cidc_holiday'); ?> </h3>
                </div>

                <div class="card-body">
                    <?= $this->Form->create($cidcHoliday) ?>
                    <fieldset>

                        <?php
                        echo $this->element('datetime_picker', array(
                            'id'                => 'date',
                            'field_name'        => 'date',
                            'placeholder'       => __('date'),
                            'label'             => __('date'),
                            'class'             => 'date',
                            'format'            => 'YYYY-MM-DD',
                        ));
                        echo $this->Form->control('description', [
                            'class' => 'form-control',
                            'escape' => false,
                            'required' => true,
                            'label' => "<font class='red'> * </font>" . __('description')
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
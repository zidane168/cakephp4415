<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\StudentAttendedClass $studentAttendedClass
 */
?>

<div class="container-fluid">

	<div class="row">
		<div class="col-12">
			<div class="card card-primary">
				<div class="card-header">
					<h3 class="card-title"> <?=__('Student Attended Classes'); ?> </h3>
				</div>

                <div class="card-body">
                    <?= $this->Form->create($studentAttendedClass) ?>
                    <fieldset>
                      
                        <?php
                            echo $this->Form->control('cidc_class_id', [
                                'required' => true,
                                'escape' => false,
                                'class' => ' form-control',
                                'options' => [$cidcClasses->name],
                                'disabled' => 'disabled',
                                'label' => "<font class='red'> * </font>" . __d('center', 'cidc_class'),
                            ]);
                            echo $this->Form->control('kid_id', [
                                'disabled' => 'disabled',
                                'required' => true,
                                'escape' => false,
                                'class' => 'form-control',
                                'options' => [$kids->kid_languages[0]->name],
                                'label' => "<font class='red'> * </font>" . __d('parent', 'kid'),
                            ]);

                            echo $this->Form->control('date', [
                                'empty' => __('please_select'),
                                'required' => true,
                                'escape' => false,
                                'class' => 'selectpicker form-control',
                                'options' => $days,
                                'label' => "<font class='red'> * </font>" . __d('parent', 'date'),
                            ]);
                            
                            echo $this->Form->control('status', [
                                'empty' => __('please_select'),
                                'required' => true,
                                'escape' => false,
                                'data-live-search' => true,
                                'class' => 'selectpicker form-control',
                                'options' => $status,
                                'label' => "<font class='red'> * </font>" . __d('cidcclass', 'status'),
                            ]);
                            echo $this->Form->control('enabled', [
                                'label' => __d('patient', 'enabled')
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


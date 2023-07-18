<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\SystemMessage $systemMessage
 */
?>

<div class="container-fluid">

	<div class="row">
		<div class="col-12">
			<div class="card card-primary">
				<div class="card-header">
					<h3 class="card-title"> <?=__('System Messages'); ?> </h3>
				</div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($systemMessage) ?>
                    <fieldset>
                      
                        <?php
                            echo $this->Form->control('cidc_class_id', ['class' => 'form-control', 'options' => $cidcClasses]);
                            echo $this->Form->control('parent_id', ['class' => 'form-control', 'options' => $parentSystemMessages]);
                            echo $this->Form->control('kid_id', ['class' => 'form-control', 'options' => $kids]);
                            echo $this->Form->control('status', ['class' => 'form-control']);
                            echo $this->Form->control('created_by', ['class' => 'form-control', 'options' => $createdBy, 'empty' => __('please_select')]);
                            echo $this->Form->control('modified_by', ['class' => 'form-control', 'options' => $modifiedBy, 'empty' => __('please_select')]);
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


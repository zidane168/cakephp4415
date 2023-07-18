<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Language $language
 */
?>

<div class="container-fluid">

	<div class="row">
		<div class="col-12">
			<div class="card card-primary">
				<div class="card-header">
					<h3 class="card-title"> <?=__('Languages'); ?> </h3>
				</div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($language) ?>
                    <fieldset>
                        <legend><?= __('Edit Language') ?></legend>
                        <?php
                            echo $this->Form->control('alias', ['class' => 'form-control']);
                            echo $this->Form->control('name', ['class' => 'form-control']);
                            echo $this->Form->control('is_default', ['class' => 'form-control']);
                            echo $this->Form->control('enabled', ['class' => 'form-control']);
                            echo $this->Form->control('modified_by', ['class' => 'form-control']);
                            echo $this->Form->control('created_by', ['class' => 'form-control']);
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


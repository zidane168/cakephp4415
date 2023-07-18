<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Permission $permission
 */
?>

<div class="container-fluid">

	<div class="row">
		<div class="col-12">
			<div class="card card-primary">
				<div class="card-header">
					<h3 class="card-title"> <?=__('Permissions'); ?> </h3>
				</div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($permission) ?>
                    <fieldset>
                        <legend><?= __('Edit Permission') ?></legend>
                        <?php
                            echo $this->Form->control('slug', ['class' => 'form-control']);
                            echo $this->Form->control('name', ['class' => 'form-control']);
                            echo $this->Form->control('p_plugin', ['class' => 'form-control']);
                            echo $this->Form->control('p_controller', ['class' => 'form-control']);
                            echo $this->Form->control('p_model', ['class' => 'form-control']);
                            echo $this->Form->control('action', ['class' => 'form-control']);
                            echo $this->Form->control('updated_by', ['class' => 'form-control']);
                            echo $this->Form->control('created_by', ['class' => 'form-control']);
                            echo $this->Form->control('roles._ids', ['class' => 'form-control',  'options' => $roles]);
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


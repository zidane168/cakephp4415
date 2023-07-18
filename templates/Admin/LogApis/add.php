<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\LogApi $logApi
 */
?>

<div class="container-fluid">

	<div class="row">
		<div class="col-12">
			<div class="card card-primary">
				<div class="card-header">
					<h3 class="card-title"> <?=__('Log Apis'); ?> </h3>
				</div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($logApi) ?>
                    <fieldset>
                      
                        <?php
                            echo $this->Form->control('url', ['class' => 'form-control']);
                            echo $this->Form->control('params', ['class' => 'form-control']);
                            echo $this->Form->control('result', ['class' => 'form-control']);
                            echo $this->Form->control('old_data', ['class' => 'form-control']);
                            echo $this->Form->control('new_data', ['class' => 'form-control']);
                            echo $this->Form->control('status', ['class' => 'form-control']);
                            echo $this->Form->control('archived', ['class' => 'form-control']);
                            echo $this->Form->control('created_by', ['class' => 'form-control']);
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


<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $album
 */
?>

<div class="container-fluid">

	<div class="row">
		<div class="col-12">
			<div class="card card-primary">
				<div class="card-header">
					<h3 class="card-title"> <?=__('Albums'); ?> </h3>
				</div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($album) ?>
                    <fieldset>
                      
                        <?php
                            echo $this->Form->control('cidc_class_id', ['class' => 'form-control']);
                            echo $this->Form->control('width', ['class' => 'form-control']);
                            echo $this->Form->control('height', ['class' => 'form-control']);
                            echo $this->Form->control('size', ['class' => 'form-control']);
                            echo $this->Form->control('path', ['class' => 'form-control']);
                            echo $this->Form->control('file_name', ['class' => 'form-control']);
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


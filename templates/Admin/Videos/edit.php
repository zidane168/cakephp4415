<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Video $video
 */
?>

<div class="container-fluid">

	<div class="row">
		<div class="col-12">
			<div class="card card-primary">
				<div class="card-header">
					<h3 class="card-title"> <?=__('Videos'); ?> </h3>
				</div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($video) ?>
                    <fieldset>
                      
                        <?php
                            echo $this->Form->control('cidc_class_id', ['class' => 'form-control', 'options' => $cidcClasses]);
                            echo $this->Form->control('ext', ['class' => 'form-control']);
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


<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order $order
 */
?>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"> <?= __d('order', 'edit_order'); ?> </h3>
                </div>

                <div class="card-body table-responsive">
                    <?= $this->Form->create($order, ['type' => 'file']) ?>
                    <fieldset>

                        <?php
                            echo $this->Form->control('order_number', [
                                'label' => __d('order', 'order_number'),
                                'readonly' => true,
                                'class' => 'form-control']);
                            echo $this->Form->control('total_fee', [
                                'label' => __d('order', 'total_fee'),
                                'readonly' => true,
                                'class' => 'form-control']); 

                            echo $this->cell('orderStatus', ['status' => $order->status]);

                            echo $this->element('multi_images_upload_container_edit', array(
                                'images_model' => $images_model,
                                'accept_file' => array(
                                    '.pdf', '.png', '.jpeg', '.jpg'
                                ),
                            ));
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
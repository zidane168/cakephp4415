<?php
use Cake\Core\Configure;
// Version: <?= Configure::read('site.version') . " | "  . date('Y-m-d H:i:s')
?>


<div class="row" style="margin-top: 30px">
    <div class="col-md-12">
        <?php echo $this->Html->image('vtl/vtl-logo.svg', [
            'style' => 'width: 40px'
        ]) ?> <?= Configure::read('site.company_name') ?>
    </div>
</div>
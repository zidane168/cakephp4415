<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CidcHoliday $cidcHoliday
 */
?>

<div class="container-fluid card full-border">


    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __d('setting', 'cidc_holiday'); ?> </h3>

                    <?php
                    if (isset($permissions['Cidc Holidays']['edit']) && ($permissions['Cidc Holidays']['edit'] == true)) {
                    ?>

                        <div class="box-tools ml-auto p-1">
                            <?php echo $this->Html->link('<i class="fa fa-pencil"></i> ' . __('edit'), array('action' => 'edit', $cidcHoliday->id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                        </div>

                    <?php
                    }
                    ?>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped">

                        <tr>
                            <th><?= __('id') ?></th>
                            <td><?= h($cidcHoliday->id) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('date') ?></th>
                            <td><?= h(date_format($cidcHoliday['date'], 'Y-m-d')) ?></td>
                        </tr>

                        <?php
                        echo $this->element('admin/filter/common/enabled_info', array(
                            'object' => $cidcHoliday,
                        ));
                        echo $this->element('admin/filter/common/admin_info', array(
                            'object' => $cidcHoliday,
                        ));
                        ?>
                    </table>


                    <div class="text">
                        <strong><?= __('Description') ?></strong>
                        <blockquote>
                            <?= $this->Text->autoParagraph(h($cidcHoliday->description)); ?>
                        </blockquote>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
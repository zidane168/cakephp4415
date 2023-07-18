<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\SystemMessage $systemMessage
 */
?>

<div class="container-fluid card full-border">


    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __d('user', 'system_message'); ?> </h3>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th><?= __('Id') ?></th>
                            <td><?= $this->Number->format($systemMessage->id) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('center', 'cidc_class') ?></th>
                            <td><?= $systemMessage->has('cidc_class_id') ? $this->Html->link($cidcClasses[$systemMessage->cidc_class_id], ['controller' => 'CidcClasses', 'action' => 'view', $systemMessage->cidc_class_id]) : '' ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('parent', 'cidc_parent') ?></th>
                            <td><?= $systemMessage->has('cidc_parent_id') ? $this->Html->link($cidcParents[$systemMessage->cidc_parent_id], ['controller' => 'CidcParents', 'action' => 'view', $systemMessage->cidc_parent_id]) : '' ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('parent', 'kid') ?></th>
                            <td><?= $systemMessage->has('kid_id') ? $this->Html->link($kids[$systemMessage->kid_id], ['controller' => 'Kids', 'action' => 'view', $systemMessage->kid_id]) : '' ?></td>
                        </tr>

                        <tr>
                            <th><?= __d('user', 'read_time') ?></th>
                            <td><?= $systemMessage->read_time ? h($systemMessage->read_time->format('Y-m-d H:i:s')) : null ?></td>
                        </tr>

                        <?php
                        echo $this->element('admin/filter/common/enabled_info', array(
                            'object' => $systemMessage,
                        ));
                        echo $this->element('admin/filter/common/admin_info', array(
                            'object' => $systemMessage,
                        ));
                        ?>
                    </table>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin-top-15">
                                <?=
                                $this->element('content_view', array(
                                    'languages'             => $languages,
                                    'language_input_fields' => $language_input_fields,
                                    // 'images'                 => $images,
                                )); ?>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

</div>
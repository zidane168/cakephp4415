<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\SystemMessage[]|\Cake\Collection\CollectionInterface $systemMessages
 */
?>
<div class="container-fluid card full-border">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->element('admin/filter/system_message_filter', array(
                'data_search' => $data_search
            ));
            ?>
        </div>
    </div>
</div>
<div class="container-fluid card full-border">

    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __d('user', 'system_messages'); ?> </h3>
                </div> <!-- box-header -->

                <div class="box-body table-responsive">
                    <table id="<?php echo str_replace(' ', '', 'System Message'); ?>" class="table table-hover table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th><?= $this->Paginator->sort('SystemMessages.id', __('id')) ?></th>
                                <th><?= $this->Paginator->sort('SystemMessages.cidc_class_id', __d('center', 'cidc_class')) ?></th>
                                <th><?= $this->Paginator->sort('SystemMessages.parent_id', __d('parent', 'cidc_parent')) ?></th>
                                <th><?= $this->Paginator->sort('SystemMessages.kid_id', __d('parent', 'kid')) ?></th>
                                <th><?= $this->Paginator->sort('SystemMessages.read_time', __d('user', 'read_time')) ?></th> 
                                <th><?= $this->Paginator->sort('SystemMessages.created', __('created')) ?></th>
                                <th><?= $this->Paginator->sort('SystemMessages.modified', __('modified')) ?></th>
                                <th><?= __('operation') ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($systemMessages as $systemMessage) : ?>
                                <tr>
                                    <td><?= $this->Number->format($systemMessage->id) ?></td>
                                    <td><?= $systemMessage->has('cidc_class_id') ? $this->Html->link($cidcClasses[$systemMessage->cidc_class_id], ['controller' => 'CidcClasses', 'action' => 'view', $systemMessage->cidc_class_id]) : '' ?></td>
                                    <td><?= $systemMessage->has('cidc_parent_id') ? $this->Html->link($cidcParents[$systemMessage->cidc_parent_id], ['controller' => 'CidcParents', 'action' => 'view', $systemMessage->cidc_parent_id]) : '' ?></td>
                                    <td><?= $systemMessage->has('kid_id') ? $this->Html->link($kids[$systemMessage->kid_id], ['controller' => 'Kids', 'action' => 'view', $systemMessage->kid_id]) : '' ?></td>
                                    <td><?= $systemMessage->read_time ? h($systemMessage->read_time->format('Y-m-d H:i:s')) : null?></td> 
                                    <td><?= h($systemMessage->created) ?></td>
                                    <td><?= h($systemMessage->modified) ?></td>
                                    <td>

                                        <?php
                                        if (isset($permissions['SystemMessages']['view']) && ($permissions['SystemMessages']['view'] == true)) {
                                            echo $this->Html->link('<i class="fa fa-eercast"></i>', array('action' => 'view', $systemMessage->id), array('class' => 'btn btn-primary btn-sm m-r-btn', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => __('view')));
                                        } 

                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?= $this->element('Paginator'); ?>
            </div><!-- box, box-primary -->
        </div><!-- .col-12 -->
    </div><!-- row -->
</div> <!-- container -->
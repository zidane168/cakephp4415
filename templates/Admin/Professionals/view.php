<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Professional $professional
 */
?>

<div class="container-fluid card full-border">


    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __('Professionals'); ?> </h3>

                    <?php
                    if (isset($permissions['Professionals']['edit']) && ($permissions['Professionals']['edit'] == true)) {
                    ?>

                        <div class="box-tools ml-auto p-1">
                            <?php echo $this->Html->link('<i class="fa fa-pencil"></i> ' . __('edit'), array('action' => 'edit', $professional->id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                        </div>

                    <?php
                    }
                    ?>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped">

                        <tr>
                            <th><?= __('Id') ?></th>
                            <td><?= $this->Number->format($professional->id) ?></td>
                        </tr>

                        <tr>
                            <th><?= __d('parent', 'gender') ?></th>
                            <td><?= h($genders[$professional->gender]) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('staff', 'type') ?></th>
                            <td><?= h($types[$professional->type]) ?></td>
                        </tr>

                        <?php
                        echo $this->element('admin/filter/common/enabled_info', array(
                            'object' => $professional,
                        ));
                        echo $this->element('admin/filter/common/admin_info', array(
                            'object' => $professional,
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
                                    'images'                 => $images,
                                )); ?>
                            </div>
                        </div>
                    </div>

                    <?php
                    foreach ($language_certification as $cer) :
                    ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="margin-top-15">
                                    <?=
                                    $this->element('content_view_certification', array(
                                        'languages'             => $cer->professional_certification_languages,
                                        'language_input_fields' => $language_input_fields_certification,
                                        'images'                 => [],
                                    )); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

</div>
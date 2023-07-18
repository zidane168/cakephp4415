<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Kid $kid
 */
?>

<div class="container-fluid card full-border">


    <div class="row">
        <div class="col-12">
            <div class="box box-primary">
                <div class="box-header d-flex">
                    <h3 class="box-title"> <?php echo __('Kids'); ?> </h3>

                    <?php
                    if (isset($permissions['Kids']['edit']) && ($permissions['Kids']['edit'] == true)) {
                    ?>

                        <div class="p-1 ml-auto box-tools">
                            <?php echo $this->Html->link('<i class="fa fa-pencil"></i> ' . __('edit'), array('action' => 'edit', $kid->id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                        </div>

                    <?php
                    }
                    ?>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th><?= __('id') ?></th>
                            <td><?= $this->Number->format($kid->id) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('parent', 'cidc_parent') ?></th>
                            <td><?= $kid->has('cidc_parent') ? $this->Html->link(
                                    $kid->cidc_parent->cidc_parent_languages[0]->name,
                                    [
                                        'controller' => 'CidcParents',
                                        'action' => 'view',
                                        $kid->cidc_parent_id
                                    ]
                                ) : '' ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('setting', 'relationship') ?></th>
                            <td><?= $kid->has('relationship') ? $this->Html->link(
                                    $kid->relationship->relationship_languages[0]->name,
                                    [
                                        'controller' => 'Relationships',
                                        'action' => 'view',
                                        $kid->relationship_id
                                    ]
                                ) : '' ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('parent', 'caretaker') ?></th>
                            <td><?= h($kid->caretaker) ?></td>
                        </tr>

                        <tr>
                            <th><?= __d('parent', 'number_of_siblings') ?></th>
                            <td><?= $this->Number->format($kid->number_of_siblings) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('parent', 'dob') ?></th>
                            <td><?= h($kid->dob) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('parent', 'gender') ?></th>
                            <td><?= h($genders[$kid->gender]) ?></td>
                        </tr>
                        <tr>
                            <th><?= __d('setting', 'emergency_contact') ?></th>
                            <td><?= $kid->has('kids_emergencies') ? $this->Html->link(
                                    $kid->kids_emergencies[0]->emergency_contact->emergency_contact_languages[0]->name . " - " .  $kidsModel->format_phone_number($kid->kids_emergencies[0]->emergency_contact->phone_number),
                                    [
                                        'controller' => 'Emergency_Contacts',
                                        'action'     => 'view',
                                        $kid->kids_emergencies[0]->emergency_contact_id
                                    ]
                                ) : '' ?></td>
                        </tr>
                        <?php
                        echo $this->element('admin/filter/common/enabled_info', array(
                            'object' => $kid,
                        ));
                        echo $this->element('admin/filter/common/admin_info', array(
                            'object' => $kid,
                        ));
                        ?>
                    </table>
                    <div class="text">
                        <strong><?= __('Special Attention Needed') ?></strong>
                        <blockquote>
                            <?= $this->Text->autoParagraph(h($kid->special_attention_needed)); ?>
                        </blockquote>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin-top-15">
                                <?= $this->element('content_view', array(
                                    'languages'             => $languages,
                                    'language_input_fields' => $language_input_fields,
                                    'images'                 => $images,
                                )); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
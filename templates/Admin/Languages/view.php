<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Language $language
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Language'), ['action' => 'edit', $language->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Language'), ['action' => 'delete', $language->id], ['confirm' => __('Are you sure you want to delete # {0}?', $language->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Languages'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Language'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="languages view content">
            <h3><?= h($language->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Alias') ?></th>
                    <td><?= h($language->alias) ?></td>
                </tr>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($language->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($language->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified By') ?></th>
                    <td><?= $this->Number->format($language->modified_by) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created By') ?></th>
                    <td><?= $this->Number->format($language->created_by) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($language->modified) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($language->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Is Default') ?></th>
                    <td><?= $language->is_default ? __('Yes') : __('No'); ?></td>
                </tr>
                <tr>
                    <th><?= __('Enabled') ?></th>
                    <td><?= $language->enabled ? __('Yes') : __('No'); ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

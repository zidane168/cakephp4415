
<tr>
    <th><?= __('created') ?></th>
    <td><?= $this->element('customize_format_datetime', ['date' => $object->created] )
 ?></td>
</tr>
<tr>
    <th><?= __('created_by') ?></th>
    <td><?= isset($object->created_by) && !empty($object->created_by) ? $this->Html->link($object->created_by['name'], ['controller' => 'Administrators', 'action' => 'view', $object->created_by->id]) : '' ?></td>
</tr>
<tr>
    <th><?= __('modified') ?></th>
    <td><?= $this->element('customize_format_datetime', ['date' => $object->modified] ) ?></td>
</tr>
<tr>
    <th><?= __('modified_by') ?></th>
    <td><?= isset($object->modified_by) && !empty($object->modified_by) ? $this->Html->link($object->modified_by['name'], ['controller' => 'Administrators', 'action' => 'view', $object->modified_by->id]) : '' ?></td>
</tr>

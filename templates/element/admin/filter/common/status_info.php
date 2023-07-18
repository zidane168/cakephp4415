<?php

if ($object->status == 1) {
	$text 	= __('checked');
	$style  = "badge badge-pill badge-success";
} else {
	$text 	= __('not_check');
	$style  = "badge badge-pill badge-danger";
}

?>

<tr>
    <th><?= __('status') ?></th>
    <td>
        <label class="<?= $style; ?>"> <?= $text ?> </label>
    </td>
</tr>
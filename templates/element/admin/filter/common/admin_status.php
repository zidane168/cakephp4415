<?php

if ($_check == 1) {
	$text 	= __('checked');
	$style  = "badge badge-pill badge-success";
} else {
	$text 	= __('not_check');
	$style  = "badge badge-pill badge-danger";
}

?>

<label class="<?= $style; ?>"> <?= $text ?> </label>
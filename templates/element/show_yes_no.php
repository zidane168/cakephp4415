<?php

if ($_check == 1) {
	$text 	= __('yes');
	$style  = "badge badge-pill badge-success";
} else {
	$text 	= __('no');
	$style  = "badge badge-pill badge-warning";
}

?>

<label class="<?= $style; ?>"> <?= $text ?> </label>
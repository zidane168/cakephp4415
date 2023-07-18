<?php
	$style  = $_check == 1 ? 'badge badge-pill badge-danger' : 'badge badge-pill badge-success';
    $name   = $_check == 1 ? __('is_used') : __('not_yet_used');
?>
	
<span class="<?= $style ?>"> <?= $name; ?></span>

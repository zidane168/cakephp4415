<?php  
    $msg = __d('cidcclass', 'unpaid');
    $class = ' unpaid';

	if ($_check == true) {
		$msg = __d('cidcclass', 'paid');
		$class = ' paid';
	} 
 ?>
	 
<span class="<?= $class; ?>"> <?= $msg; ?> </span>  

<?php
$msg = __d('cidcclass', 'pending');
$class = ' unpaid';

if ($_check == true) {
    $msg = __d('cidcclass', 'approval');
    $class = ' paid';
}
?>

<span class="<?= $class; ?>"> <?= $msg; ?> </span>
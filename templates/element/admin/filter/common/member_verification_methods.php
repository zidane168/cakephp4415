<?php
$style = "";
$text = $verification_methods[$this->Number->format($memberVerification->verification_method)];
if ($text === 'Sms') {
    $style  = "badge badge-pill badge-success";
    
} elseif ($text === 'Email') {
    $style  = "badge badge-pill badge-info";
}
?>

<label class="<?= $style; ?>"> <?= $text ?> </label>
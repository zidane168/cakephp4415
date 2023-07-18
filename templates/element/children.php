<?php

use Cake\Routing\Router;
?>

<td class="table-kid-parent col-sm-1"><?= h($index + 1 . ".")?></td>
<td class="table-kid-parent col-sm-5">
    <?php
    echo $this->Html->image($path, array(
        "id"    => 'avatar',
        "class" => "image-avatar-kid-parent",
        "alt"   => 'Avatar',
    ));
    // echo ("This")
    ?>
</td>
<td class="table-kid-parent col-sm-3"><?= $this->Html->link($name, [
    'controller' => 'Kids',
    'action'     => 'view',
    $kid_id
])?></td>
<td class="table-kid-parent col-sm-3"><?= h($gender)?></td>
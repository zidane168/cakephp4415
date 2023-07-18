<?php
    $class_parent = $parent ? 'complete' : '';
    $class_kid = $kid ? 'complete' : '';
?>

<ul class="timeline-parent" id="timeline">
    <li class="li <?= $class_parent ?>">
        <div class="timestamp">
            <span class="author"></span>
        </div>
        <div class="status">
            <h4> <?php echo __d('parent', 'add_cidc_parent'); ?></h4>
        </div>
    </li>
    <li class="li <?= $class_kid ?>">
        <div class="timestamp">
            <span class="author"></span>
        </div>
        <div class="status">
            <h4> <?php echo __d('parent', 'add_kid'); ?></h4>
        </div>
    </li>
</ul>
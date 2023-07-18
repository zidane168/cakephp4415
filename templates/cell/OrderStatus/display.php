<?php

    $options = [
        'empty' => __('please_select'),
        'escape' => false,
        'value' => $status, 
        'data-live-search' => true,
        'class' => 'selectpicker form-control',
        'options' => $result, 
    ];
    if ($is_required == true) {
        $options['required'] = true;
        $options['label'] = "<font class='red'> * </font>" . __d('order', 'status');
    } else {
        $options['label'] =  __d('order', 'status');
    } 

    echo $this->Form->control('status', $options); 


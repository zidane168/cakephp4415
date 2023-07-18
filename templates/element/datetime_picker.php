
<div class="form-group datetimepicker">
    <?php
    	$label = (isset($label) ? $label : $field_name);
    	if ($label) {
	    	echo (isset($required) ? '<font class="red">*</font>' : '') .  $this->Form->label($label);
    	}
    	if (!isset($placeholder)) {
    		$placeholder = '';
        }
        $id = isset($id) ? $id : $field_name;  
    ?>
    
    <div class="input-group calendar">
        <div class="input-group-prepend">
            <span class="input-group-text" > 
                <i class="fas fa-calendar-alt"></i> 
            </span>
        </div>
		
        <?php
            $option = array(
				'id' => $id,
                'class' => 'form-control datetimepicker '. (isset($class) ? $class : ''),
				'label' => false,
				'placeholder' => $placeholder,
                'type' => 'text',
                'required' => isset($required) ? $required : false 
            );

            if(isset($value) && $value){
                 $option['value'] = $value; 
            }

			echo $this->Form->input($field_name, $option);
		?>
    </div> 
</div>

<script type="text/javascript">
	$(function () {

        let option = {
            'showClose' : true,
            'format' : "<?= isset($format) ? $format : 'YYYY-MM-DD HH:mm'; ?>",
            'useCurrent': false,
            // 'date': "<?= isset($value) ? $value : '' ?>",
           //  'viewDate': "<?=  isset($value) ? $value : '' ?>"
        };

        <?php 
            if (isset($minDate)) { ?>
                option['minDate'] = new Date();
            
            <?php } if (isset($disabledDates)) { ?>
                option['disabledDates'] = <?= json_encode($disabledDates); ?> 
            
            <?php } 
        ?> 

        $('#<?= $id ?>').datetimepicker(option);
	});

</script>
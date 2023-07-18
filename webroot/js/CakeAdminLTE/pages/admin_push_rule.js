var ADMIN_PUSH_RULE = {
   
    init_page: function(){
        
        // check type
        ADMIN_PUSH_RULE.check_type();

        // init datetime picker range
        COMMON.init_datetimepicker_range($('#period_start'), $('#period_end'));

        // init datetime for instant
        COMMON.init_datetimepicker();
        COMMON.init_timepicker("HH:mm:ss");
    },

    check_type: function() {
        ADMIN_PUSH_RULE.check_type_common();
        $('#push_type_id').on('change',function() {
            ADMIN_PUSH_RULE.check_type_common();
        });
    },

    check_type_common: function() {
        var value   = $('#push_type_id').val();

        $('.push_type_rules').hide().find('input,select').attr('disabled',true);
       
        switch (parseInt(value, 10)) {

            case 1: // 'instant':
                $('.dv-status').hide().find('input').prop('checked', true);
                $('.period_date').hide().find('input,select').attr('disabled',true);
                break;

            case 2: // specific datetime
                $('.specific_date').show().find('input,select').attr('disabled',false);
                $('.period_date').show().find('input,select').attr('disabled',false);
                break;

            case 3: // 'daily':
                $('.execute-time').show().find('input,select').attr('disabled',false);
                $('.period_date').show().find('input,select').attr('disabled',false);
                break;

            default:
                break;
        }
    },
}

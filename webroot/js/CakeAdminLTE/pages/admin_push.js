var ADMIN_PUSH = {
   
    // url_get_members: '',
    message_confirm_push_all: '',
    message_must_choose_member: '',

    clone_zho_to_eng_language: function() {
        $('#PushLanguage0Title').on('keyup',function() {
            $('#PushLanguage1Title').val($('#PushLanguage0Title').val());
        });

        $('#PushLanguage0ShortDescription').on('keyup',function() {
            $('#PushLanguage1ShortDescription').val($('#PushLanguage0ShortDescription').val());
        });

        $('#PushLanguage0LongDescription').on('keyup',function() {
            $('#PushLanguage1LongDescription').val($('#PushLanguage0LongDescription').val());
        });
    },
 
    init_page: function(){

        COMMON.init_element_number($('#txt_phone'));
        
		// init datetime picker range
        COMMON.init_datetimepicker_range($('#period_start'), $('#period_end'));
      
        // init datetime for instant
        COMMON.init_datepicker(false);
        COMMON.init_datetimepicker();
        COMMON.init_timepicker("HH:mm:ss");
        COMMON.init_monthdaypicker();

        // auto input
        ADMIN_PUSH.clone_zho_to_eng_language();

        // check type
        ADMIN_PUSH.check_type();
        
        // push method
        ADMIN_PUSH.check_method();

        $('#confirmSubmission').on('click', function(event) {

            $('.push-to-someone').find('.alert-choose-member').remove();

            var value   = $('#push_method').val();

	        switch (parseInt(value, 10)) {
				// case 1: case 2: case 3:
                //     if ($('#push_group_id').val() == null || $('#push_group_id').val() == "") {
                //         event.preventDefault();
                //        //  alert('MUST fill push group id');
                //     }

		  	    // 	if (confirm(ADMIN_PUSH.message_confirm_push_all)) {
                //         $('#push-form').submit();
                //     }

                //     break;

                case 10: case 11:
                    if ($('#phone').val() == null || $('#phone').val() == "") {
                        $('.push-to-someone').prepend('<div class="alert alert-warning alert-choose-member">' +
	                        '<button type="button" class="close" data-dismiss="alert">×</button>' +
	                        ADMIN_PUSH.message_must_choose_member + '</div>');
                    } else{
                       
                        if (confirm(ADMIN_PUSH.message_confirm_push_all)) {
                            event.preventDefault();
                            $('#push-form').submit();
                        }
                    }
                    break;

				default:
                  
                    if (confirm(ADMIN_PUSH.message_confirm_push_all)) {
                        event.preventDefault();
                        $('#push-form').submit();
                    }
					break;
			}
        });
    },

    check_type: function() {
        ADMIN_PUSH.check_type_common();
        $('#push_type').on('change',function() {
            ADMIN_PUSH.check_type_common();
        });
    },

    check_method: function() {
        ADMIN_PUSH.check_method_common();
		$('#push_method').on('change',function() {
			ADMIN_PUSH.check_method_common();
		});
    },

    // init_autocomplete_member: function(){
    //     $('.member-autocomplete').autocomplete({
    //         delay: 500,
    //         source: function(request, response) {

    //             var data = {
    //                 "text": request.term,
    //                 "member_ids": []
    //             };

    //             $.each($('.txt-member-token'), function(){
    //                 data['member_ids'].push($(this).val());
    //             });

    //             COMMON.call_ajax({
    //                 url: ADMIN_PUSH.url_get_members,
    //                 type: 'POST',
    //                 dataType: 'json',
    //                 data: data,
    //                 success: function(json) {
    //                     if(json.status == true){
    //                         response($.map(json.params, function(item, key) {
    //                             return {
    //                                 label: item,
    //                                 value: parseInt(key)
    //                             }
    //                         }));
    //                     }else{
    //                         return {};
    //                     }
    //                 }
    //             });
    //         }, 
    //         select: function(event, ui) {
    //             $('.member-autocomplete').val('');
    //             $('.list-member-name').append('<span>' + 
    //                 ui.item.label + '<i class="fa fa-remove"></i>' +
    //                 '<input type="hidden" name="data[Push][member_token][]" value="' + ui.item.value + '" class="txt-member-token" />' +
    //             '</span>');
    //             $('.push-to-someone').find('.alert-choose-member').remove();
    //             ADMIN_PUSH.init_remove_member();
    //             return false;
    //         },
    //         focus: function(event, ui) {
    //             return false;
    //         }
    //     });
    // },

    // init_remove_member: function(){
    //     $('.list-member-name i').on('click', function(){
    //         $(this).parent().remove();
    //     });
    // },

    check_type_common: function() {
        var value   = $('#push_type').val();

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

    check_method_common: function(){
        var value = $('#push_method').val();

        $('.push-to-someone').hide().find('.list-member-name').html('');
        // $('.push-by-criteria').hide().find('input,select,textarea').val(''); // BUUUUUG when user click to submit -> will call this again so the value is null
        $('.push-by-criteria').find('input[type=checkbox]').prop('checked', false);

        switch (parseInt(value, 10)) 
        {
            case 10: case 11: // 'push-to-someone':
                $('.push-to-someone').show();
                console.log('show some one');
                break;

            case 20: // 'push-by-criteria':
                $('.push-by-criteria').show();
                break;

            case 1: case 2: case 3:    // 'push-to-all':
                break;

            default:
                break;
        }
    },
    
}
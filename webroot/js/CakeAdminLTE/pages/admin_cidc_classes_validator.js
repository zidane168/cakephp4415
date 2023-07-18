
$(document).ready(function() {
    $('#cidcClass').validate({
        rules : {
            end_date : { endDateGreaterThanStartDate : true }, 
            end_time : { endTimeGreaterThanStartTime : true },
            maximum_of_students : { maximumStudent : true },
            target_audience_to : { targetAudienceTo : true} 
        },  
    });

    $.extend($.validator.messages, {
        required: lang.this_field_is_required,
    });

    $.validator.addMethod("endDateGreaterThanStartDate", function(value, element){
        let start_date = $('#start_date').val(); 
        return Date.parse(start_date) <= Date.parse(value);
    }, lang.end_date_should_be_greater_than_equal_to_start_date); 

    $.validator.addMethod("endTimeGreaterThanStartTime", function(value, element){
        let start_time_value = $('#start_time').val();
        let end_time_value =  value;
        let stime   = start_time_value.split(":", -1);
        let sHour   = stime[0];
        let sMinute = stime[1];

        let etime   = end_time_value.split(":", -1);
        let eHour   = etime[0];
        let eMinute = etime[1]; 

        const start_time    = moment({hour: sHour, minute: sMinute})
        const end_time      = moment({hour: eHour, minute: eMinute}) 

        return moment(start_time).valueOf() <= moment(end_time).valueOf()
    }, lang.end_time_should_be_greater_than_equal_to_start_time ); 

    $.validator.addMethod("maximumStudent", function(value, element) {
        let minimum_of_students = $('#minimum_of_students').val();   
        return parseInt(minimum_of_students) <= parseInt(value)
    }, lang.max_should_be_greater_than_equal_to_min ) 

    $.validator.addMethod("targetAudienceTo", function(value, element) {
        let target_audience_from = $('#target_audience_from').val();   
        return parseInt(target_audience_from) <= parseInt(value)
    }, lang.max_should_be_greater_than_equal_to_min ) 

    // $.validator.addMethod("minimumStudent", function(value, element){
    //     let maximum_of_students = $('#maximum_of_students').val();   
    //     return value <= maximum_of_students
    // }, lang.max_should_be_greater_than_equal_to_min ) 
})
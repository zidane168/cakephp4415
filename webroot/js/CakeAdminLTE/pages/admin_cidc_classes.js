var ADMIN_CIDC_CLASSES = {
    url_get_course_by_program: "",
    current_language: "zh_HK",

    init: function () {
        ADMIN_CIDC_CLASSES.init_select_category();

        $('#number-of-lessons').on('change', function() {
            if ($('#number-of-lessons').val() > 1000) {
                alert (lang.limit_number_of_lessons);
                $('#number-of-lessons').val(1);
            }
        })
    },
 
    submit: function() {
        $('#submit').click(function() {
            checked = $("input[type=checkbox]:checked").length;
      
            if (!checked) {
              alert("You must check at least one checkbox.");
              return false;
            } 
        });
    },

    init_select_category: function () {
        $("#program_id").on("change", function () {
            console.log($('#program_id').val())
            COMMON.call_ajax({
                url: ADMIN_CIDC_CLASSES.url_get_course_by_program,
                type: "GET", 
                data: {  
                    id: $('#program_id').val(),
                    language:  ADMIN_CIDC_CLASSES.current_language,
                },
                dataType: "json",
                success: function (result) {
                    let status = result.status;
                    let params = result.params;

                    let html_options = "<option value=''>" + $("#course_id").find("option").first().text() +  "</option>";
                    if (status === true) {
                        for (let index in params) {
                            html_options += "<option value=" +  index +  ">" +  params[index] +  "</option>";
                        }
                    }
                    $("#course_id").html(html_options);
                    $("#course_id").selectpicker("refresh");
                },
                error: function (error) {
                    alert("Get course_id data error!");
                },
            });
        });
    },
 
};

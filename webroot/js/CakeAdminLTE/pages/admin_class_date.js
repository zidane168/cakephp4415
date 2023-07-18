var ADMIN_CLASS_DATE = {
  url_get_date_by_class_id: "",
  current_language: "",
  init_page: function () {
    ADMIN_CLASS_DATE.init_select_class();
  },

  init_select_class: function () {
    $("#cidc_class_id").on("change", function () {
      COMMON.call_ajax({
        url: ADMIN_CLASS_DATE.url_get_date_by_class_id,
        type: "GET",
        data: {
          cidc_class_id: $(this).val(),
          language: ADMIN_CLASS_DATE.current_language,
        },
        dataType: "json",
        success: function (result) {
          let status = result.status;
          let params = result.params;

          let html_options =
            "<option value = ''>" +
            $("#date_id").find("option").first().text() +
            "</option>";
          if (status == true) {
            for (let index of params["dates"]) {
              html_options +=
                "<option value=" + index + ">" + index + "</option>";
            }
          }
          $("#date_id").html(html_options);
          $("#date_id").selectpicker("refresh"); 
          $("#cidc_class_id_view").text(params["id"]);
          $("#cidc_class_name_view").text(params["name"]);
          $("#cidc_class_code_view").text(params["code"]);
          $("#program_view").text(params["program"]);
          $("#course_view").text(params["course"]);
          $("#center_view").text(params["center"]);
          $("#class_type_view").text(params["class_type"]);
          $("#target_audience_view").text(params["target_audience"]);
          $("#min_max_students_view").text(params["min_max_students"]);
          $("#number_of_register_view").text(params["number_of_register"]);
          $("#number_of_lessons_view").text(params["number_of_lessons"]);
          $("#date_view").text(params["date"]);
          $("#time_view").text(params["time"]);
        },
        error: function (error) {
          alert("Get date data error!");
        },
      });
    });
  },
};

let ADMIN_STUDENT_REGISTER_CLASS = {
  url_get_detail_class_by_id: "",
  current_language: "", 
  url_get_list_kids_no_register_class: "",   

  init: function () {  
      document.getElementById('cidc_class_info').style.display = 'none';

      $('#cidc_class_id').on('change', function() {
        ADMIN_STUDENT_REGISTER_CLASS.get_detail_class_by_id();
        ADMIN_STUDENT_REGISTER_CLASS.get_list_kids_no_register_class();
      });
  },   

  get_detail_class_by_id: function () {
    COMMON.call_ajax({
      url: ADMIN_STUDENT_REGISTER_CLASS.url_get_detail_class_by_id,
      type: "POST",
      data: {
        id: $("#cidc_class_id").val(),
        language: ADMIN_STUDENT_REGISTER_CLASS.current_language,
      },
      dataType: "json",
      success: function (result) {
        let status = result.status;
        let params = result.params;

        if (status == true) {
          if ($("#cidc_class_id").val() == "") {
            $("#cidc_class_info").html("");
            $("#cidc_class_info").css("display", "none");
          } else {
            let html = COMMON.display_class_info(params);
            document.getElementById("cidc_class_info").style.display = "block";
            $("#cidc_class_info").html(html);
          }
        }
      },
      error: function (error) {
        alert("Get detail class by id!");
      },
    });
  },

  get_list_kids_no_register_class: function () {
    COMMON.call_ajax({
      url: ADMIN_STUDENT_REGISTER_CLASS.url_get_list_kids_no_register_class,
      type: "POST",
      data: {
        id: $("#cidc_class_id").val(),
        language: ADMIN_STUDENT_REGISTER_CLASS.current_language,
      },
      dataType: "json",
      success: function (result) {
        let status = result.status;
        let params = result.params;

        let html_options =
          "<option value=''>" +
          $("#kid_id").find("option").first().text() +
          "</option>";

        if (status == true) {
          for (let index in params) {
            html_options +=
              "<option value=" + index + ">" + params[index] + "</option>";
          }
        }

        $("#kid_id").html(html_options);
        $("#kid_id").selectpicker("refresh");
      },
      error: function (error) {
        console.log(error);
        alert("Get student no register error!");
      },
    });
  },
};

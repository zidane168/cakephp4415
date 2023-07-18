"use strict";

var ADMIN_PATIENT = {
  url_get_district_data: "",
  current_language: "",
  init_page: function init_page() {
    ADMIN_PATIENT.init_select_category();
  },
  init_select_category: function init_select_category() {
    $("#region_id").on("change", function () {
      COMMON.call_ajax({
        url: ADMIN_PATIENT.url_get_district_data,
        type: "GET",
        data: {
          id: $(this).val(),
          language: ADMIN_PATIENT.current_language
        },
        dataType: "json",
        success: function success(result) {
          var status = result.status;
          var params = result.params;
          var html_options = "<option value=''>" + $("#district_id").find("option").first().text() + "</option>";

          if (status === true) {
            for (var index in params) {
              html_options += "<option value=" + index + ">" + params[index] + "</option>";
            }
          }

          $("#district_id").html(html_options);
          $("#district_id").selectpicker("refresh");
        },
        error: function error(_error) {
          alert("Get district_id data error!");
        }
      });
    });
  }
};
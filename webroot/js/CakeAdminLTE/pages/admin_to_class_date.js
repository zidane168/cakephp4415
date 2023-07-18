var ADMIN_TO_ADMIN_CLASS_DATE_INFO = {
  url_get_date_by_class_id: "",
  url_get_detail_class_by_id: "",
  current_language: "",
  init_page: function () {
    document.getElementById("to_cidc_class_info").style.display = "none";
    $("#to_cidc_class_id").on("change", function () {
      ADMIN_TO_ADMIN_CLASS_DATE_INFO.get_detail_class_by_id();
      ADMIN_TO_ADMIN_CLASS_DATE_INFO.get_dates_by_class_id();
    });
  },
  init_edit: function () {
    document.getElementById("to_cidc_class_info").style.display = "block";
    ADMIN_TO_ADMIN_CLASS_DATE_INFO.get_detail_class_by_id();

    $("#to_cidc_class_id").on("change", function () {
      ADMIN_TO_ADMIN_CLASS_DATE_INFO.get_detail_class_by_id();
      ADMIN_TO_ADMIN_CLASS_DATE_INFO.get_dates_by_class_id();
    });
  },

  get_dates_by_class_id: function () {
    COMMON.call_ajax({
      url: ADMIN_TO_ADMIN_CLASS_DATE_INFO.url_get_date_by_class_id,
      type: "GET",
      data: {
        cidc_class_id: $("#to_cidc_class_id").val(),
        language: ADMIN_TO_ADMIN_CLASS_DATE_INFO.current_language,
      },
      dataType: "json",
      success: function (result) {
        let status = result.status;
        let params = result.params;

        let html_options =
          "<option value = ''>" +
          $("#to_date_id").find("option").first().text() +
          "</option>";
        if (status == true) {
          for (let index of params["dates"]) {
            html_options +=
              "<option value=" + index + ">" + index + "</option>";
          }
        }
        $("#to_date_id").html(html_options);
        $("#to_date_id").selectpicker("refresh");
      },
      error: function (error) {
        alert("Get date data error!");
      },
    });
  },
  get_detail_class_by_id: function () {
    COMMON.call_ajax({
      url: ADMIN_TO_ADMIN_CLASS_DATE_INFO.url_get_detail_class_by_id,
      type: "POST",
      data: {
        id: $("#to_cidc_class_id").val(),
        language: ADMIN_TO_ADMIN_CLASS_DATE_INFO.current_language,
      },
      dataType: "json",
      success: function (result) {
        let status = result.status;
        let params = result.params;

        if (status == true) {
          if ($("#to_cidc_class_id").val() == "") {
            $("#to_cidc_class_info").html("");
            $("#to_cidc_class_info").css("display", "none");
          } else {
            let html = `<div class="flex"> ${lang.cidc_class.name}: ${params.name} - ${params.code} </div>`;
            html += `<div class="flex"> ${lang.cidc_class.description}: ${params.description}  </div>`;
            html += `<div class="flex"> ${lang.cidc_class.fee}: ${params.fee}  </div>`;
            html += `<div class="flex"> ${lang.cidc_class.number_of_register}: ${params.number_of_register}  </div>`;
            html += `<div class="flex"> ${lang.cidc_class.min_max_student}: ${params.minimum_of_students} -  ${params.maximum_of_students} </div>`;
            html += `<div class="flex"> ${lang.cidc_class.number_of_lessons}: ${params.number_of_lessons}  </div>`;
            html += `<div class="flex"> ${lang.cidc_class.date}: ${params.date}  </div>`;
            html += `<div class="flex"> ${lang.cidc_class.time}: ${params.time}  </div>`;
            document.getElementById("to_cidc_class_info").style.display =
              "block";
            $("#to_cidc_class_info").html(html);
          }
        }
      },
      error: function (error) {
        alert("Get detail class by id!");
      },
    });
  },
};

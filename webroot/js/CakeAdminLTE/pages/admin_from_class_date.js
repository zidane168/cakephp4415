var ADMIN_FROM_ADMIN_CLASS_DATE_INFO = {
  url_get_date_by_class_id: "",
  url_get_detail_class_by_id: "",
  url_get_list_kids_register_class: "",
  current_language: "",
  init_page: function () {
    document.getElementById("from_cidc_class_info").style.display = "none";
    $("#from_cidc_class_id").on("change", function () {
      ADMIN_FROM_ADMIN_CLASS_DATE_INFO.get_detail_class_by_id();
      ADMIN_FROM_ADMIN_CLASS_DATE_INFO.get_dates_by_class_id();
      ADMIN_FROM_ADMIN_CLASS_DATE_INFO.get_list_kids_register_class();
    });
  },
  init_edit: function () {
    document.getElementById("from_cidc_class_info").style.display = "block";
    ADMIN_FROM_ADMIN_CLASS_DATE_INFO.get_detail_class_by_id();

    $("#from_cidc_class_id").on("change", function () {
      ADMIN_FROM_ADMIN_CLASS_DATE_INFO.get_detail_class_by_id();
      ADMIN_FROM_ADMIN_CLASS_DATE_INFO.get_dates_by_class_id();
      ADMIN_FROM_ADMIN_CLASS_DATE_INFO.get_list_kids_register_class();
    });
  },

  get_dates_by_class_id: function () {
    COMMON.call_ajax({
      url: ADMIN_FROM_ADMIN_CLASS_DATE_INFO.url_get_date_by_class_id,
      type: "GET",
      data: {
        cidc_class_id: $("#from_cidc_class_id").val(),
        language: ADMIN_FROM_ADMIN_CLASS_DATE_INFO.current_language,
      },
      dataType: "json",
      success: function (result) {
        let status = result.status;
        let params = result.params;

        let html_options =
          "<option value = ''>" +
          $("#from_date_id").find("option").first().text() +
          "</option>";
        if (status == true) {
          for (let index of params["dates"]) {
            html_options +=
              "<option value=" + index + ">" + index + "</option>";
          }
        }
        $("#from_date_id").html(html_options);
        $("#from_date_id").selectpicker("refresh");
      },
      error: function (error) {
        alert("Get date data error!");
      },
    });
  },
  get_detail_class_by_id: function () {
    COMMON.call_ajax({
      url: ADMIN_FROM_ADMIN_CLASS_DATE_INFO.url_get_detail_class_by_id,
      type: "POST",
      data: {
        id: $("#from_cidc_class_id").val(),
        language: ADMIN_FROM_ADMIN_CLASS_DATE_INFO.current_language,
      },
      dataType: "json",
      success: function (result) {
        let status = result.status;
        let params = result.params;

        if (status == true) {
          if ($("#from_cidc_class_id").val() == "") {
            $("#from_cidc_class_info").html("");
            $("#from_cidc_class_info").css("display", "none");
          } else {
            let html = `<div class="flex"> ${lang.cidc_class.name}: ${params.name} - ${params.code} </div>`;
            html += `<div class="flex"> ${lang.cidc_class.description}: ${params.description}  </div>`;
            html += `<div class="flex"> ${lang.cidc_class.fee}: ${params.fee}  </div>`;
            html += `<div class="flex"> ${lang.cidc_class.number_of_register}: ${params.number_of_register}  </div>`;
            html += `<div class="flex"> ${lang.cidc_class.min_max_student}: ${params.minimum_of_students} -  ${params.maximum_of_students} </div>`;
            html += `<div class="flex"> ${lang.cidc_class.number_of_lessons}: ${params.number_of_lessons}  </div>`;
            html += `<div class="flex"> ${lang.cidc_class.date}: ${params.date}  </div>`;
            html += `<div class="flex"> ${lang.cidc_class.time}: ${params.time}  </div>`;
            document.getElementById("from_cidc_class_info").style.display =
              "block";
            $("#from_cidc_class_info").html(html);
          }
        }
      },
      error: function (error) {
        console.log(error);
        alert("Get detail class by id!");
      },
    });
  },
  get_list_kids_register_class: function () {
    COMMON.call_ajax({
      url: ADMIN_FROM_ADMIN_CLASS_DATE_INFO.url_get_list_kids_register_class,
      type: "POST",
      data: {
        id: $("#from_cidc_class_id").val(),
        language: ADMIN_FROM_ADMIN_CLASS_DATE_INFO.current_language,
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

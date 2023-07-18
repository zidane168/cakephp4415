var ADMIN_SICK_LEAVE = {
  url_get_kids_register_by_class_id: "",
  url_get_detail_class_by_id: "",
  current_language: "",
  url_get_date_by_class_id: "",
  init: function () {
    document.getElementById("cidc_class_info").style.display = "none";
    $("#cidc_class_id").on("change", function () {
      ADMIN_SICK_LEAVE.get_detail_class_by_id();
      ADMIN_SICK_LEAVE.get_kids_register_by_class_id();
      ADMIN_SICK_LEAVE.get_dates_by_class_id();
    });
  },
  edit_init: function () {
    ADMIN_SICK_LEAVE.get_detail_class_by_id();
    $("#cidc_class_id").on("change", function () {
      ADMIN_SICK_LEAVE.get_detail_class_by_id();
      ADMIN_SICK_LEAVE.get_kids_register_by_class_id();
      ADMIN_SICK_LEAVE.get_dates_by_class_id();
    });
  },
  get_kids_register_by_class_id: function () {
    COMMON.call_ajax({
      url: ADMIN_SICK_LEAVE.url_get_kids_register_by_class_id,
      type: "POST",
      data: {
        id: $("#cidc_class_id").val(),
        language: ADMIN_SICK_LEAVE.current_language,
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
        alert("Get student register error!");
      },
    });
  },
  get_detail_class_by_id: function () {
    COMMON.call_ajax({
      url: ADMIN_SICK_LEAVE.url_get_detail_class_by_id,
      type: "POST",
      data: {
        id: $("#cidc_class_id").val(),
        language: ADMIN_SICK_LEAVE.current_language,
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
            let html = `<div class="flex"> ${lang.cidc_class.name}: ${params.name} - ${params.code} </div>`;
            html += `<div class="flex"> ${lang.cidc_class.description}: ${params.description}  </div>`;
            html += `<div class="flex"> ${lang.cidc_class.fee}: ${params.fee}  </div>`;
            html += `<div class="flex"> ${lang.cidc_class.number_of_register}: ${params.number_of_register}  </div>`;
            html += `<div class="flex"> ${lang.cidc_class.min_max_student}: ${params.minimum_of_students} -  ${params.maximum_of_students} </div>`;
            html += `<div class="flex"> ${lang.cidc_class.number_of_lessons}: ${params.number_of_lessons}  </div>`;
            html += `<div class="flex"> ${lang.cidc_class.date}: ${params.date}  </div>`;
            html += `<div class="flex"> ${lang.cidc_class.time}: ${params.time}  </div>`;
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
  get_dates_by_class_id: function () {
    console.log(ADMIN_SICK_LEAVE.url_get_date_by_class_id);
    COMMON.call_ajax({
      url: ADMIN_SICK_LEAVE.url_get_date_by_class_id,
      type: "GET",
      data: {
        cidc_class_id: $("#cidc_class_id").val(),
        current_cidc_class_id: $("#cidc_class_id").val(),
      },
      dataType: "json",
      success: function (result) {
        let status = result.status;
        let params = result.params;

        let html_options =
          "<option value = ''>" +
          $("#date_id").find("option").first().text() +
          "</option>";
        if (status == 200) {
          for (let index of params["dates"]) {
            html_options +=
              "<option value=" + index + ">" + index + "</option>";
          }
        }
        $("#date_id").html(html_options);
        $("#date_id").selectpicker("refresh");
      },
      error: function (error) {
        alert("Get date data error!");
      },
    });
  },
};

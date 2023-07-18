var ADMIN_RESCHEDULE = {
  url_get_date_by_class_id: "",
  url_get_detail_class_by_id: "",
  url_get_register_classes_by_kid_id: "",
  url_get_class_by_from_class_id: "",
  url_get_dates_by_class_kid_id: "",
  current_language: "",
  init_page: function () {
    document.getElementById("to_cidc_class_info").style.display = "none";
    document.getElementById("from_cidc_class_info").style.display = "none";
    $("#kid_id").on("change", function () {
      ADMIN_RESCHEDULE.get_register_class_by_id();
    });
    $("#from_cidc_class_id").on("change", function () {
      ADMIN_RESCHEDULE.get_detail_from_class_by_id();
      ADMIN_RESCHEDULE.get_dates_by_from_class_id();
      ADMIN_RESCHEDULE.get_classes_by_from_class_id();
    });
    $("#to_cidc_class_id").on("change", function () { 
      ADMIN_RESCHEDULE.get_detail_to_class_by_id();
      ADMIN_RESCHEDULE.get_dates_by_to_class_id();
    });
  },
  edit_page: function () {
    document.getElementById("to_cidc_class_info").style.display = "none";
    document.getElementById("from_cidc_class_info").style.display = "none";
    ADMIN_RESCHEDULE.get_detail_from_class_by_id();
    ADMIN_RESCHEDULE.get_detail_to_class_by_id();
    $("#kid_id").on("change", function () {
      ADMIN_RESCHEDULE.get_register_class_by_id();
    });
    $("#from_cidc_class_id").on("change", function () {
      ADMIN_RESCHEDULE.get_detail_from_class_by_id();
      ADMIN_RESCHEDULE.get_dates_by_from_class_id();
      ADMIN_RESCHEDULE.get_classes_by_from_class_id();
    });
    $("#to_cidc_class_id").on("change", function () { 
      ADMIN_RESCHEDULE.get_detail_to_class_by_id();
      ADMIN_RESCHEDULE.get_dates_by_to_class_id();
    });
  },
  //   init_edit: function () {
  //     document.getElementById("to_cidc_class_info").style.display = "block";
  //     ADMIN_RESCHEDULE.get_detail_class_by_id();

  //     $("#to_cidc_class_id").on("change", function () {
  //       ADMIN_RESCHEDULE.get_detail_class_by_id();
  //       ADMIN_RESCHEDULE.get_dates_by_class_id();
  //     });
  //   },

  get_dates_by_to_class_id: function () {
    COMMON.call_ajax({
      url: ADMIN_RESCHEDULE.url_get_date_by_class_id,
      type: "GET",
      data: {
        cidc_class_id: $("#to_cidc_class_id").val(),
        current_cidc_class_id: $("#from_cidc_class_id").val(),
      },
      dataType: "json",
      success: function (result) {
        let status = result.status;
        let params = result.params;

        let html_options =
          "<option value = ''>" +
          $("#to_date_id").find("option").first().text() +
          "</option>";
        if (status == 200) {
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

  get_dates_by_from_class_id: function () {
    COMMON.call_ajax({
      url: ADMIN_RESCHEDULE.url_get_dates_by_class_kid_id,
      type: "GET",
      data: {
        cidc_class_id: $("#from_cidc_class_id").val(),
        kid_id: $("#kid_id").val(),
      },
      dataType: "json",
      success: function (result) {
        let status = result.status;
        let params = result.params;

        let html_options =
          "<option value = ''>" +
          $("#from_date_id").find("option").first().text() +
          "</option>";
        if (status == 200) { 
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
  get_detail_to_class_by_id: function () {
    COMMON.call_ajax({
      url: ADMIN_RESCHEDULE.url_get_detail_class_by_id,
      type: "POST",
      data: {
        id: $("#to_cidc_class_id").val(),
        language: ADMIN_RESCHEDULE.current_language,
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
            let html = COMMON.display_class_info(params);
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

  get_detail_from_class_by_id: function () { 
    COMMON.call_ajax({
      url: ADMIN_RESCHEDULE.url_get_detail_class_by_id,
      type: "POST",
      data: {
        id: $("#from_cidc_class_id").val(),
        language: ADMIN_RESCHEDULE.current_language,
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
            let html = COMMON.display_class_info(params);
            document.getElementById("from_cidc_class_info").style.display =
              "block";
            $("#from_cidc_class_info").html(html);
          }
        }
      },
      error: function (error) {
        alert("Get detail class by id!");
      },
    });
  },
  get_register_class_by_id: function () {
    COMMON.call_ajax({
      url: ADMIN_RESCHEDULE.url_get_register_classes_by_kid_id,
      type: "GET",
      data: {
        kid_id: $("#kid_id").val(),
        language: ADMIN_RESCHEDULE.current_language,
      },
      dataType: "json",
      success: function (result) {
        let status = result.status;
        let params = result.params;

        let html_options =
          "<option value = ''>" +
          $("#from_cidc_class_id").find("option").first().text() +
          "</option>";
        if (status == true) {
          for (let index in params) {
            html_options +=
              "<option value=" + index + ">" + params[index] + "</option>";
          }
        }
        $("#from_cidc_class_id").html(html_options);
        $("#from_cidc_class_id").selectpicker("refresh");
      },
      error: function (error) {
        alert("Get date data error!");
      },
    });
  },
  get_classes_by_from_class_id: function () { 
    COMMON.call_ajax({
      url: ADMIN_RESCHEDULE.url_get_class_by_from_class_id,
      type: "GET",
      data: {
        from_cidc_class_id: $("#from_cidc_class_id").val(),
        language: ADMIN_RESCHEDULE.current_language,
      },
      dataType: "json",
      success: function (result) {
        let status = result.status;
        let params = result.params;

        let html_options =
          "<option value = ''>" +
          $("#to_cidc_class_id").find("option").first().text() +
          "</option>";
        if (status == true) {
          for (let index in params) {
            html_options +=
              "<option value=" + index + ">" + params[index] + "</option>";
          }
        }
        $("#to_cidc_class_id").html(html_options);
        $("#to_cidc_class_id").selectpicker("refresh");
      },
      error: function (error) {
        alert("Get date data error!");
      },
    });
  },
};

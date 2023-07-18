var ADMIN_COMPANY = {
  url_index: "",
  url_get_item_detail: "",
  url_upgrade_item_detail: "",
  data_change_status: {},
  message_confirm: "",
  is_have_pending: 0,
  init_page: function () {
    COMMON.init_validate_form_tabs($("#btn-submit-data"));
  },
  init_edit_form: function () {
    $("#company-edit-form").on("submit", function (event) {
      if (ADMIN_COMPANY.is_have_pending == 1) {
        var result = confirm(
          ADMIN_COMPANY.message_confirm_continuous_edit_pending
        );
        if (result == false) {
          if ($("#btn-submit-data").length) {
            $("#btn-submit-data").removeAttr("disabled");
          }
          event.preventDefault();
        }
      }
    });
  },
  init_detail_page: function () {
    var hashtag = window.location.hash.substr(1);
    if (hashtag) {
      $('a[href="#' + hashtag + '"]').trigger("click");
    }

    $(".btn-approve")
      .off()
      .on("click", function () {
        ADMIN_COMPANY.data_change_status = {
          id: $(this).data("id"),
          detail_id: $(this).data("detail-id"),
          status: 1,
        };

        ADMIN_COMPANY.init_confirm_change_status("Approve");
      });

    $(".btn-reject")
      .off()
      .on("click", function () {
        ADMIN_COMPANY.data_change_status = {
          id: $(this).data("id"),
          detail_id: $(this).data("detail-id"),
          status: 4,
        };

        ADMIN_COMPANY.init_confirm_change_status("Reject");
      });

    $(".btn-view-detail").on("click", function () {
      var id = $(this).data("id");

      COMMON.call_ajax({
        url: ADMIN_COMPANY.url_get_item_detail + "/" + id,
        type: "GET",
        dataType: "text",
        success: function (result) {
          $(".company-detail-modal .modal-body").html(result);
          $(".company-detail-modal").modal("show");
          ADMIN_COMPANY.init_action_popup_view_detail();
        },
        error: function (error) {
          alert("Get data for company detail is error!");
        },
      });
    });
  },
  init_action_popup_view_detail: function () {
    $(".btn-approve-modal")
      .off()
      .on("click", function () {
        ADMIN_COMPANY.data_change_status = {
          id: $(this).data("id"),
          detail_id: $(this).data("detail-id"),
          status: 1,
        };

        ADMIN_COMPANY.init_confirm_change_status("Approve");
      });

    $(".btn-reject-modal")
      .off()
      .on("click", function () {
        ADMIN_COMPANY.data_change_status = {
          id: $(this).data("id"),
          detail_id: $(this).data("detail-id"),
          status: 4,
        };

        ADMIN_COMPANY.init_confirm_change_status("Reject");
      });
  },
  init_confirm_change_status: function (action) {
    var message = ADMIN_COMPANY.message_confirm.replace("[action]", action);
    $(".confirm-change-status-modal .modal-body h3").text(message);
    $(".confirm-change-status-modal").modal("show");

    $(".btn-confirm-yes")
      .off()
      .on("click", function () {
        COMMON.call_ajax({
          url:
            ADMIN_COMPANY.url_upgrade_item_detail +
            "/" +
            ADMIN_COMPANY.data_change_status.id,
          type: "POST",
          data: {
            company_detail_id: ADMIN_COMPANY.data_change_status.detail_id,
            status: ADMIN_COMPANY.data_change_status.status,
          },
          dataType: "json",
          success: function (result) {
            if (typeof result.status != "undefined") {
              alert(result.params.message);
              if (result.status === true) {
                window.location.href = ADMIN_COMPANY.url_index;
              }
            } else {
              alert(action + " this company was FAILED!");
            }
          },
          error: function (error) {
            alert(action + " this company was FAILED!");
          },
        });
      });
  },
};

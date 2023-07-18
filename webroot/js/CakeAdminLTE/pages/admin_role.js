// vilh (2019/03/25)
// vilh (2021/05/24)
// Slide up / slide down for panel role permission

// Get the button

// ---- scroll to top -----
var mybutton = document.getElementById("myBtn");

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function () {
    scrollFunction();
};

function scrollFunction() {
    if (
        document.body.scrollTop > 500 ||
        document.documentElement.scrollTop > 500
    ) {
        mybutton.style.display = "block";
    } else {
        mybutton.style.display = "none";
    }
}

// When the user clicks on the button, scroll to the top of the document
function scrollWin() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
}
// ---- scroll to top -----

$(document).ready(function () {
    $(".my-box").each(function (i, v) {
        var box = $(this).children(".box").first();

        // Find the body and the footer
        var bf = box.find(".card-body, .box-footer");

        if (!box.hasClass("collapsed-box")) {
            box.addClass("collapsed-box");

            // Convert minus into plus
            $(this)
                .children(".fa-minus")
                .removeClass("fa-minus")
                .addClass("fa-plus");
            bf.slideUp();
        } else {
            box.removeClass("collapsed-box");

            // Convert plus into minus
            $(this)
                .children(".fa-plus")
                .removeClass("fa-plus")
                .addClass("fa-minus");
            bf.slideDown();
        }

        var is_all = true;
        $.each($(this).find(".chk-permission-id"), function (key, item) {
            if (!$(item).is(":checked")) {
                is_all = false;
                return;
            }
        });

        if (is_all) {
            //  $(this).find('.chk-all-permission').iCheck('check');
            $(this).find(".chk-permission-id").prop("checked", true); // replace iCheck (jquery 2.2.1) to prop('checked', true) (jquery 3.5.1)
        }
    });

    // collapse -> +
    $("#collapse").click(function () {
        $(".my-box").each(function (i, v) {
            var box = $(this).children(".box").first();

            // Find the body and the footer
            var bf = box.find(".card-body, .box-footer");

            if (!box.hasClass("collapsed-box")) {
                box.addClass("collapsed-box");
                var plus = box.find(".fa-minus");
                plus.removeClass("fa-minus").addClass("fa-plus");
                bf.slideUp();
            }
        });
    });

    // expand -> -
    $("#expand").click(function () {
        $(".my-box").each(function (i, v) {
            var box = $(this).children(".box").first();

            // Find the body and the footer
            var bf = box.find(".card-body, .box-footer");

            if (box.hasClass("collapsed-box")) {
                box.removeClass("collapsed-box");
                var plus = box.find(".fa-plus");
                plus.removeClass("fa-plus").addClass("fa-minus");
                bf.slideDown();
            }
        });
    });

    // init input check top is checkall
    $("input.chk-all-permission").change(function (event) {
        if ($(this).is(":checked")) {
            $(this)
                .closest("table")
                .find(".chk-permission-id")
                .prop("checked", true); // replace iCheck (jquery 2.2.1) to prop('checked', true) (jquery 3.5.1)
        } else {
            $(this)
                .closest("table")
                .find(".chk-permission-id")
                .prop("checked", false);
        }
    });

    //$(".fa-minus").on("click", function(event) {
    $(document).on("click", ".fa-minus", function () {
        // get .box parent
        box = $(this).closest(".card");

        // change minus => plus and collapsed
        var body = box.find(".card-body, .card-footer");

        console.log("Click into minus");
        if (!box.hasClass("collapsed-box")) {
            box.addClass("collapsed-box");
            var plus = box.find(".fa-minus");
            // plus.attr("class", "fa fa-plus");
            plus.removeClass("fa-minus").addClass("fa-plus");
            body.slideUp();
        }
    });

    $(document).on("click", ".fa-plus", function () {
        console.log("Click into PLUS");
        // get .box parent
        box = $(this).closest(".card");

        // change minus => plus and collapsed
        var body = box.find(".card-body, .card-footer");
        if (box.hasClass("collapsed-box")) {
            console.log("+");
            box.removeClass("collapsed-box");
            var plus = box.find(".fa-plus");
            plus.removeClass("fa-plus").addClass("fa-minus");
            body.slideDown();
        }
    });

    $(".fa-plus").on("click", function (event) {
        // cannot use this because after removeClass the document NOT YET update, so dont know which element is
    });

    /// jquery 2.2.1
    // $('input.chk-all-permission').on('ifChanged', function(event){

    //     console.log(event);
    //     if($(this).is(":checked")){
    //         $(this).closest('table').find('.chk-permission-id').iCheck('check');
    //     }else{
    //         $(this).closest('table').find('.chk-permission-id').iCheck('uncheck');
    //     }
    // });

    $("input.chk-permission-id").on("ifChecked", function (event) {
        var is_all = true;
        $.each(
            $(this).closest("table").find(".chk-permission-id"),
            function (key, item) {
                if (!$(item).is(":checked")) {
                    is_all = false;
                    return;
                }
            }
        );
        if (is_all) {
            $(this)
                .closest("table")
                .find(".chk-all-permission")
                .iCheck("check");
        }
    });

    $("input.chk-permission-id").on("ifUnchecked", function (event) {
        $(this)
            .closest("table")
            .find(".chk-all-permission")
            .prop("checked", false)
            .iCheck("update");
    });
});

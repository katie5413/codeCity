$(document).ready(function () {
    $.fn.dataTable.ext.search.pop();

    // table library init
    var classTable = $("#classTable").DataTable({
        scrollResize: true,
        scrollY: "calc(100vh - 150px)",
        scrollCollapse: true,
        scrollX: false,
        language: {
            lengthMenu: "每頁顯示 _MENU_ 筆",
            zeroRecords: "沒有資料",
            info: "",
            infoEmpty: "沒有資料",
            paginate: {
                next: '<img src="../src/img/icon/right-dark.svg">',
                previous: '<img src="../src/img/icon/left-dark.svg">',
            },
            search: '<img src="../src/img/icon/search.svg">',
            searchPlaceholder: "篩選",
        },
    });

    var studentTable = $("#studentTable").DataTable({
        scrollResize: true,
        scrollY: "calc(100vh - 150px)",
        scrollCollapse: true,
        scrollX: false,
        language: {
            lengthMenu: "每頁顯示 _MENU_ 筆",
            zeroRecords: "沒有資料",
            info: "",
            infoEmpty: "沒有資料",
            paginate: {
                next: '<img src="../src/img/icon/right-dark.svg">',
                previous: '<img src="../src/img/icon/left-dark.svg">',
            },
            search: '<img src="../src/img/icon/search.svg">',
            searchPlaceholder: "篩選",
        },
    });

    var studentTable = $("#personTable").DataTable({
        scrollResize: true,
        scrollY: "calc(100vh - 150px)",
        scrollCollapse: true,
        scrollX: false,
        language: {
            lengthMenu: "每頁顯示 _MENU_ 筆",
            zeroRecords: "沒有資料",
            info: "",
            infoEmpty: "沒有資料",
            paginate: {
                next: '<img src="../src/img/icon/right-dark.svg">',
                previous: '<img src="../src/img/icon/left-dark.svg">',
            },
            search: '<img src="../src/img/icon/search.svg">',
            searchPlaceholder: "篩選",
        },
    });

    sortIconPosition();

    // change position of sort icon at table's header
    function sortIconPosition() {
        $(
            ".table__container .dataTables_wrapper .dataTables_scroll thead th"
        ).each(function () {
            var xPos;
            if ($(this).css("text-align") == "center") {
                xPos =
                    $(this).width() / 2 +
                    ($(this).text().split("").length / 2) * 14 +
                    8;
            } else {
                xPos = $(this).text().split("").length * 14 + 8;
            }
            $(this).css("background-position-x", xPos + "px");
            $(this).css(
                "background-position-y",
                $(this).height() / 2 + 4 + "px"
            );
        });
    }

    // sort by checkbox
    $.fn.dataTable.ext.order["dom-checkbox"] = function (settings, col) {
        return this.api()
            .column(col, { order: "index" })
            .nodes()
            .map(function (td, i) {
                return $("input", td).prop("checked") ? "1" : "0";
            });
    };

    $(window).resize(function () {
        sortIconPosition();
    });

    // table's input focus listener
    $(".dataTables_filter input").focus(function () {
        $(this).parent().parent().css("border-color", "#2193d3");
    });
    $(".dataTables_filter input").focusout(function () {
        $(this).parent().parent().css("border-color", "#f2f2f2");
    });
});

$(".sub-title.form__input").click(function () {
    $(this).addClass("edit");
    $("#changeClassName").removeAttr("disabled");
});

$("#changeClassName").blur(function () {
    $(".sub-title.form__input.edit").removeClass("edit");
});

const initClassName = $("#changeClassName").val();

// 學生名單
$(".addStudent-btn").click(function () {
    $("#addStudent").removeClass("close").addClass("open");
});

// 改課程名
$(document).click(function (e) {
    if (
        $(".sub-title.form__input") !== e.target &&
        !$(".sub-title.form__input").has(e.target).length
    ) {
        $(".sub-title.form__input").removeClass("edit");
        if (initClassName !== $("#changeClassName").val()) {
            changeClassName($("#changeClassName").val());
        }
    }
});

function changeClassName(name) {
    const selectID = $("#changeClassName").attr("classID");
    window.location.href = `../src/action/changeClassName.php?classID=${selectID}&&name=${name}`;
}

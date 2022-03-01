$(document).ready(function () {
    $.fn.dataTable.ext.search.pop();

    // table library init

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
        paging: false,
        scrollY: "calc(100vh - 200px)",
        scrollCollapse: true,
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

    $("#downloadGrade").click(function () {
        $("#studentTable").table2excel({
            exclude: ".noExl",
            name: "成績表",
            filename: "成績表",
            fileext: ".xls",
            preserveColors: true,
        });
    });
});

// 換圖片
$("#selectAwardArea .select-items .option").on("click", function () {
    var selectAwardID = $(this).attr("value");
    $.ajax({
        type: "GET",
        url: `../src/action/getAwardList.php?selectAward=${selectAwardID}`,
        success: function (data) {
            // do things
            $("#building_img_area img").remove();
            $("#building_img_area").append(data);
        },
    });
    $("#selectAward").attr("value", $(this)[0].innerHTML);
});

// pop
$(".edit-mission-btn").click(function () {
    $("#editMission").removeClass("close").addClass("open");
});

$(".edit-mission__confirm").click(function () {
    var allFill = true;
    $("#editMission .input__must_fill").each(function (index) {
        if ($(this).val().trim() == "") {
            alert($(this).prev().text());
            $(this).focus();
            allFill = false;
            return false;
        }
    });

    if (allFill) {
        const imgId = $("#selectAward")[0].getAttribute("select-id");
        $("#editMission").removeClass("open").addClass("close");
    }
});

// pop
$(".add-subMission-btn").click(function () {
    $("#addSubMission").removeClass("close").addClass("open");
});

// pop
$(".edit-subMission-btn").click(function () {
    $("#editSubMission").removeClass("close").addClass("open");

    var selectID = $(this).attr("id");

    $.ajax({
        type: "GET",
        url: `../src/action/getSubMission.php?selectID=${selectID}`,
        dataType: "json",
        success: function (msgdata) {
            $("#editSubMission .inner").attr('action',`../src/action/updateSubMission.php?selectID=${selectID}`);
            $("#missionGoal_title_update").val(msgdata[0]["title"]);
            $("#missionGoal_content_update").val(msgdata[0]["content"]);
            $("#missionGoal_percent_update").val(msgdata[0]["percent"]);
            $("#editSubMission").removeClass("close").addClass("open");
        },
    });
});

$(".edit-subMission__confirm").click(function () {
    var allFill = true;
    $("#editSubMission .input__must_fill").each(function (index) {
        if ($(this).val().trim() == "") {
            alert($(this).prev().text());
            $(this).focus();
            allFill = false;
            return false;
        }
    });

    if (allFill) {
        $("#editMission").removeClass("open").addClass("close");
    }
});

// deleteMsg
$(".delete-subMission-btn").click(function () {
    $("#deleteSubMission").removeClass("close").addClass("open");
    
    var selectID = $(this).attr("id");

    $.ajax({
        type: "GET",
        url: `../src/action/getSubMission.php?selectID=${selectID}`,
        dataType: "json",
        success: function (msgdata) {
            $("#deleteSubMission .inner").attr('action',`../src/action/deleteSubMission.php?selectID=${selectID}`);
            $("#deleteSubMission").removeClass("close").addClass("open");
        },
    });
});


$(document).ready(function () {
    $.fn.dataTable.ext.search.pop();

    // table library init
    var subMissionTable = $("#subMissionTable").DataTable({
        scrollResize: true,
        scrollY: "calc(100vh - 150px)",
        scrollCollapse: true,
        scrollX: false,
        searching: false,
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
        columnDefs: [
            //给第一列指定宽度为表格整个宽度的20%
            { width: "40%", targets: 1 },
            { width: "40%", targets: 2 },
            { width: "5%", targets: 3 },
        ],
        paging: false,
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
});

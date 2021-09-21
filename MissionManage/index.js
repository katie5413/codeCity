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

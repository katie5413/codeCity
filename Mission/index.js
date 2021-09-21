//submitHomework
$(".submit-homework-btn").click(function () {
    $("#submitHomework").removeClass("close").addClass("open");
    
});

// 上傳作業圖片
$("#upload_mission_img").change(function () {
    var file = this.files[0];
    //用size属性判断文件大小不能超过5M ，前端直接判断的好处，免去服务器的压力。
    if (file.size > 5 * 1024 * 1024) {
        alert("Too Big! No more than 5MB");
    }

    var reader = new FileReader();
    reader.onload = function () {
        // 通过 reader.result 来访问生成的 base64 DataURL
        var base64 = reader.result;
        $("#mission__img_area .mission_submit").remove();
        $("#mission__img_area").append(
            `<img class="mission_submit" src="${base64}" alt="mission_submit">`
        );
    };
    reader.readAsDataURL(file);
});

$(".submit-homework__confirm").click(function () {
    var allFill = true;
    if ($("#mission__img_area").hasClass("default") === true) {
        $("#submitHomework .group_at_least_one").each(function (index) {
            if ($(this).val().trim() == "") {
                alert("At least one image or link");
                $(this).focus();
                allFill = false;
                return false;
            }
        });
    }

    if (allFill) {
        $("#submitHomework").removeClass("open").addClass("close");
        $("body").removeClass("fixed");
    }
});

// editHomework
$(".edit-homework-btn").click(function () {
    $("#editHomework").removeClass("close").addClass("open");
    
});

// 上傳作業圖片(編輯)
$("#upload_mission_img_update").change(function () {
    var file = this.files[0];
    //用size属性判断文件大小不能超过5M ，前端直接判断的好处，免去服务器的压力。
    if (file.size > 5 * 1024 * 1024) {
        alert("Too Big! No more than 5MB");
    }

    var reader = new FileReader();
    reader.onload = function () {
        // 通过 reader.result 来访问生成的 base64 DataURL
        var base64 = reader.result;
        $("#mission__img_area_update .mission_submit").remove();
        $("#mission__img_area_update").append(
            `<img class="mission_submit" src="${base64}" alt="mission_submit">`
        );
    };
    reader.readAsDataURL(file);
});

$(".edit-homework__confirm").click(function () {
    var allFill = true;
    if ($("#mission__img_area_update").hasClass("default") === true) {
        $("#editHomework .group_at_least_one").each(function (index) {
            if ($(this).val().trim() == "") {
                alert("At least one image or link");
                $(this).focus();
                allFill = false;
                return false;
            }
        });
    }

    if (allFill) {
        $("#editHomework").removeClass("open").addClass("close");
        $("body").removeClass("fixed");
    }
});

// deleteHomework
$(".delete-homework-btn").click(function () {
    $("#deleteHomework").removeClass("close").addClass("open");
    
});

$(".delete-homework__confirm").click(function () {
    $("#deleteHomework").removeClass("open").addClass("close");
});

/* Messages */
//submitMsg
$(".submit-msg-btn").click(function () {
    $("#submitMsg").removeClass("close").addClass("open");
    
});

// 上傳留言圖片
$("#upload_msg_img").change(function () {
    var file = this.files[0];
    //用size属性判断文件大小不能超过5M ，前端直接判断的好处，免去服务器的压力。
    if (file.size > 5 * 1024 * 1024) {
        alert("Too Big! No more than 5MB");
    }

    var reader = new FileReader();
    reader.onload = function () {
        // 通过 reader.result 来访问生成的 base64 DataURL
        var base64 = reader.result;
        $("#msg__img_area .msg_img").remove();
        $("#msg__img_area").append(
            `<img class="msg_img" src="${base64}" alt="msg">`
        );
    };
    reader.readAsDataURL(file);
});

$(".submit-msg__confirm").click(function () {
    $("#submitMsg").removeClass("open").addClass("close");
});

//editMsg
$(".edit-msg-btn").click(function () {
    

    var selectID = $(this).attr("id");

    $.ajax({
        type: "GET",
        url: `../src/action/getMessage.php?selectID=${selectID}`,
        dataType: "json",
        success: function (msgdata) {
            var data = `<img class="msg_img" src="${msgdata[0]["img"]}" alt="msg">`;
            $("#msg__img_area_update img").remove();
            $("#msg__img_area_update").append(data);
            $("#msg_text_update").val(msgdata[0]["content"]);
            $("#editMsg .inner").attr('action',`../src/action/updateMessage.php?selectID=${selectID}`);
            $("#editMsg").removeClass("close").addClass("open");
        },
    });
});

// 上傳留言圖片
$("#upload_msg_img_update").change(function () {
    var file = this.files[0];
    //用size属性判断文件大小不能超过5M ，前端直接判断的好处，免去服务器的压力。
    if (file.size > 5 * 1024 * 1024) {
        alert("Too Big! No more than 5MB");
    }

    var reader = new FileReader();
    reader.onload = function () {
        // 通过 reader.result 来访问生成的 base64 DataURL
        var base64 = reader.result;
        $("#msg__img_area_update .msg_img").remove();
        $("#msg__img_area_update").append(
            `<img class="msg_img" src="${base64}" alt="msg">`
        );
    };
    reader.readAsDataURL(file);
});

$(".edit-msg__confirm").click(function () {
    $("#editMsg").removeClass("open").addClass("close");
});

// deleteMsg
$(".delete-msg-btn").click(function () {
    $("#deleteMsg").removeClass("close").addClass("open");
    

    var selectID = $(this).attr("id");

    $.ajax({
        type: "GET",
        url: `../src/action/getMessage.php?selectID=${selectID}`,
        dataType: "json",
        success: function (msgdata) {

        },
    });
    $.ajax({
        type: "GET",
        url: `../src/action/getMessage.php?selectID=${selectID}`,
        dataType: "json",
        success: function (msgdata) {
            $("#deleteMsg .inner").attr('action',`../src/action/deleteMessage.php?selectID=${selectID}`);
            $("#deleteMsg").removeClass("close").addClass("open");
        },
    });
});

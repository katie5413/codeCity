// 上傳留言圖片
$("#upload_user_img").change(function () {
    var file = this.files[0];
    //用size属性判断文件大小不能超过5M ，前端直接判断的好处，免去服务器的压力。
    if (file.size > 5 * 1024 * 1024) {
        alert("Too Big! No more than 5MB");
    }

    var reader = new FileReader();
    reader.onload = function () {
        // 通过 reader.result 来访问生成的 base64 DataURL
        var base64 = reader.result;
        $("#user__img_area .user_img").remove();
        $("#user__img_area").append(
            `<img class="user_img" src="${base64}" alt="avatar">`
        );
    };
    reader.readAsDataURL(file);

    $('#changeAvatar').click();
});
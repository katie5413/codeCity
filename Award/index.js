$('.upload-award-btn').click(function () {
$('#uploadAward').removeClass('close').addClass('open');
})

// 上傳作業圖片
$("#upload_award_img").change(function () {
    var file = this.files[0];
    //用size属性判断文件大小不能超过5M ，前端直接判断的好处，免去服务器的压力。
    if (file.size > 5 * 1024 * 1024) {
        alert("Too Big! No more than 5MB");
    }

    var reader = new FileReader();
    reader.onload = function () {
        // 通过 reader.result 来访问生成的 base64 DataURL
        var base64 = reader.result;
        $("#award__img_area .award_submit").remove();
        $("#award__img_area").append(
            `<img class="award_submit" src="${base64}" alt="award_submit">`
        );
    };
    reader.readAsDataURL(file);
});

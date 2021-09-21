$(".pop .inner .close").click(function () {
    $(".pop").removeClass("open").addClass("close");
    $("body").removeClass("fixed");
});

$(".pop .inner .cancel").click(function () {
    $(".pop").removeClass("open").addClass("close");
    $("body").removeClass("fixed");
});

// pop
$(".messege-img img").click(function () {
    $("#viewImg").removeClass("close").addClass("open");
    $("#viewImg .inner #selectImg").remove();
    $("#viewImg .inner").append(
        `<img id="selectImg" src="${$(this)[0].src}"/>`
    );
});

$("#viewImg").click(function () {
    $("#viewImg").removeClass("open").addClass("close");
});
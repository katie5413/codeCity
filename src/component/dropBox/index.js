$(document).on('click', '.drop__container', function(){
    $(this).toggleClass('active');

    var search = $(this).find('input.select-selected[type="text"]').val();
    if (search.trim() != '') {
        $(this).find('.select-items').children().each(function () {
            if ($(this).text().toLowerCase().indexOf(search.toLowerCase()) == -1) {
                $(this).hide();
            } else {
                $(this).show();
            }
        })
    } else { 
        $(this).find('.select-items').children().each(function () {
            $(this).show();
        })
        $(this).find('.select-selected').attr("select-id", '');
    }
})

$(document).on('click', '.drop__container .option', function(){
    $(this).parent().siblings('.select-selected').val($(this).text());
    $(this).parent().siblings('.select-selected').attr("select-id", $(this).attr('value'));
})

$(document).on('click', '.drop__container .drop__clear', function (e) {
    e.stopPropagation();
    $(this).parent().find('input.select-selected[type="text"]').val('');
    $(this).parent().find('.select-items').children().each(function () {
        $(this).show();
    })
    $(this).parent().find('.select-selected').attr("select-id", '');
})

$(document).on('focus change paste keyup', '.drop__container input.select-selected[type="text"]', function() {
    var search = $(this).val();
    if (search.trim() != '') {
        $(this).siblings('.select-items').children().each(function () {
            if ($(this).text().toLowerCase().indexOf(search.toLowerCase()) == -1) {
                $(this).hide();
            } else {
                $(this).show();
            }
        })
    } else { 
        $(this).siblings('.select-items').children().each(function () {
            $(this).show();
        })
        $(this).attr("select-id", '');
    }
});

$(document).on('click', function(e) {
    if($('.drop__container') !== e.target && !$('.drop__container').has(e.target).length){
        $('.drop__container').removeClass('active');
    }  
});
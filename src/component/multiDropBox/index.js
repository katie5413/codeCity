$(document).on('click', '.drop__container-multi', function(){
    $(this).addClass('active');
})

$(document).on('click', '.drop__container-multi .option', function (e) {
    e.stopPropagation();
    e.preventDefault();
    $(this).find('label').toggleClass('active');
    var selected = $(this).parent().siblings('.select-selected').val() == '' ? [] : $(this).parent().siblings('.select-selected').val().split('、');
    var selectedId = $(this).parent().siblings('.select-selected').attr("select-id") == '' || $(this).parent().siblings('.select-selected').attr("select-id") == undefined || $(this).parent().siblings('.select-selected').attr("select-id") == 'undefined' ? [] : $(this).parent().siblings('.select-selected').attr("select-id").split(',');
    var currentText = $(this).find('.text').text().trim();
    var currentValue = $(this).attr('value');
    if ($(this).find('label').hasClass('active')) {
        selected.push(currentText);
        selectedId.push(currentValue);
    } else {
        selected = selected.filter(function (item) { 
            return item != currentText;
        });
        selectedId = selectedId.filter(function (item) { 
            return item != currentValue;
        });
    }
    $(this).parent().siblings('.select-selected').val(selected.join('、')).trigger('change');
    $(this).parent().siblings('.select-selected').attr("select-id", selectedId.join(','));
});

$(document).on('click', '.drop__container-multi .drop__clear', function (e) {
    e.stopPropagation();
    $(this).parent().find('input.select-selected[type="text"]').val('').trigger('change');
    $(this).parent().find('.select-items').children().each(function () {
        $(this).children().removeClass('active');
    })
    $(this).parent().find('.select-selected').attr("select-id", '');
})

$(document).on('click touchstart', function (e) {
    if ($('.drop__container-multi') !== e.target && !$('.drop__container-multi').has(e.target).length) {
        $('.drop__container-multi').removeClass('active');
    }
});
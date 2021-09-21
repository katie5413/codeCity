window.onload = function() {
    moment.locale('zh-TW');
    $('.calendar').daterangepicker({
        singleDatePicker: true,
        // showDropdowns: true,
        autoApply: true,
        autoUpdateInput: false,
        maxYear: parseInt(moment().format('YYYY'),10),
    }, function(start, end, label) {
        $('.date').val(start.format('YYYY/MM/DD'));
    });
};
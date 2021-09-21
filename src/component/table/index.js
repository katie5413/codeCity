$(document).ready(function() {
    $('#table').DataTable({
        "language": {
            "lengthMenu": "每頁顯示 _MENU_ 筆",
            "zeroRecords": "Nothing found - sorry",
            "info": "",
            "infoEmpty": "No records available",
            "infoFiltered": "(filtered from _MAX_ total records)"
        }
    });
} );
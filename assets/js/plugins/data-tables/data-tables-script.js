$(document).ready(function () {
    $('#data-table-simple').DataTable();
    $('.dt').DataTable({
        "order": [[0, 'desc']]
    });


    var table = $('#data-table-row-grouping').DataTable({
        "columnDefs": [
            {"visible": false, "targets": 2}
        ],
        "order": [[2, 'asc']],
        "displayLength": 25,
        "drawCallback": function (settings) {
            var api = this.api();
            var rows = api.rows({page: 'current'}).nodes();
            var last = null;

            api.column(2, {page: 'current'}).data().each(function (group, i) {
                if (last !== group) {
                    $(rows).eq(i).before(
                        '<tr class="group"><td colspan="10"><b>' + group + '</b></td></tr>'
                    );
                    last = group;
                }
            });
        }
    });
    $(".dropdown-content.select-dropdown li").on("click", function () {
        var that = this;
        setTimeout(function () {
            if ($(that).parent().hasClass('active')) {
                $(that).parent().removeClass('active');
                $(that).parent().hide();
            }
        }, 100);
    });

});
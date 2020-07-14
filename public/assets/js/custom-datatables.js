// Material DataTables
$(document).ready(function () {
    const dtTable = $('#dtmaterial').DataTable({
        columnDefs: [{
            'targets': 0,
            'searchable': false,
            'orderable': false,
            'className': 'select-checkbox dt-body-center',
            'render': function (data, type, full, meta){
                return '<input type="checkbox" name="id[]" value="' + $('<div/>').text(data).html() + '">';
            }
        }],
        //'order': [[1, 'asc']],
        'select': {
            style: 'os',
            selector: 'td:first-child'
        },
        "aLengthMenu": [[25, 50, 150, -1], [25, 50, 150, "All"]],
        "iDisplayLength": 25,
        "bLengthChange": true,
        "bFilter": true,
        //"sDom": 'Rfrtlip',
        "sDom": 'rt<"px-2"ip>',
    });
    $('#dtmaterial_wrapper').find('label').each(function () {
      $(this).parent().append($(this).children());
    });
    $('#dtmaterial_wrapper .dataTables_filter').find('input').each(function () {
        const $this = $(this);
        $this.attr("placeholder", "Search");
        //$this.removeClass('form-control-sm');
    });
    $('#dtmaterial_wrapper .dataTables_length').addClass('d-flex flex-row');
    $('#dtmaterial_wrapper .dataTables_filter').addClass('md-form').remove();
    $('#dtmaterial_wrapper select').removeClass('custom-select custom-select-sm form-control form-control-sm');
    $('#dtmaterial_wrapper select').addClass('mdb-select');
    $('#dtmaterial_wrapper .mdb-select').materialSelect();
    $('#dtmaterial_wrapper .dataTables_filter').find('label').remove();
    $('.pagination .active .page-link').addClass('mdb-color');
    $('.dataTables_empty').addClass('text-center red-text p-4');

    /*
    $('#search-box').keyup(function() {
        let searchVal = $(this).val();
        //dtTable.fnFilter(searchVal).draw();
        dtTable.search(searchVal).draw();
        $('.dataTables_empty').addClass('text-center red-text p-4');
    }).mouseup(function() {

    });*/
    $('#search-box').on('keyup mouseup change', function() {
        let searchVal = $(this).val();
        //dtTable.fnFilter(searchVal).draw();
        dtTable.search(searchVal).draw();
        $('.dataTables_empty').addClass('text-center red-text p-4');
    });
});

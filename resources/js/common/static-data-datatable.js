const theTable = $('#static-data-datatable');

theTable.DataTable({
    serverSide: false,
    responsive: {
        details: {
            display: $.fn.dataTable.Responsive.display.childRowImmediate,
            type: 'none'
        }
    },
    ordering: false,
    searching: false,
    language: {
        url: route('datatables-i18n')
    }
});
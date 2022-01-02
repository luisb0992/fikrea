const theTable = $('#file-logs-table');

theTable.DataTable({
    serverSide: true,
    responsive: {
        details: {
            display: $.fn.dataTable.Responsive.display.childRowImmediate,
            type: 'none'
        }
    },
    processing: true,
    ajax: '/dashboard/file/' + theTable.attr('data-file-id') + '/historial-datos',
    // ajax: route('dashboard.files.history-datatable', {id: theTable.attr('data-file-id')}),
    ordering: false,
    searching: false,
    columnDefs: [{
        targets: 'col-created_at',
        data: 'created_at',
        type: 'html',
        render: function (data, type, row) {
            const date = moment(data, 'YYYY-MM-DD HH:mm:ss');

            const firstColumnName = $('#first-column').attr('data-first-column-name');

            return '<span class="d-md-inline d-lg-none mr-3" style="display: inline-block; min-width: 75px; font-weight: bold;">' + firstColumnName + '</span>'
                + date.format('DD-MM-YYYY')
                + '<div class="text-info">' + date.format('HH:mm') + '</div>';
        }
    }, {
        targets: 'col-action',
        data: 'action',
        type: 'html'
    }, {
        targets: 'col-description',
        data: 'description',
        type: 'html'
    }],
    language: {
        url: route('datatables-i18n')
    }
});
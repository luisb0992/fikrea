const theTable = $('#sharing-datatable');

theTable.DataTable({
    serverSide: true,
    responsive: {
        details: {
            display: $.fn.dataTable.Responsive.display.childRowImmediate,
            type: 'none'
        }
    },
    processing: true,
    ajax: '/dashboard/file/sharing-data',
    // ajax: route('dashboard.files.sharing-datatable'),
    ordering: false,
    searching: false,
    columnDefs: [{
        targets: 'col-info',
        data: null,
        type: 'html',
        render: function (data, type, row) {
            const firstColumnName = $('#first-column').attr('data-first-column-name');

            return '<span class="d-md-inline d-lg-none mr-3" style="display: inline-block; min-width: 75px; font-weight: bold;">' + firstColumnName + '</span>'
                + row.title
                + '<br><span style="font-style: italic; font-size: smaller">'
                + row.description
                + '</span><br>'
                + '<em class="text-secondary">' + row.count + ' fichero(s), ' + row.size + '</em>';
        }
    }, {
        targets: 'col-recipient_list',
        data: 'recipient_list',
        type: 'html',
        render: function (data) {
            const recipients = data;
            const count = recipients.length;

            let recipientsList = '';
            let hasEmail, displayData;

            for (let i = 0; i < count; i++) {
                hasEmail = (typeof recipients[i].email !== 'undefined') && (recipients[i].email !== '') && (recipients[i].email !== null);
                displayData = '';
                if (hasEmail) {
                    displayData = recipients[i].email;
                } else if ((typeof recipients[i].phone !== 'undefined') && (recipients[i].phone !== null)) {
                    displayData = recipients[i].phone;
                }

                recipientsList +=
                    '<a href="' + (hasEmail ? 'mailto:' : '') + displayData + '">' + displayData + '</a>'
                    + (i + 1 < count ? '<hr>' : '')
                ;
            }

            return recipientsList;
        }
    }, {
        targets: 'col-created_at',
        data: 'created_at',
        type: 'html',
        render: function (data) {
            const date = moment(data, 'YYYY-MM-DD HH:mm:ss');

            return date.format('DD-MM-YYYY') + '<div class="text-info">' + date.format('HH:mm') + '</div>';
        }
    }, {
        targets: 'col-updated_at',
        data: 'updated_at',
        type: 'html',
        render: function (data) {
            const date = moment(data, 'YYYY-MM-DD HH:mm:ss');

            return date.format('DD-MM-YYYY') + '<div class="text-info">' + date.format('HH:mm') + '</div>';
        }
    }, {
        targets: 'col-action',
        data: 'action',
        type: 'html'
    }],
    drawCallback: function () {
        $('[data-toggle="tooltip"]').tooltip();
    },
    language: {
        url: route('datatables-i18n')
    }
});

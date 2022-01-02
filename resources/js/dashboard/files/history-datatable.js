const theTable = $('#history-datatable');

theTable.DataTable({
    serverSide: true,
    responsive: {
        details: {
            display: $.fn.dataTable.Responsive.display.childRowImmediate,
            type: 'none'
        }
    },
    processing: true,
    ajax: '/dashboard/file/' + theTable.attr('data-sharing-id') + '/sharing-history-data',
    // ajax: route('dashboard.files.sharing-history-datatable', {id: theTable.attr('data-sharing-id')}),
    ordering: false,
    searching: false,
    columnDefs: [{
        targets: 'col-date',
        data: 'starts_at',
        type: 'html',
        render: function (data, type, row) {
            const date = (typeof data !== 'undefined') ? moment(data, 'YYYY-MM-DD HH:mm:ss') : moment(row.downloaded_at, 'YYYY-MM-DD HH:mm:ss');

            const firstColumnName = $('#first-column').attr('data-first-column-name');

            // return '<span class="d-md-inline d-lg-none mr-3" style="display: inline-block; min-width: 75px; font-weight: bold;">' + firstColumnName + '</span>';
            return '<span class="d-md-inline d-lg-none mr-3" style="display: inline-block; min-width: 75px; font-weight: bold;">' + firstColumnName + '</span>'
                + date.format('DD-MM-YYYY')
                + '<div class="text-info">' + date.format('HH:mm') + '</div>';
        }
    }, {
        targets: 'col-action',
        data: 'action',
        type: 'html',
    }, {
        targets: 'col-contact',
        data: null,
        type: 'html',
        render: function (data, type, row) {
            if (row.name) {
                let link = '';

                if (row.email) {
                    link = '<a href="mailto:' + row.email + '">' + row.email + '</a>';
                } else if (data.phone) {
                    link = '<a href="tel:' + row.phone + '">' + row.phone + '</a>';
                }

                return row.name + ' ' + row.lastname + '<div>' + link + '</div>'
            }

            return row.anonymous_text;
        }
    }, {
        targets: 'col-ip',
        data: 'ip',
        type: 'html'
    }, {
        targets: 'col-user_agent',
        data: 'user_agent',
        type: 'html'
    }, {
        targets: 'col-location',
        data: 'location',
        type: 'html',
        render: function (data, type, row) {
            if ((data.country !== null) && (data.city !== null)) {
                let coordinates = '';

                // Preferir la localización exacta obtenida del GPS; si no, utilizar la obtenida a partir de la dirección IP
                let latitude = null;
                let longitude = null;
                if (row.latitude !== null) {
                    latitude = row.latitude;
                    longitude = row.longitude;
                } else {
                    latitude = data.latitude;
                    longitude = data.longitude;
                }

                if ((latitude !== null) && (longitude !== null)) {
                    coordinates =
                        '<div class="col-md-6 col-s-12">' +
                        '<a target="_blank" class="btn btn-primary square" href="https://www.google.com/maps/search/?api=1&query='
                        + latitude + ',' + longitude + '">'
                        + '<i class="fas fa-map-marker-alt"></i></a></div>';
                }

                return '<div class="row"><div class="col-md-6 col-s-12"><div>'
                    + data.city + ' ' + data.region
                    + '</div><div><strong>' + data.country + '</strong></div></div>'
                    + coordinates + '</div>';
            }

            return '<div class="text-center"><div class="bg-danger text-white p-2">' + row.no_location_text + '</div></div>';
        }
    }],
    language: {
        url: route('datatables-i18n')
    }
});

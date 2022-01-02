@extends('dashboard.layouts.main')

@section('title')
    @lang('Listado de comparticiones de archivos')
@stop

@push('page-styles')
    <link rel="stylesheet" type="text/css" href="@mix('assets/css/datatable.css')">
    <link rel="stylesheet" type="text/css" href="@mix('assets/css/dashboard/file/list.css')"/>
@endpush

@section('help')
    <div>
        @lang('Se muestra la lista con las comparticiones de archivos realizadas')
        <div class="page-title-subheading">
            @lang('Cada conjunto de archivos posee un enlace de descarga que se envío a los destinatarios')
        </div>
    </div>
@stop

@section('content')
    <div class="col-12">
        <div class="card mb-3" style="width: 100%;">
            <h5 class="card-header">@lang('Archivos Compartidos')</h5>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="mb-0 table table-striped table-hover responsive row-border" id="sharing-datatable"
                           style="width: 100%">
                        <thead>
                        <tr>
                            <th class="col-info min-tablet-l" style="width: 50%;" id="first-column"
                                data-first-column-name="@lang('Información General')">
                                <span class="d-none d-lg-block">@lang('Información General')</span>
                            </th>
                            <th class="col-recipient_list min-tablet-l" style="width: 30%;">@lang('Destinatarios')</th>
                            <th class="col-created_at min-tablet-l" style="width: 10%;">@lang('Creado')</th>
                            <th class="col-updated_at min-tablet-l" style="width: 10%;">
                                @lang('Última Actualización')
                            </th>
                            <th class="col-action min-tablet-l"></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="sharing-file-list-modal" tabindex="-1" aria-labelledby="sharingFileListModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sharingFileListModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    okokokokokok
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
@stop

@push('page-scripts')
    <script src="@asset('assets/js/landing/vendor/bootstrap.min.js')"></script>
    <script src="@mix('assets/js/moment.js')"></script>
    <script src="@mix('assets/js/datatables.js')"></script>
    <script src="@mix('assets/js/dashboard/files/sharing-datatable.js')"></script>
@endpush

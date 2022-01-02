@extends('dashboard.layouts.main')

@push('page-styles')
    <link rel="stylesheet" type="text/css" href="@mix('assets/css/datatable.css')">
@endpush

{{-- Título de la Página --}}
@section('title', @config('app.name'))

@section('help')
    <div>
        @lang('Historial de acceso al archivo seleccionado')
        <div class="page-title-subheading">
            @lang('Muestra el historial de descargas y acceso al archivo seleccionado.')
        </div>
    </div>
@stop

{{--
    El contenido de la página
--}}
@section('content')
    <div class="col-12">
        <div class="card mb-3" style="width: 100%;">
            <h5 class="card-header">@lang('Historial del Archivo')</h5>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="mb-0 table table-striped table-hover responsive row-border"
                           id="file-logs-table" data-file-id="{{ $file->id }}" style="width: 100%">
                        <thead>
                        <tr>
                            <th class="col-created_at min-tablet-l" style="width: 25%;" id="first-column"
                                data-first-column-name="@lang('Fecha y Hora')">
                                <span class="d-none d-lg-block">@lang('Fecha y Hora')</span>
                            </th>
                            <th class="col-action min-tablet-l" style="width: 25%;">@lang('Acción')</th>
                            <th class="col-description min-tablet-l" style="width: 50%;">@lang('Detalles')</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@push('page-scripts')
    <script src="@mix('assets/js/moment.js')"></script>
    <script src="@mix('assets/js/datatables.js')"></script>
    <script src="@mix('assets/js/file-logs-datatable.js')"></script>
@endpush
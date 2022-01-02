@extends('dashboard.layouts.main')

@section('title')
    @lang('Historial de visitas a la comparticiones de archivos')
@stop

@push('page-styles')
    <link rel="stylesheet" type="text/css" href="@mix('assets/css/datatable.css')">
    <link rel="stylesheet" type="text/css" href="@mix('assets/css/dashboard/file/list.css')"/>
@endpush

@section('help')
    <div>
        @lang('Se muestra la lista de visitas realizadas sobre la compartición de archivo')
        <div class="page-title-subheading">
            @lang('También la cantidad de descargas de la compartición por destinatario.')
        </div>
    </div>
@stop

@section('content')
    <x-certificate-button
            :route="@route('dashboard.files.certificate', ['id' => $fileSharing->id])"></x-certificate-button>
    <div class="col-12">
        <div class="card mb-3" style="width: 100%;">
            <h5 class="card-header">@lang('Historial de Accesos y Descargas')</h5>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="mb-0 table table-striped table-hover responsive row-border" id="history-datatable"
                           data-sharing-id="{{ $fileSharing->id }}" style="width: 100%">
                        <thead>
                        <tr>
                            <th class="col-date min-tablet-l" style="width: 10%;" id="first-column"
                                data-first-column-name="@lang('Fecha')">
                                <span class="d-none d-lg-block">@lang('Fecha')</span>
                            </th>
                            <th class="col-action min-tablet-l" style="width: 10%;">@lang('Acción')</th>
                            <th class="col-contact min-tablet-l" style="width: 15%;">@lang('Destinatario')</th>
                            <th class="col-ip min-tablet-l" style="width: 10%;">@lang('Ip')</th>
                            <th class="col-user_agent min-tablet-l" style="width: 18%;">@lang('Sistema')</th>
                            <th class="col-location min-tablet-l" style="width: 17%;"
                                class="text-center">@lang('Ubicación Aproximada')
                                <span data-toggle="tooltip" data-placement="top"
                                      data-original-title="@lang('Ubicación aproximada en relación a la dirección ip utilizada en la conexión')">*</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <x-certificate-button
            :route="@route('dashboard.files.certificate', ['id' => $fileSharing->id])"></x-certificate-button>
@stop

@push('page-scripts')
    <script src="@mix('assets/js/moment.js')"></script>
    <script src="@mix('assets/js/datatables.js')"></script>
    <script src="@mix('assets/js/dashboard/files/history-datatable.js')"></script>
@endpush

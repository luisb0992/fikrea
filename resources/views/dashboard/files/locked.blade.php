@extends('dashboard.layouts.main')

@push('page-styles')
    <link rel="stylesheet" type="text/css" href="@mix('assets/css/datatable.css')">
@endpush

{{-- Título de la Página --}}
@section('title')
    @lang('Listado de archivos')
@stop

{{-- Css Personalizado --}}
@push('page-styles')
    <link rel="stylesheet" type="text/css" href="@mix('assets/css/dashboard/file/list.css')"/>
@endpush

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
    <div>
        @lang('Se muestran los archivos que ha subido a :app en estado bloqueado', ['app' => config('app.name')])
        <div class="page-title-subheading">
            @lang('Para desbloquear estos archivos, cambie su suscripción o libere espacio eliminando archivos existentes.')
            @lang('Los archivos que se mantengan por más de 24 horas en este estado, serán eliminados automáticamente por el sistema.')
        </div>
    </div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')
    <div class="col-12">
        @include('dashboard.partials.disk-space')
        <div class="card mb-3" style="width: 100%;">
            <h5 class="card-header">@lang('Archivos Bloqueados')</h5>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="mb-0 table table-striped table-hover responsive row-border"
                           id="static-data-datatable" style="width: 100%">
                        <thead>
                        <tr>
                            <th class="col-name min-tablet-l" style="width: 60%;" id="first-column"
                                data-first-column-name="@lang('Archivo')">
                                <span class="d-none d-lg-block">@lang('Archivo')</span>
                            </th>
                            <th class="col-type min-tablet-l" style="width: 10%;">@lang('Tipo')</th>
                            <th class="text-center col-size min-tablet-l" style="width: 10%;">@lang('Tamaño')</th>
                            <th class="text-nowrap col-created_at min-tablet-l" style="width: 20%;">@lang('Creado')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach( $files as $file )
                            <tr>
                                <td data-label="@lang('Archivo')">
                                    <span class="d-md-inline d-lg-none mr-3"
                                          style="display: inline-block; min-width: 75px; font-weight: bold;">
                                        @lang('Archivo')
                                    </span>
                                    <a href="javascript:void(0)">{{ $file->name }}</a>
                                </td>
                                <td class="align-middle" data-label="@lang('Tipo')">
                                    @include('dashboard.partials.file-icon', ['type' => $file->type])
                                </td>
                                <td class="text-center" data-label="@lang('Tamaño')">
                                    @filesize($file->size)
                                </td>
                                <td class="align-middle text-nowrap" data-label="@lang('Creado')">
                                    @date( $file->created_at )
                                    <div class="text-info">@time( $file->created_at )</div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@push('page-scripts')
    <script src="@mix('assets/js/datatables.js')"></script>
    <script src="@mix('assets/js/common/static-data-datatable.js')"></script>
@endpush

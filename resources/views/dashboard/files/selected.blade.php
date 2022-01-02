@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title')
    @lang('Listado de archivos')
@stop

{{-- Css Personalizado --}}
@push('page-styles')
    <link rel="stylesheet" type="text/css" href="@mix('assets/css/datatable.css')">
    <link rel="stylesheet" type="text/css" href="@mix('assets/css/dashboard/file/list.css')"/>
@endpush

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
    <div>
        @lang('Se muestran los archivos que han sido seleccionados para ejecutar una acción de selección múltiple.')
        <div class="page-title-subheading">
            @lang('Puede ver los detalles de la selección realizada, o quitar ficheros de la selección.')
        </div>
    </div>
@stop

{{--
    El contenido de la página
--}}
@section('content')
    <div class="col-12">
        <div v-cloak id="app">
            @include('dashboard.partials.disk-space')
            <div class="card mb-3" style="width: 100%;">
                <h5 class="card-header">@lang('Archivos Seleccionados')</h5>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="mb-0 table table-striped responsive" id="static-data-datatable">
                            <thead>
                            <tr>
                                <th class="col-name" style="width: 30%;">@lang('Archivo')</th>
                                <th class="col-name" style="width: 30%;">@lang('Carpeta')</th>
                                <th class="col-type" style="width: 10%;">@lang('Tipo')</th>
                                <th class="text-center col-size" style="width: 10%;">@lang('Tamaño')</th>
                                <th class="text-nowrap col-created_at" style="width: 20%;">@lang('Creado')</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(file, index) in files" :key="file.id" :id="'selected-file-'+file.id">
                                <td data-label="@lang('Archivo')">
                                    <a href="javascript:void(0)">@{{ file.name }}</a>
                                    <div v-if="file.is_folder" class="btn btn-action-details ml-2" data-html="true"
                                         data-toggle="tooltip"
                                         data-placement="bottom" :data-original-title="file.extra_data"
                                         data-delay='{"show": 0, "hide": 1000}'>
                                        <i class="fa fa-eye"></i>
                                    </div>
                                </td>
                                <td data-label="@lang('Carpeta')">@{{ file.full_path }}</td>
                                <td class="align-middle" data-label="@lang('Tipo')">
                                    <span v-html="file.type"></span>
                                </td>
                                <td class="text-center" data-label="@lang('Tamaño')">@{{ file.size }}</td>
                                <td class="align-middle text-nowrap" data-label="@lang('Creado')">
                                    @{{ file.created_at_date }}
                                    <div class="text-info">@{{ file.created_at_time }}</div>
                                </td>
                                <td>
                                    <button @click.prevent="deselect(file.id)" class="btn btn-action-remove"
                                            data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="@lang('Quitar de la selección')">
                                        <i class="fa fa-check"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="files.length === 0">
                                <td colspan="6" class="text-center">Ningún dato disponible en esta tabla</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('page-scripts')
    <script src="@mix('assets/js/dashboard/files/selected.js')"></script>
    <script src="@mix('assets/js/datatables.js')"></script>
    {{--    <script src="@mix('assets/js/common/static-data-datatable.js')"></script>--}}
@endpush

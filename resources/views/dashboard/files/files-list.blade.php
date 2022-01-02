@extends('dashboard.layouts.main')

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
        @lang('Se muestran los archivos que ha subido a :app', ['app' => config('app.name')])
        <div class="page-title-subheading">
            @lang('Puede seleccionar aquel o aquellos archivos que desee compartir o los que desee enviar a firmar').
            @lang('También puede obtener un enlace rápido a un archivo utilizando el botón')
            <i class="fas fa-share-alt"></i>
        </div>
    </div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')

    {{-- El mensaje flash que se muestra cuando la operación ha tenido éxito o error --}}
    <div class="offset-md-3 col-md-6">
        @include('dashboard.sections.body.message-success')
        @include('dashboard.sections.body.message-error')
    </div>
    {{--/El mensaje flash que se muestra cuando la operacion ha tenido éxito o error --}}

    <div v-cloak id="app" class="col-md-12">

        {{-- Las rutas de las solicitudes --}}
        <div id="request"
             data-remove-file="@route('dashboard.file.delete', ['id' => ':id'])"
             data-url-to-reload="@route('dashboard.file.list')"
        ></div>
        {{-- / Las rutas de las solicitudes --}}

        {{-- Las modales necesarias --}}
        @include('dashboard.modals.files.remove-file-confirmation')
        @include('dashboard.modals.files.remove-files-confirmation')
        @include('dashboard.modals.files.sharing-title-and-description')
        @include('dashboard.modals.files.sharing-title-and-description-multiple')
        {{-- /Las modales necesarias --}}

        <div>

            {{-- Mensajes de la aplicación --}}
            <div id="message"
                 data-share-title="@config('app.name')"
                 data-share-text="@lang('Se ha copiado la dirección de descarga del archivo')">
            </div>
            {{--/Mensajes de la aplicación --}}

            {{-- Uso del espacio disponible --}}
            @include('dashboard.partials.disk-space')
            {{--/Uso del espacio disponible --}}

            {{-- Control de la Tabla --}}
            @include('dashboard.files.controls-files-table')
            {{--/Control de la Tabla --}}

            {{-- Formulario para la compartición de archivos --}}
            <form class="d-none" id="form" action="@route('dashboard.share.file.set')" method="post"
                  data-request-remove-files="@route('dashboard.files.delete')"
                  data-request-download-files="@route('dashboard.files.download')"
                  data-after-remove-redirect-to="@route('dashboard.file.list')"
            >
                @csrf
                <input type="hidden" name="selected" value="">
            </form>
            {{-- Formulario para la compartición de archivos --}}

            {{-- Formulario para la movida múltiple de archivos --}}
            <form class="d-none" id="formMultipleMove" action="@route('dashboard.files.multiple-move')" method="post">
                @csrf
                <input type="hidden" name="selected" value="">
            </form>
            {{-- Formulario para la movida múltiple de archivos --}}

            {{-- Formulario para la firma múltiple de archivos --}}
            <form class="d-none" id="formMultiple" action="@route('dashboard.files.multiple.sign')"
                  method="post">
                @csrf
                <input type="hidden" name="selected" value="">
            </form>
            {{-- Formulario para la firma múltiple de archivos --}}

            {{-- Campo para indicar el ID del fichero del que se quiere copiar la URL --}}
            <input type="hidden" id="extra-data-id">

            {{-- Listado de archivos --}}
            <div class="main-card mb-3 card">
                <div class="card-body">

                    <h5 class="card-title">
                        @lang('Archivos subidos')
                    </h5>
                    @if( $folder )
                        <x-folders-breadcrumb :folder="$folder"/>
                    @endif
                    <hr>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <b-form inline>
                                <input type="hidden" id="filesToShow" value={{$count}}>
                                @lang('Mostrando')
                                <b-form-select
                                        v-model="filesToShow"
                                        :options="optionsFilesToShow"
                                        class="inline"
                                        @change="changingSelection"
                                ></b-form-select>
                                @lang('archivos').
                            </b-form>

                        </div>

                        {{-- link para ir a subir un nuevo archivo  --}}
                        <div class="col-md-6 mb-2 text-right">
                            <div class="btn-group" role="group" aria-label="">
                                <a href="javascript:void(0)" class="btn btn-action-details d-md-inline d-lg-none">
                                    <label style="margin-bottom: 0; margin-top: -1px !important;"
                                           data-toggle="tooltip" data-placement="right"
                                           data-original-title="@lang('Seleccionar todos')" @change.prevent="selectAll"
                                           class="check-container">
                                        <input class="form-control" type="checkbox"/>
                                        <span class="square-check"></span>
                                        <div style="display: inline-block; margin-left: 30px;">@lang('Todos')</div>
                                    </label>
                                </a>
                                <a href="@route('dashboard.folders.create', ['id' => request()->get('id', null)])"
                                   class="btn btn-action-folder" data-toggle="tooltip" data-placement="top"
                                   data-original-title="@lang('Crear Carpeta')">
                                    <i class="fas fa-folder-plus"></i>
                                    <span class="d-none d-lg-inline">@lang('Crear Carpeta')</span>
                                </a>
                                <a href="@route('dashboard.file.upload', ['id' => request()->get('id', null)])"
                                   class="btn btn-action-upload" data-toggle="tooltip" data-placement="top"
                                   data-original-title="@lang('Subir Archivo Nuevo')">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span class="d-none d-lg-inline">@lang('Subir un Archivo')</span>
                                </a>
                            </div>
                        </div>

                    </div>

                    {{-- La tabla con los archivos subidos --}}
                    @include('dashboard.files.files-table', ['selection' => true])
                    {{--/La tabla con los archivos subidos --}}
                </div>
            </div>
            {{-- / Listado de archivos --}}

            {{-- Control de la Tabla --}}
            @include('dashboard.files.controls-files-table')
            {{--/Control de la Tabla --}}

        </div>

    </div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
    {{-- BootstrapVue --}}
    <script src="@asset('assets/js/vue/bootstrap-vue.js')"></script>

    <script src="@mix('assets/js/dashboard/files/file-list.js')"></script>

    {{--  gestión de redes sociales  --}}
    <script src="@mix('assets/js/common/social-media.js')"></script>
@stop
@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@push('page-styles')
    <link rel="stylesheet" type="text/css" href="@mix('assets/css/dashboard/file/upload.css')"/>
@endpush

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
    <div>
        @lang('Suba tus archivos y compártelos con tus colaboradores, familiares o amigos')
        <div class="page-title-subheading">
            @lang('Arrastra o selecciona los archivos que desees subir a :app', ['app' => config('app.name')])
        </div>
    </div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')
    <div v-cloak id="app" class="col-md-12 file-uploading">

        {{--
            La configuración de subida de archivos
            Los tipos de archivos permitidos para la subida
        --}}
        <div id="config" data-max-size="@config('files.max.size')"></div>
        {{--/La configuración de subida de archivos --}}

        {{-- Los mensajes de la aplicación --}}
        <div id="message"
            data-file-shared="@lang('Se ha copiado la dirección de descarga del archivo')"
            data-file-not-valid="@lang('El archivo no es válido')"
            data-file-not-valid-with-error="@lang('El archivo no es válido o contiene errores para su lectura')"
            data-file-too-big="@lang('El archivo es demasiado grande')"
            data-file-upload-success="@lang('El archivo ha sido subido con éxito. Puede acceder al mismo desde la opción Archivos del Gestor de Archivos.')"
            data-storage-amount-limit-exceeded="@lang('El tamaño del archivo supera el almacenamiento contratado en su plan :plan', ['plan' => $user->subscription->plan->name])"
            data-file-locked="@lang('El tamaño del archivo supera el almacenamiento contratado en su plan :plan, por lo que se encuentra bloqueado', ['plan' => $user->subscription->plan->name])">
        </div>
        {{--/Los mensajes de la aplicación --}}

        {{-- Rutas para las solicitudes --}}
        <div id="route"
            data-sign-file-request="@route('dashboard.file.sign', ['id' => ':id'])"
            {{-- FIXME: Valorar si es factible esta acción en este punto y la forma en que se quiere gestionar --}}
            data-share-file-request=""
            data-delete-file-request="@route('dashboard.file.delete', ['id' => ':id'])"></div>
        {{--/Rutas para las solicitudes --}}

        {{-- Las rutas de las solicitudes --}}
        <div id="request"
            data-remove-file="@route('dashboard.file.delete', ['id' => ':id'])"
            data-url-to-reload="@route('dashboard.file.list')">
        </div>
        {{-- / Las rutas de las solicitudes --}}

        {{-- Las modales necesarias --}}
        @include('dashboard.modals.files.remove-file-confirmation')
        @include('dashboard.modals.files.sharing-title-and-description')
        {{-- /Las modales necesarias --}}

        {{-- Uso del espacio disponible --}}
        @include('dashboard.partials.disk-space')
        {{--/Uso del espacio disponible --}}

        <div v-show="$refs.upload && $refs.upload.dropActive" class="drop-active">
            <h3>@lang('Arrastra archivos para subir')</h3>
        </div>
        <div class="main-card mb-3 card">
            <div class="card-body">

                {{-- Botón para subir más archivos --}}
                @include('dashboard.files.upload-file-button')
                {{-- / Botón para subir más archivos --}}

                {{-- Listado de archivos subidos recientemente --}}
                <div class="text-bold text-secondary text-right">
                    @lang('Tamaño máximo admitido') : @filesize(config('files.max.size'))
                </div>
                @if( $folder )
                    <x-folders-breadcrumb :folder="$folder" leaf_navigation="true"/>
                @endif
                <div class="table-responsive">
                    <table class="mb-0 table table-striped">
                        <thead>
                            @include('dashboard.files.upload-files-table-header')
                        </thead>
                        <tbody>
                        <tr v-if="!files.length">
                            <td colspan="4">
                                <div class="text-center p5">

                                    <i class="fas fa-cloud-upload-alt fa-9x text-info text-shadow"></i>
                                    <h4>@lang('Arrastra un archivo aquí')</h4>
                                    <h4>@lang('o bien')</h4>
                                    <label :for="'file'" class="btn btn-lg btn-primary">
                                        @lang('Selecciona un archivo')
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr v-for="(file, index) in files" :key="file.id" :id="'uploaded-file-'+file.id">
                            {{-- Archivo --}}
                            <td>
                                <div class="bold">@{{file.name}}</div>
                                <div v-if="file.active || file.progress !== '0.00'">
                                    <div
                                            class="mt-2"
                                            :class="{
                                                'progress-bar': true, 
                                                'progress-bar-striped': true,
                                                'bg-success': file.progress,  
                                                'bg-danger' : file.error, 
                                                'progress-bar-animated': file.active
                                            }"
                                            role="progressbar"
                                            :style="{
                                                width: file.progress + '%'
                                            }"
                                        >
                                        @{{file.progress}}%
                                    </div>
                                </div>
                            </td>
                            {{--/Archivo --}}

                            {{-- Tamaño --}}
                            <td class="text-center" data-label="@lang('Tamaño')">
                                @{{file.size | formatSize}}
                            </td>
                            {{--/Tamaño --}}

                            {{-- Estado --}}
                            <td v-if="file.error" class="text-center text-danger" data-label="@lang('Estado')">
                                @{{file.error == 'abort' ? '@lang('Abortado')' : '@lang('Error en Servidor')' }}
                            </td>
                            <td v-else-if="file.success" class="text-center text-success"
                                data-label="@lang('Estado')">@lang('Guardado con éxito')</td>
                            <td v-else-if="file.active" class="text-center text-warning"
                                data-label="@lang('Estado')">@lang('En progreso')</td>
                            <td v-else class="text-center text-primary"
                                data-label="@lang('Estado')">@lang('Pendiente')</td>
                            {{--/Estado --}}

                            {{-- Opciones a realizar con el archivo subido --}}
                            {{--
                                TODO
                                @REYNIER
                                NO SE ESTAN MOSTRANDO MOSTRANDO LOS TOOLTIPS DE LOS BOTONES CORRECTAMENTE
                                NO SE QUE PASA CON ESTA TABLA QUE AL PASAR EL MOUSE POR ENCIMA SE OPACA COMPLETA, 
                                CUANDO QUIZAS DEBERIA SER SOLO LA FILA
                            --}}
                            <td class="text-center">
                                <span v-if="file.active || !file.success">
                                    <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                                </span>
                                <span v-else>
                                    <div class="btn-group text-white" role="group" aria-label="">
                                        <a v-if="!file.data.locked" href="javascript:void(0)"
                                           @click.prevent="redirectToURL(file.id)"
                                           class="btn btn-action-move" data-toggle="tooltip" data-placement="top"
                                           data-original-title="@lang('Mover a carpeta')">
                                           <i class="far fa-share-square text-white"></i>
                                        </a>
                                        
                                        <a v-if="!file.data.locked" href="javascript:void(0)"
                                           class="btn btn-action-sign" @click.prevent="signFile(file)"
                                           data-toggle="tooltip" data-placement="top"
                                           data-original-title="@lang('Generar documento para firmar')">
                                            <i class="fas fa-signature"></i>
                                        </a>
                                        <a v-if="!file.data.locked" href="javascript:void(0)"
                                           class="btn btn-action-copy-url" @click.prevent="sharingExtraData(file.id)"
                                           data-toggle="tooltip" data-placement="top"
                                           data-original-title="@lang('Copiar URL')">
                                            <i class="fas fa-copy"></i>
                                        </a>
                                        <a v-if="!file.data.locked" @click.prevent="shareFile(file)"
                                           class="btn btn-action-share" data-toggle="tooltip" data-placement="bottom"
                                           data-original-title="@lang('Compartir el archivo')">
                                            <i class="fas fa-share-alt" data-id="file.id"></i>
                                        </a>
                                        <a @click.prevent="confirmRemoveFile(file.id)" class="btn btn-action-remove"
                                           data-toggle="tooltip" data-placement="bottom"
                                           data-original-title="@lang('Eliminar el archivo')">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </span>
                            </td>
                            {{-- / Opciones a realizar con el archivo subido --}}

                        </tr>
                        </tbody>
                        <tfoot>
                            @include('dashboard.files.upload-files-table-header')
                        </tfoot>
                    </table>
                </div>
                {{-- /Listado de archivos subidos recientemente --}}

            </div>
        </div>

        {{-- Campo para indicar el ID del fichero del que se quiere copiar la URL --}}
        <input type="hidden" id="extra-data-id">

        {{-- Formulario para la compartición de archivos --}}
        <form class="d-none" id="form" action="@route('dashboard.share.file.set')" method="post">
            @csrf
            <input type="hidden" name="selected" value="">
        </form>
        {{-- Formulario para la compartición de archivos --}}
        {{-- Campos para controlar la subida de imagen desde el portapapales --}}
        <div class="d-none">
            <input type="file" name="clipboard-file" id="clipboard-file">
            <button type="button" @click.prevent="addClipboardData" id="clipboard-button"></button>
        </div>
    </div>
@stop

{{-- Los scripts personalizados --}}
@push('page-scripts')
    <script src="@asset('assets/js/vue/bootstrap-vue.js')"></script>
    <script src="@mix('assets/js/dashboard/files/clipboard.js')"></script>
    <script src="@mix('assets/js/dashboard/files/file.js')"></script>
@endpush
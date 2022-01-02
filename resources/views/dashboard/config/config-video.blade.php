@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', 'WorkSpace')

{{-- Css Personalizado --}}
@section('css')
<link rel="stylesheet" href="/assets/css/workspace/video.css" />
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @lang('Pulse el botón grabar para iniciar la grabación de video')
    <div class="page-title-subheading">
          @lang('Puede decir lo que quiera que se tome de ejemplo para las validaciones')
          @lang('de video de sus documentos.')
    </div>
</div>
@stop

{{-- 
    Aquí incluímos el contenido de la página
--}}
@section('content')

<div v-cloak id="app" class="col-md-12">

    {{-- El texto de referencia que se debe decir --}}
    <div class="alert alert-warning">
        <span class="fa-2x-text">@lang('Tenga en cuenta que su grabación se tomara como referencia en las validaciones de los firmantes de sus documentos.')</span>
    </div>
    {{--/El texto de referencia que se debe decir --}}

    {{-- Los botones de Acción --}}
    <div class="col-md-12 mb-4">

        <a href="#" :class="!videos.length ? 'disabled': ''"
            @click.prevent="save" class="btn btn-xlg btn-success"
            data-save-request="@route('dashboard.config.video')"
            data-redirect-request="@route('dashboard.config')"
            >
            @lang('Finalizar')
         </a>

         <a href="@route('dashboard.config')" class="btn btn-xlg btn-danger">
            @lang('Atrás')
        </a>

    </div>
    {{--/ Los botones de Acción --}}

    {{-- La ventana de video --}}
    <div class="col-md-12 text-center">
        <video id="video-screen" autoplay></video>
    </div>  
    {{--/La ventana de video --}}

    {{-- Una tabla con el archivo de Video --}}
    <div class="table-responsive">
        <table class="table">

            <thead>
                <tr>
                    <th class="text-center">@lang('Eliminar')</th>
                    <th>@lang('Archivo')</th>
                    <th>@lang('Tipo')</th>
                    <th>@lang('Duración') (s)</th>
                    <th>@lang('Tamaño') (kB)</th>
                    <th class="text-center">@lang('Ver')</th>
                </tr>
            </thead>

            <tbody v-if="!videos.length">
                <tr>
                    <td colspan="6" class="text-center">@lang('No hay grabaciones de Video')</td>
                </tr>
            </tbody>

            <tbody v-else>
                <tr v-for="video in videos">
                    <td class="text-center">
                        <button @click="removeVideo(video)" class="btn btn-danger square">
                            <i class="fa fa-trash fa-2x"></i>
                        </button>
                    </td>
                    <td>@{{video.filename}}</td>
                    <td>@{{video.type}}</td>
                    <td>@{{video.duration}}</td>
                    <td>@{{video.size}}</td>
                    <td class="text-center">
                        <video :src="video.video" class="video-thumb" controls></video>
                        <div class="text-center bg-warning bold">
                            @{{video.duration}}
                        </div>
                    </td>
                </tr>
            </tbody>

            <tfoot>
                <tr>
                    <th class="text-center">@lang('Eliminar')</th>
                    <th>@lang('Archivo')</th>
                    <th>@lang('Tipo')</th>
                    <th>@lang('Duración') (s)</th>
                    <th>@lang('Tamaño') (kB)</th>
                    <th class="text-center">@lang('Escuchar')</th>
                </tr>
            </tfoot>

        </table>
    </div>

    <div id="messages" class="d-none" data-video-record-success="@lang('La grabación de video ha finalizado con éxito')"></div>

    <div class="col-md-12 text-right mb-4">
        <div class="btn-group" role="group">
            <button id="video" @click="recordVideo()" class="btn" 
                :class="recording ? 'btn-danger' : 'btn-success'"
                data-max-record-time="@config('validations.video.recordtime')"    
            >  
                <span v-if="recording">
                    <i class="fa fa-stop"></i>     
                    @lang('Detener Grabación') @{{showTimer}}
                </span>
                <span v-else>
                    <i class="fas fa-video"></i>
                    @lang('Iniciar Grabación')  @{{showTimer}} 
                </span>
            </button> 
        </div>
    </div>

</div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js"></script>
<script src="@mix('assets/js/dashboard/config-video.js')"></script>
@stop
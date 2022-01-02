@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@section('css')
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @lang('Pulse el botón grabar para iniciar la grabación de audio')
    <div class="page-title-subheading">
          @lang('Puede decir lo que quiera que se tome de ejemplo para las validaciones')
          @lang('de audio de sus documentos.')
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

        <a href="#" :class="!audios.length ? 'disabled': ''"
            @click.prevent="save" class="btn btn-xlg btn-success"
            data-save-request="@route('dashboard.config.audio')"
            data-redirect-request="@route('dashboard.config')"
            >
            @lang('Finalizar')
         </a>

        <a href="@route('dashboard.config')" class="btn btn-xlg btn-danger">
            @lang('Atrás')
        </a>

    </div>
    {{--/Los botones de Acción --}}

    {{-- Una tabla con el archivo de Audio --}}
    <div class="table-responsive">
        <table class="table">

            <thead>
                <tr>
                    <th class="text-center">@lang('Eliminar')</th>
                    <th>@lang('Archivo')</th>
                    <th>@lang('Tipo')</th>
                    <th>@lang('Duración') (s)</th>
                    <th>@lang('Tamaño') (kB)</th>
                    <th class="text-center">@lang('Escuchar')</th>
                </tr>
            </thead>

            <tbody v-if="!audios.length">
                <tr>
                    <td colspan="6" class="text-center">@lang('No hay grabaciones de Audio')</td>
                </tr>
            </tbody>

            <tbody v-else>
                <tr v-for="audio in audios">
                    <td class="text-center">
                        <button @click="removeAudio(audio)" class="btn btn-danger square">
                            <i class="fa fa-trash fa-2x"></i>
                        </button>
                    </td>
                    <td>@{{audio.filename}}</td>
                    <td>@{{audio.type}}</td>
                    <td>@{{audio.duration}}</td>
                    <td>@{{audio.size}}</td>
                    <td class="text-center">
                        <span v-if="audio.playing">
                            <button @click="stopAudio(audio)" class="btn btn-danger square">
                                <i class="fa fa-stop fa-2x"></i>
                            </button>
                        </span>
                        <span v-else>
                            <button @click="playAudio(audio)" class="btn btn-success square">
                                <i class="fa fa-play fa-2x"></i>
                            </button>          
                        </span>
                        <div class="text-center bold">
                            @{{audio.duration}}
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
        {{--/Una tabla con el archivo de Audio --}}

    </div>

    <div id="messages" class="d-none" data-audio-record-success="@lang('La grabación de audio ha finalizado con éxito')"></div>

    {{-- El botón que controla la grabación --}}
    <div class="col-md-12 text-right mb-4">
        <div class="btn-group" role="group">
            <button id="audio" @click="recordAudio()" class="btn" 
                :class="recording ? 'btn-danger' : 'btn-success'"
                data-max-record-time="@config('validations.audio.recordtime')"    
            >  
                <span v-if="recording">
                    <i class="fa fa-stop"></i>     
                    @lang('Detener Grabación') @{{showTimer}}
                </span>
                <span v-else>
                    <i class="fas fa-microphone"></i> 
                    @lang('Iniciar Grabación')  @{{showTimer}} 
                </span>
            </button> 
        </div>
    </div>
    {{--/El botón que controla la grabación --}}

</div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js"></script>
<script src="@mix('assets/js/dashboard/config-audio.js')"></script>
@stop
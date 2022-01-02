@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

    {{-- Css Personalizado --}}
@section('css')
    <link rel="stylesheet" href="/assets/css/dashboard/config.css" />
@stop

{{-- El encabezado con la ayuda para la página --}}
@section('help')
    <div>
        @lang('Esta es la pantalla donde puede controlar su configuración')
        <div class="page-title-subheading">
            @lang('Puede cambiar aquellas opciones que se ajusten más a sus necesidades')
        </div>
    </div>
@stop

{{-- El contenido de la página --}}
@section('content')

    <div v-cloak id="app" class="col-md-12 mb-3">
        {{-- Modales necesarias --}}
        @include('dashboard.modals.save-config-modal')
        {{-- /Modales necesarias --}}

        {{-- Los botones con las acciones --}}
        <div class="col-md-12 mb-4">
            <div class="btn-group" role="group">
                <a href="" @click.prevent="saveConfigModal" class="btn btn-lg btn-success mr-1">@lang('Guardar')</a>
                <a href="@route('dashboard.home')" class="btn btn-lg btn-danger">@lang('Cancelar')</a>
            </div>
        </div>
        {{-- /Los botones con las acciones --}}

        {{-- contenido al 80% --}}
        <div class="container-md">
            <div class="col-md-12">
                <form action="@route('dashboard.config.save')" data-after-save-redirect-to="@route('dashboard.home')"
                    method="post">

                    {{-- Configuración de las opciones de firma manuscrita --}}
                    <div class="col-md-12 mb-3 card">

                        <div class="card-header">
                            <h5 class="m-0 p-0">
                                <i class="fas fa-signature"></i>
                                @lang('Configuración de su firma manuscrita')
                            </h5>
                        </div>

                        <div class="card-body">

                            <div class="no-selection">
                                @lang('Trace aquí su firma que podrá ser utilizada en procesos de firma')
                            </div>

                            <div class="text-left mt-2">

                                <div id="sign-wrapper" class="sign-wrapper">

                                    {{-- La imagen de firma previamente guardada --}}
                                    <img id="sign-image" class="d-none" src="@exists($user->config->sign->sign)" alt="" />
                                    {{-- /La imagen de firma previamente guardada --}}

                                    {{-- El canvas de firma --}}
                                    <canvas id="sign" class="sign" class="bg-light font-weight-bold"></canvas>
                                    {{-- /El canvas de firma --}}

                                </div>

                                <div class="text-right mt-2">
                                    <button @click.prevent="clearSign" class="btn btn-danger">
                                        <i class="fa fa-trash"></i>
                                        @lang('Eliminar')
                                    </button>
                                </div>

                            </div>

                            <div class="text-left mt-2">
                                <label class="check-container">
                                    <span class="text bold">
                                        @lang('Utilizar en todos los procesos de firma')
                                    </span>
                                    @if ($user->config->sign->useAsDefault)
                                        <input id="use-as-default" v-model="sign.useAsDefault" type="checkbox"
                                            class="form-control" checked />
                                    @else
                                        <input id="use-as-default" v-model="sign.useAsDefault" type="checkbox"
                                            class="form-control" />
                                    @endif
                                    <span class="square-check"></span>
                                </label>
                            </div>

                        </div>

                    </div>
                    {{-- /Configuración de las opciones de firma manuscrita --}}

                    {{-- Configuración de las opciones de validación de grabación de Audio --}}
                    <div class="col-md-12 mb-3 card">

                        <div class="card-header">
                            <h5 class="m-0 p-0">
                                <i class="fas fa-volume-up"></i>
                                @lang('Configuración de las grabaciones de Audio')
                            </h5>
                        </div>

                        <div class="card-body">

                            <em>
                                @lang('El texto de referencia que deben leer los usuarios para validar el documento mediante
                                una
                                grabación de audio')
                            </em>

                            <div class="mt-2">
                                <p>
                                    <textarea id="audio-text" v-model.trim="audio.text" rows="8"
                                        class="bg-light font-weight-bold">{{ $user->config->audio->text }}</textarea>
                                </p>
                            </div>

                            <div class="mt-2 mb-2">
                                @lang('Puede proporcionar una grabación de audio personalizada de ejemplo para sus usuarios
                                como
                                guión para este tipo de validación').
                                @lang('A continuación puede escuchar un ejemplo')
                            </div>

                            {{-- Control de la reproducción de audio --}}
                            <div class="audio-controls w-50 text-right">
                                <audio v-if="audio.sample" id="audio-sample" :src="audio.sample"
                                    data-src="{{ $user->config->audio->sample }}" controls class="embed-responsive-item"
                                    data-toggle="tooltip" title="@lang('Reproducir la grabación')">
                                    <source :src="audio.sample" type="audio/x-wav" />
                                </audio>
                                <audio v-else controls class="embed-responsive-item">
                                    <source src="@asset('/assets/media/dashboard/audio/audio-validation-example.mp3')"
                                        type="audio/mp3" />
                                </audio>
                            </div>
                            {{-- /Control de la reproducción de audio --}}

                            {{-- El botón que controla la grabación de audio --}}
                            <div class="mt-2 text-right">
                                <div class="btn-group" role="group">

                                    <button v-if="audio.sample" @click.prevent="removeAudio()" class="btn btn-secondary">
                                        <i class="fas fa-trash"></i>
                                        @lang('Eliminar Grabación')
                                    </button>

                                    <button id="audio" @click.prevent="recordAudio()" class="btn ml-1"
                                        :class="audio.recording ? 'btn-danger' : 'btn-success'"
                                        data-max-record-time="@config('validations.audio.recordtime')">
                                        <span v-if="audio.recording">
                                            <i class="fa fa-stop"></i>
                                            @lang('Detener Grabación') @{{ showAudioTimer }}
                                        </span>
                                        <span v-else>
                                            <i class="fas fa-microphone"></i>
                                            @lang('Iniciar Grabación') @{{ showAudioTimer }}
                                        </span>
                                    </button>
                                </div>
                            </div>
                            {{-- /El botón que controla la grabación de audio --}}

                        </div>

                    </div>
                    {{-- /Configuración de las opciones de validación de grabación de Audio --}}

                    {{-- Configuración de las opciones de validación de grabación de Video --}}
                    <div class="col-md-12 mb-3 card">

                        <div class="card-header">
                            <h5 class="m-0 p-0">
                                <i class="fas fa-video"></i>
                                @lang('Configuración de las grabaciones de Video')
                            </h5>
                        </div>

                        <div class="card-body">
                            @lang('El texto de referencia que deben leer los usuarios para validar el documento mediante una
                            grabación de audio')

                            <div class="mt-2">
                                <p>
                                    <textarea id="video-text" v-model.trim="video.text" rows="8"
                                        class="bg-light font-weight-bold">{{ $user->config->video->text }}</textarea>
                                </p>
                            </div>

                            <div class="mt-2 mb-2">
                                @lang('Puede proporcionar una grabación de video personalizada de ejemplo para sus usuarios
                                como
                                guión para este tipo de validación')
                            </div>

                            {{-- Control de la reproducción de video --}}
                            <div class="video-controls">
                                <video v-if="video.sample || video.recording" id="video-sample" controls
                                    data-src="{{ $user->config->video->sample }}" class="embed-responsive-item"
                                    data-toggle="tooltip" title="@lang('Reproducir la grabación')">
                                    <source :src="video.sample" type="video/mp4" />
                                </video>
                            </div>
                            {{-- /Control de la reproducción de video --}}

                            {{-- El botón que controla la grabación de video --}}
                            <div class="mt-2 text-right">
                                <div class="btn-group" role="group">

                                    <button v-if="video.sample" @click.prevent="removeVideo()" class="btn btn-secondary">
                                        <i class="fas fa-trash"></i>
                                        @lang('Eliminar Grabación')
                                    </button>

                                    <button id="video" @click.prevent="recordVideo()" class="btn ml-1"
                                        :class="video.recording ? 'btn-danger' : 'btn-success'"
                                        data-max-record-time="@config('validations.video.recordtime')">
                                        <span v-if="video.recording">
                                            <i class="fa fa-stop"></i>
                                            @lang('Detener Grabación') @{{ showVideoTimer }}
                                        </span>
                                        <span v-else>
                                            <i class="fas fa-microphone"></i>
                                            @lang('Iniciar Grabación') @{{ showVideoTimer }}
                                        </span>
                                    </button>
                                </div>
                            </div>
                            {{-- /El botón que controla la grabación de video --}}

                        </div>
                    </div>

                    {{-- Configuración de las opciones de validación de documentos identificativos --}}
                    <div class="col-md-12 mb-3 card">

                        <div class="card-header">
                            <h5 class="m-0 p-0">
                                <i class="fas fa-address-card"></i>
                                @lang('Configuración de los Documentos Identificativos')
                            </h5>
                        </div>

                        <div class="card-body">
                            <div>
                                <em>
                                    @lang('Puede utilizar técnicas de reconocimiento facial mediantes redes neuronales para
                                    comprobar que el documento pertenece a la persona')
                                </em>
                            </div>
                            <div class="text-center mt-2">
                                <img class="img-fluid" src="@asset('/assets/images/dashboard/images/recognition-face.png')"
                                    alt="">
                            </div>
                            <div class="mt-2">
                                <strong>@lang('¿Cómo funciona?')</strong>
                            </div>
                            <div>
                                <em>
                                    @lang('El usuario, además de un documento que incluya una foto que le identifique, debe
                                    subir una foto frontal suya').
                                    @lang('La red neuronal de :app validará la coincidencia de la foto con la que presenta
                                    el
                                    documento', ['app' => config('app.name')])
                                </em>
                            </div>
                            <div class="mt-2">
                                <label class="check-container">
                                    <span class="text bold">
                                        @lang('Utilizar reconocimiento facial')
                                    </span>
                                    @if ($user->config->identificationDocument->useFacialRecognition)
                                        <input id="use-facial-recognition"
                                            v-model="identificationDocument.useFacialRecognition" type="checkbox"
                                            class="form-control" checked />
                                    @else
                                        <input id="use-facial-recognition"
                                            v-model="identificationDocument.useFacialRecognition" type="checkbox"
                                            class="form-control" />
                                    @endif
                                    <span class="square-check"></span>
                                </label>
                            </div>
                        </div>

                    </div>
                    {{-- /Configuración de las opciones de validación de documentos identificativos --}}


                    {{-- Configuración de las notificaciones --}}
                    <div class="col-md-12 mb-3 card">

                        <div class="card-header">
                            <h5 class="m-0 p-0">
                                <i class="fas fa-bell"></i>
                                @lang('Configuración de notificaciones')
                            </h5>
                        </div>

                        <div class="card-body">

                            {{-- Perioricidad de envío de notificaciones --}}
                            <div>
                                <em>
                                    @lang('Cuando envía un documento a firmar o crea una solicitud de documentos se envía un
                                    mensaje de correo al usuario').
                                    @lang('Si el usuario no responde a su solicitud puede enviar recordatorios con la
                                    cadencia
                                    deseada durante un periodo máximo de tiempo').
                                    @lang('Puede configurar aquí la periodicidad de esas notificaciones')
                                </em>
                            </div>
                            <div class="mt-2">
                                <select v-model="notification.days" id="notifications"
                                    data-selected="{{ $user->config->notification->days }}" class="form-control">
                                    <option value="1">@lang('Todos los días')</option>
                                    <option value="2">@lang('Cada 2 días')</option>
                                    <option value="3">@lang('Cada 3 días')</option>
                                    <option value="4">@lang('Cada 4 días')</option>
                                    <option value="5">@lang('Cada 5 días')</option>
                                    <option value="6">@lang('Cada 6 días')</option>
                                    <option value="7">@lang('Cada semana')</option>
                                </select>
                            </div>
                            <div class="bold mt-2">
                                @lang('Las notificaciones se enviarán a lo largo de un periodo máximo de 15 días')
                            </div>
                            {{-- /Perioricidad de envío de notificaciones --}}

                            <hr />

                            {{-- Recibir notificaciones por email --}}
                            <div>
                                <em>
                                    @lang('Puede elegir si desea recibir notificaciones por email de seguimiento durante el
                                    proceso de firma y validación de documentos')
                                </em>
                                <div class="mt-2">
                                    <label class="check-container">
                                        <span class="text bold">
                                            @lang('No recibir notificaciones durante el proceso por email')
                                        </span>
                                        @if ($user->config->notification->receive)
                                            <input id="receive-notifications" v-model="notification.receive" type="checkbox"
                                                class="form-control" checked />
                                        @else
                                            <input id="receive-notifications" v-model="notification.receive" type="checkbox"
                                                class="form-control" />
                                        @endif
                                        <span class="square-check"></span>
                                    </label>
                                </div>
                            </div>
                            {{-- /Recibir notificaciones por email --}}

                        </div>
                    </div>
                    {{-- / Configuración de las notificaciones --}}

                </form>
            </div>
        </div>

        {{-- Los botones con las acciones --}}
        <div class="col-md-12 mb-4">
            <div class="btn-group" role="group">
                <a href="@route('dashboard.document.list')" @click.prevent="saveConfig"
                    class="btn btn-lg btn-success mr-1">@lang('Guardar')</a>
                <a href="@route('dashboard.home')" class="btn btn-lg btn-danger">@lang('Cancelar')</a>
            </div>
        </div>
        {{-- /Los botones con las acciones --}}

    </div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')

    {{-- Signature Pad 
    @link https://github.com/szimek/signature_pad --}}
    <script src="@asset('assets/js/dashboard/vendor/signature_pad.umd.min.js')"></script>

    {{-- Moment.js --}}
    <script src="@asset('assets/js/libs/moment-with-locales.min.js')"></script>

    {{-- El script de la página --}}
    <script src="@mix('assets/js/dashboard/config.js')"></script>
@stop

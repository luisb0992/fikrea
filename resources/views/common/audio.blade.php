<div v-cloak id="app" class="col-md-12">

    {{-- Mensaje que se le pasa a Vue JS --}}
    <div id="messages" class="d-none"
        data-audio-record-success="@lang('La grabación de audio ha finalizado con éxito')"></div>
    {{-- /Mensaje que se le pasa a Vue JS --}}

    {{-- El registro de la visita del usuario --}}
    <div id="visit" data-visit="@json($visit)"></div>
    {{-- /El registro de la visita del usuario --}}

    {{-- Los botones de Acción --}}
    <div class="row col-md-12 mt-2 mb-2">
        <div class="btn-group" role="group">

            @isset($token)
                <a href="#" :class="!audios.length ? 'disabled': ''" @click.prevent="save"
                    class="btn btn-lg btn-success mr-1"
                    data-save-request="@route('workspace.save.audio', ['token' => $token])"
                    data-redirect-request="@route('workspace.home', ['token' => $token])">
                    @lang('Finalizar')
                </a>

                <a href="@route('workspace.home', ['token' => $token])" class="btn btn-lg btn-danger">
                    @lang('Atrás')
                </a>
            @else
                <a href="#" :class="!audios.length ? 'disabled': ''" @click.prevent="save"
                    class="btn btn-lg btn-success mr-1"
                    data-save-request="@route('dashboard.audio.save', ['id' => $signer->document->id])"
                    data-redirect-request="@route('dashboard.document.status', ['id' => $signer->document->id])">
                    @lang('Finalizar')
                </a>

                <a href="@route('dashboard.document.status', ['id' => $signer->document->id])"
                    class="btn btn-lg btn-danger">
                    @lang('Atrás')
                </a>
            @endisset

            {{--  modal para agregar o ver un comentario  --}}
            @include('common.comments.button-add-comment-modal', [
                'validationType' => config('validations.document-validations.audio')
            ])
        </div>
    </div>
    {{-- /Los botones de Acción --}}

    {{-- El texto de referencia que se debe decir --}}
    <div class="col-md-12 my-4">
        <div class="alert alert-info p-4">
            <h4 class="alert-heading mb-4">
                <i class="fas fa-info-circle"></i> @lang('A continuación diga el siguiente texto'):
            </h4>
            <span class="text-justify font-weight-bolder" style="line-height: 2.0">"{{ $audioText }}"</span>
            <hr>
            <div class="d-flex align-items-center justify-content-end">
                <div class="d-none d-sm-block">
                    <small class="text-muted mr-2 font-italic">@lang('Audio de ejemplo')</small>
                </div>
                <div>
                    <audio controls class="embed-responsive-item">
                        @if ($audioSample)
                            {{-- Si se ha proporcionado un ejemplo de audio personalizado se muestra --}}
                            <source src="{{ $audioSample }}" type="audio/x-wav" />
                        @else
                            {{-- En caso contrario se muestra el audio de ejemplo --}}
                            <source src="@asset('/assets/media/dashboard/audio/audio-validation-example.mp3')"
                                type="audio/mp3" />
                        @endif
                    </audio>
                </div>
            </div>
        </div>
    </div>
    {{-- /El texto de referencia que se debe decir --}}

    {{-- El botón que controla la grabación --}}
    <div class="col-md-12 mb-4">
        <div class="btn-group" role="group">
            <button id="audio" @click="recordAudio()" class="btn" :class="recording ? 'btn-danger' : 'btn-primary'"
                data-max-record-time="@config('validations.audio.recordtime')">
                <span v-if="recording">
                    <i class="fa fa-stop"></i>
                    @lang('Detener Grabación') @{{ showTimer }}
                </span>
                <span v-else>
                    <i class="fas fa-microphone"></i>
                    <span v-if="audios.length">
                        @lang('¿Grabar otro audio?')
                        <span v-if="pause">
                            @{{ pauseTimer }}
                        </span>
                        <span v-else>
                            @{{ showTimer }}
                        </span>
                    </span>
                    <span v-else>
                        @lang('Iniciar Grabación') @{{ showTimer }}
                    </span>
                </span>
            </button>
            <button @click="pauseAudio()" class="btn ml-2" :class="pause ? 'btn-success' : 'btn-info'" v-if="recording">
                <span v-if="pause">
                    <i class="fa fa-play"></i> @lang('Reanudar Grabación')
                </span>
                <span v-else>
                    <i class="fa fa-pause"></i> @lang('Pausar Grabación')
                </span>
            </button>
        </div>
    </div>
    {{-- /El botón que controla la grabación --}}

    {{-- Una tabla con los archivos de Audio --}}
    <div class="col-md-12 mb-4">
        <div class="table-responsive card">
            <table class="table table-striped">
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
                        <td colspan="6" class="text-center text-danger bold">@lang('No hay grabaciones de audio')</td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr v-for="audio in audios">
                        <td data-label="@lang('Eliminar')" class="text-center">
                            <button @click="removeAudio(audio)" class="btn btn-danger square">
                                <i class="fa fa-trash fa-2x"></i>
                            </button>
                        </td>
                        <td data-label="@lang('Archivo')">@{{ audio . filename }}</td>
                        <td data-label="@lang('Tipo')">@{{ audio . type }}</td>
                        <td data-label="@lang('Duración')">@{{ audio . duration }}</td>
                        <td data-label="@lang('Tamaño')">@{{ audio . size }}</td>
                        <td data-label="@lang('Escuchar')" class="text-center">
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
                                @{{ audio . duration }}
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
    </div>
    {{-- / Una tabla con los archivos de Audio --}}

</div>

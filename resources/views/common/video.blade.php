<div v-cloak id="app" class="col-md-12">

    <div id="messages" class="d-none"
        data-video-record-success="@lang('La grabación de video ha finalizado con éxito')"></div>

    {{-- El registro de la visita del usuario --}}
    <div id="visit" data-visit="@json($visit)"></div>
    {{-- /El registro de la visita del usuario --}}

    {{-- Los botones de Acción --}}
    <div class="col-md-12 mb-4">

        <div class="btn-group" role="group">
        @isset($token)
            <a href="#" :class="!videos.length ? 'disabled': ''" @click.prevent="save"
                class="btn btn-lg btn-success mr-1"
                data-save-request="@route('workspace.video.save', ['token' => $token])"
                data-redirect-request="@route('workspace.home', ['token' => $token])">
                @lang('Finalizar')
            </a>

            <a href="@route('workspace.home', ['token' => $token])" class="btn btn-lg btn-danger">
                @lang('Atrás')
            </a>
        @else
            <a href="#" :class="!videos.length ? 'disabled': ''" @click.prevent="save"
                class="btn btn-lg btn-success mr-1"
                data-save-request="@route('dashboard.video.save', ['id' => $signer->document->id])"
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
                'validationType' => config('validations.document-validations.video')
            ])
        </div>

    </div>
    {{-- / Los botones de Acción --}}

    {{-- El texto de referencia que se debe decir --}}
    <div class="col-md-12 my-4">
        <div class="alert alert-info p-4">
            <h4 class="alert-heading mb-4">
                <i class="fas fa-info-circle"></i> @lang('A continuación diga el siguiente texto'):
            </h4>
            <span class="text-justify font-weight-bolder" style="line-height: 2.0">"{{ $videoText }}"</span>
            <hr>
            {{-- Si se ha proporcionado un ejemplo de audio personalizado se muestra --}}
            @if ($videoSample)
                <div class="text-center">
                    <video controls class="embed-responsive-item">
                        <source src="{{ $videoSample }}" type="video/mp4" />
                    </video>
                    <div>
                        <em>@lang('El siguiente video de ejemplo le muestra como realizar esta validación')</em>
                    </div>
                </div>
            @endif
        </div>
    </div>
    {{-- /El texto de referencia que se debe decir --}}

    {{-- La ventana de video --}}
    <div class="col-md-12 text-center">
        <video id="video-screen" autoplay></video>
    </div>
    {{-- /La ventana de video --}}

    {{-- Control de la grabación de un video --}}
    <div class="col-md-12 mb-4">
        <div class="btn-group" role="group">
            <button id="video" @click.prevent="recordVideo()" class="btn" :class="recording ? 'btn-danger' : 'btn-primary'"
                data-max-record-time="@config('validations.video.recordtime')">
                <span v-if="recording">
                    <i class="fa fa-stop"></i>
                    @lang('Detener Grabación') @{{ showTimer }}
                </span>
                {{--  <span v-else>
                    <i class="fas fa-video"></i>
                    @lang('Iniciar Grabación') @{{ showTimer }}
                </span>  --}}
                <span v-else>
                    <i class="fas fa-video"></i>
                    <span v-if="videos.length">
                        @lang('¿Grabar otro video?')
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
            <button @click="pauseVideo()" class="btn ml-2" :class="pause ? 'btn-success' : 'btn-info'" v-if="recording">
                <span v-if="pause">
                    <i class="fa fa-play"></i> @lang('Reanudar Grabación')
                </span>
                <span v-else>
                    <i class="fa fa-pause"></i> @lang('Pausar Grabación')
                </span>
            </button>
        </div>
    </div>
    {{-- /Control de la grabación de un video --}}

    {{-- Una tabla con los archivos de Video --}}
    <div class="col-md-12 my-4">
        <div class="table-responsive card">
            <table class="table table-striped">

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
                        <td data-label="@lang('Eliminar')" class="text-center">
                            <button @click.prevent="removeVideo(video)" class="btn btn-danger square">
                                <i class="fa fa-trash fa-2x"></i>
                            </button>
                        </td>
                        <td data-label="@lang('Archivo')">@{{ video . filename }}</td>
                        <td data-label="@lang('Tipo')">@{{ video . type }}</td>
                        <td data-label="@lang('Duración')">@{{ video . duration }}</td>
                        <td data-label="@lang('Tamaño')">@{{ video . size }}</td>
                        <td data-label="@lang('Ver')" class="text-center">
                            <video :src="video.video" class="video-thumb" controls></video>
                            <div class="text-center bold">
                                @{{ video . duration }}
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
                        <th class="text-center">@lang('Ver')</th>
                    </tr>
                </tfoot>

            </table>
        </div>
    </div>
    {{-- /Una tabla con los archivos de Video --}}

</div>

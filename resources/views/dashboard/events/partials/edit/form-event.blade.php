{{-- Cuerpo del formulario para el evento --}}
<div class="col-md-12 mb-4">
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="card-title mt-2 py-2">
                <div>
                    <i class="fas fa-list-alt"></i>
                    @lang('Datos del evento')
                </div>
                <div>
                    <small>
                        <span class="text-primary">(*)</span> <span class="text-info">@lang('Campos requeridos')</span>
                    </small>
                </div>
            </h5>
        </div>

        <div class="card-body w-100">

            {{-- Titulo --}}
            <div class="form-group">
                <label for="title">@lang('Título') <span class="text-primary">*</span></label>
                <input type="text" v-model="event.title" id="title" class="form-control"
                    placeholder="@lang('Un título identificativo para el evento')">
            </div>

            {{-- Descripcion --}}
            <div class="form-group mt-1">
                <label for="description">@lang('Descripción')</label>
                <textarea v-model="event.description" id="description" rows="5" class="form-control"
                    placeholder="@lang('Una descripción para el evento opcional')"></textarea>
            </div>

            {{-- Imagen --}}
            <div class="form-group mt-1">
                <label>@lang('Archivo de imagen')</label>
                <br>

                {{-- cambiar a url o seleccion de imagen --}}
                <button type="button" class="btn mb-2" :class="states.isUrlImage ? 'btn-info' : 'btn-primary'"
                    @click="insertUrlImage">
                    <span v-if="states.isUrlImage">
                        @lang('Seleccionar imagen')
                    </span>
                    <span v-else>
                        @lang('Insertar URL de la imagen')
                    </span>
                </button>

                {{-- imagen por url --}}
                <div v-if="states.isUrlImage">

                    {{-- input text --}}
                    <input type="text" v-model="event.imagen" placeholder="@lang('Insertar URL')" class="form-control">

                    {{-- previzualizar la imagen por url
                        La url se carga en la propiedad event.imagen --}}
                    <div v-show="event.imagen" class="mt-1">
                        <b-img :src="event.imagen" fluid alt="event-header" style="max-width: 30%; max-height: 400px;">
                        </b-img>
                    </div>
                </div>

                {{-- video por seleccion --}}
                <div v-else>

                    {{-- input file --}}
                    <b-form-file v-model="event.imagen" placeholder="@lang('Seleccionar imagen')"
                        @input="checkFileImage">
                    </b-form-file>

                    {{-- preview del video seleccionado --}}
                    <div v-show="preview.image" class="mt-2">
                        <b-img :src="preview.image" fluid alt="event-header" style="max-width: 30%; max-height: 400px;">
                        </b-img>
                        <br>
                        <button type="button" class="btn btn-danger mt-2" @click="deleteImage">
                            <i class="fas fa-times"></i>
                            @lang('Eliminar imagen')
                        </button>
                    </div>

                    {{-- si el archivo es invalido --}}
                    <small class="form-text text-danger" v-if="!states.validImage && !states.isUrlImage">
                        <i class="fas fa-exclamation-triangle"></i>
                        @lang('El archivo seleccionado es inválido')
                    </small>

                    {{-- nombre del archivo seleccioando --}}
                    <div v-show="preview.image">
                        @lang('Archivo seleccioando'): @{{ event . imagen ? event . imagen . name : '' }}
                    </div>
                </div>

                {{-- mensaje informativo --}}
                <small class="form-text text-info">
                    <i class="fas fa-info-circle"></i>
                    @lang('Utilice una imagen png, jpg o gif (tamaño máximo 5 Mb)')
                </small>
            </div>

            {{-- Video --}}
            <div class="form-group mt-1">
                <label>@lang('Archivo de video')</label>
                <br>

                {{-- cambiar a url o seleccion de video --}}
                <button type="button" class="btn mb-2" :class="states.isUrlVideo ? 'btn-info' : 'btn-primary'"
                    @click="insertUrlVideo">
                    <span v-if="states.isUrlVideo">
                        @lang('Seleccionar video')
                    </span>
                    <span v-else>
                        @lang('Insertar URL del video')
                    </span>
                </button>

                {{-- video por url --}}
                <div v-if="states.isUrlVideo">

                    {{-- input text --}}
                    <input type="text" v-model="event.video" placeholder="@lang('Insertar URL')" class="form-control">

                    {{-- previzualizar el video por url
                        La url se carga en la propiedad event.video --}}
                    <div v-show="event.video" style="max-width: 30%; max-height: 400px;">
                        <video-embed :src="event.video"></video-embed>
                    </div>

                    {{-- mensaje informativo --}}
                    <small class="form-text text-info">
                        <i class="fas fa-info-circle"></i>
                        @lang('Las plataformas soportadas son YouTube, Vimeo, Dailymotion, Coub.')
                    </small>
                </div>

                {{-- video por seleccion --}}
                <div v-else>

                    {{-- input file --}}
                    <b-form-file v-model="event.video" placeholder="@lang('Seleccionar video')" @input="checkVideoFile">
                    </b-form-file>

                    {{-- preview del video seleccionado --}}
                    <div v-show="preview.video" class="mt-2">
                        <video :src="preview.video" controls style="max-width: 30%; max-height: 400px;"></video>
                    </div>

                    {{-- si el archivo es invalido --}}
                    <small class="form-text text-danger" v-if="!states.validVideo && !states.isUrlVideo">
                        <i class="fas fa-exclamation-triangle"></i>
                        @lang('El archivo seleccionado es inválido')
                    </small>

                    {{-- nombre del archivo seleccioando --}}
                    <div v-show="event.video && !states.isUrlVideo">
                        @lang('Archivo seleccioando'): @{{ event . video ? event . video . name : '' }}
                    </div>

                    {{-- mensaje informativo --}}
                    <small class="form-text text-info">
                        <i class="fas fa-info-circle"></i>
                        @lang('Utilice un video mp4, avi o mkv')
                    </small>
                </div>
            </div>

            {{-- fecha inicial --}}
            <div class="d-flex mt-1">
                <div class="form-group flex-fill mr-4">
                    <label for="startDate">@lang('Fecha inicial del evento') <span class="text-primary">*</span></label>
                    <b-form-datepicker id="startDate" v-model="event.startDate"
                        placeholder="@lang('La fecha cuando inicia el evento')" locale="es" :hide-header="true"
                        min="{{ date('Y-m-d') }}" @input="endDateHigher"></b-form-datepicker>
                </div>
                <div class="form-group flex-fill">
                    <label for="startTime">@lang('Hora inicial del evento')</label>
                    <b-form-timepicker id="startTime" v-model="event.startTime"
                        placeholder="@lang('Hora inicial del evento')" locale="es" :hide-header="true" no-close-button>
                    </b-form-timepicker>
                </div>
            </div>

            {{-- fecha final --}}
            <div class="d-flex mt-1">

                {{-- fecha final --}}
                <div class="form-group flex-fill" :class="!event.endDate ? 'mr-4' : ''">
                    <label for="endDate">@lang('Fecha final del evento')</label>
                    <b-form-datepicker id="endDate" v-model="event.endDate"
                        placeholder="@lang('La fecha cierre del evento')" locale="es" :hide-header="true"
                        min="{{ date('Y-m-d') }}" @input="endDateHigher">
                    </b-form-datepicker>
                    <small class="form-text text-danger" v-if="!states.stateDatePicker">
                        <i class="fas fa-exclamation-triangle"></i>
                        @lang('La fecha final no puede ser menor o igual a la fecha inicial.')
                    </small>
                </div>

                {{-- boton limpiar fecha final --}}
                <div class="form-group mr-4 ml-1" v-if="event.endDate">
                    <label for="clearEndDateAndTime">.</label>
                    <br>
                    <b-button v-b-tooltip.hover variant="danger" title="@lang('Limpiar la fecha final del evento')"
                        @click="clearEndDateAndTime" id="clearEndDateAndTime">
                        <i class="fas fa-times"></i>
                    </b-button>
                </div>

                {{-- hora final --}}
                <div class="form-group flex-fill">
                    <label for="endTime">@lang('Hora final del evento')</label>
                    <b-form-timepicker id="endTime" v-model="event.endTime" placeholder="@lang('Hora final del evento')"
                        locale="es" :hide-header="true" no-close-button :disabled="!event.endDate"></b-form-timepicker>
                </div>
            </div>

            {{-- mensaje informativo sobre fechas --}}
            <div class="mt-n3 mb-2">
                <small class="form-text text-info">
                    <i class="fas fa-info-circle"></i>
                    @lang('Puede dejar la fecha y la hora final en blanco y agregarla después.')
                </small>
            </div>

            {{-- meta maxima y minima --}}
            <div class="d-flex mt-1">
                <div class="form-group flex-fill mr-4">
                    <label for="minGoal">@lang('Meta mínima a obtener')</label>
                    <input type="text" name="min_goal" id="minGoal" class="form-control"
                        placeholder="@lang('La meta mínima a obtener')" pattern="[0-9]+"
                        title="@lang('Ingrese solo números')" max="9999999" v-model="event.minGoal"
                        @input="highestMaximumGoal">
                </div>

                <div class="form-group flex-fill">
                    <label for="maxGoal">@lang('Meta máxima esperada')</label>
                    <input type="text" name="max_goal" id="maxGoal" class="form-control"
                        placeholder="@lang('La meta máxima esperada')" pattern="[0-9]+"
                        title="@lang('Ingrese solo números')" max="9999999" v-model="event.maxGoal"
                        @input="highestMaximumGoal">
                </div>
            </div>

            {{-- mensaje error sobre metas --}}
            <div>
                <div class="mt-n3" v-if="!states.goalAsNumber">
                    <small class="form-text text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        @lang('Debe introducir solo números positivos.')
                    </small>
                </div>
                <div class="mt-n3" v-if="!states.maximumGoalIsHigher">
                    <small class="form-text text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        @lang('El valor de la meta máxima esperada debe ser mayor al de la meta mínima.')
                    </small>
                </div>
            </div>

            {{-- finalidad del evento --}}
            <div class="form-group mt-1">
                <label for="purpose">@lang('Propósito del evento') <span class="text-primary">*</span></label>
                <select class="form-control" id="purpose" v-model="event.purpose">
                    @foreach ($purposes as $purpose)
                        <option value="{{ $purpose->id }}" seleted>@lang($purpose->name)</option>
                    @endforeach
                </select>
            </div>

            {{-- tipo de evento --}}
            <div class="form-group mt-1">
                <label for="eventType">@lang('Tipo de evento') <span class="text-primary">*</span></label>
                <div class="d-flex justify-content-start">
                    <div class="shadow-sm rounded border text-center p-4 mr-3">
                        <label for="vote">
                            <input type="radio" v-model="event.type" name="eventType" :value="eventTypes.vote" id="vote"
                                class="mb-3 scale-2-5">
                            <br>
                            {{ new \App\Enums\Event\EventType(\App\Enums\Event\EventType::VOTE) }}
                            <br>
                            <div class="mt-2">
                                <i class="fas fa-vote-yea fa-3x text-danger"></i>
                            </div>
                        </label>
                    </div>

                    <div class="shadow-sm rounded border text-center p-4 mr-3">
                        <label for="survey">
                            <input type="radio" v-model="event.type" name="eventType" :value="eventTypes.survey"
                                id="survey" class="mb-3 scale-2-5">
                            <br>
                            {{ new \App\Enums\Event\EventType(\App\Enums\Event\EventType::SURVEY) }}
                            <br>
                            <div class="mt-2">
                                <i class="fas fa-clipboard-list fa-3x text-primary"></i>
                            </div>
                        </label>
                    </div>

                    <div class="shadow-sm rounded border text-center p-4 mr-3">
                        <label for="signatureCollection">
                            <input type="radio" v-model="event.type" name="eventType" :value="eventTypes.signature"
                                id="signatureCollection" class="mb-3 scale-2-5">
                            <br>
                            {{ new \App\Enums\Event\EventType(\App\Enums\Event\EventType::SIGNATURE_COLLECTION) }}
                            <br>
                            <div class="mt-2">
                                <i class="fas fa-file-signature fa-3x text-info"></i>
                            </div>
                        </label>
                    </div>

                    <div class="shadow-sm rounded border text-center p-4">
                        <label for="surveyAndSignatureCollection">
                            <input type="radio" v-model="event.type" name="eventType"
                                :value="eventTypes.surveyAndSignature" id="surveyAndSignatureCollection"
                                class="mb-3 scale-2-5">
                            <br>
                            {{ new \App\Enums\Event\EventType(\App\Enums\Event\EventType::SURVEY_AND_SIGNATURE_COLLECTION) }}
                            <br>
                            <div class="mt-2">
                                <i class="fas fa-clipboard-list fa-3x mr-1 text-primary"></i>
                                <i class="fas fa-file-signature fa-3x text-info"></i>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- mensajes informativos para cada tipo de evento --}}
                <div v-if="event.type === eventTypes.vote">
                    <small class="form-text text-info">
                        <i class="fas fa-info-circle"></i>
                        @lang('Las votaciones son privadas y anónimas.')
                    </small>
                </div>
                <div v-else-if="event.type === eventTypes.survey">
                    <small class="form-text text-info">
                        <i class="fas fa-info-circle"></i>
                        @lang('Las encuestas son públicas y puede elegir su anonimato.')
                    </small>
                </div>
                <div v-else-if="event.type === eventTypes.signature">
                    <small class="form-text text-info">
                        <i class="fas fa-info-circle"></i>
                        @lang('Las recogidas de firmas son públicas y puede elegir su anonimato.')
                    </small>
                </div>
                <div v-else-if="event.type === eventTypes.surveyAndSignature">
                    <small class="form-text text-info">
                        <i class="fas fa-info-circle"></i>
                        @lang('Las encuestas y recogidas de firmas son públicas y puede elegir su anonimato.')
                    </small>
                </div>
            </div>

            {{-- evento publico --}}
            <div class="form-group mt-1" v-if="false">
                <label for="purpose">@lang('Clase de evento')</label>
                <div class="d-flex justify-content-start">
                    <div class="shadow-sm rounded border text-center p-4 mr-3">
                        <label for="isPublic">
                            <input v-model="event.isPublic" type="radio" name="eventIsPublic" value="1" id="isPublic"
                                class="mb-3 scale-2-5">
                            <br>
                            @lang('Evento público')
                            <br>
                            <div class="mt-2">
                                <i class="fas fa-lock-open fa-3x text-primary"></i>
                            </div>
                        </label>
                    </div>
                    <div class="shadow-sm rounded border text-center p-4 mr-3">
                        <label for="isPrivate">
                            <input v-model="event.isPublic" type="radio" name="eventIsPublic" value="0" id="isPrivate"
                                class="mb-3 scale-2-5">
                            <br>
                            @lang('Evento privado')
                            <br>
                            <div class="mt-2">
                                <i class="fas fa-lock fa-3x text-primary"></i>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- respuestas anonimas --}}
            <div class="form-group mt-1" v-if="event.type && (event.type != eventTypes.vote)">
                <label for="purpose">@lang('Respuestas anónimas')</label>
                <div class="d-flex justify-content-start">
                    <div class="shadow-sm rounded border text-center p-4 mr-3">
                        <label for="isAnonimus">
                            <input v-model="event.isAnonymous" type="radio" name="eventIsAnonimus" value="1"
                                id="isAnonimus" class="mb-3 scale-2-5">
                            <br>
                            @lang('Anónimo')
                            <br>
                            <div class="mt-2">
                                <i class="fas fa-user-times fa-3x text-primary"></i>
                            </div>
                        </label>
                    </div>
                    <div class="shadow-sm rounded border text-center p-4 mr-3">
                        <label for="isNotAnonimus">
                            <input v-model="event.isAnonymous" type="radio" name="eventIsAnonimus" value="0"
                                id="isNotAnonimus" class="mb-3 scale-2-5">
                            <br>
                            @lang('Sabido')
                            <br>
                            <div class="mt-2">
                                <i class="fas fa-user-check fa-3x text-primary"></i>
                            </div>
                        </label>
                    </div>
                </div>
                <small class="form-text text-info">
                    <i class="fas fa-info-circle"></i>
                    @lang('Mostrara o no las respuestas de las personas involucradas, aun así, se mostrara reflejado
                    en el resultado final')
                </small>
            </div>

            {{-- Modo kiosco --}}
            <div class="form-group mt-1" v-if="false">
                <label for="kioskMode">@lang('Modo kiosco')</label>
                <div class="d-flex justify-content-start">
                    <div class="shadow-sm rounded border text-center p-4 mr-3">
                        <label for="kioskModeOn">
                            <input v-model="event.kioskMode" type="radio" name="kioskMode" value="1" id="kioskModeOn"
                                class="mb-3 scale-2-5">
                            <br>
                            @lang('Activar')
                            <br>
                            <div class="mt-2">
                                <i class="fas fa-check-square fa-3x text-primary"></i>
                            </div>
                        </label>
                    </div>
                    <div class="shadow-sm rounded border text-center p-4 mr-3">
                        <label for="kioskModeOff">
                            <input v-model="event.kioskMode" type="radio" name="kioskMode" value="0" id="kioskModeOff"
                                class="mb-3 scale-2-5">
                            <br>
                            @lang('Desactivar')
                            <br>
                            <div class="mt-2">
                                <i class="fas fa-calendar-times fa-3x text-primary"></i>
                            </div>
                        </label>
                    </div>
                </div>
                <small class="form-text text-info">
                    <i class="fas fa-info-circle"></i>
                    @lang('El modo kiosco le permitirá llenar los resultados del evento sin tener ninguna
                    limitación.')
                    @lang('Tenga en cuenta que será usted quien recoja cada resultado y no las personas
                    involucradas.')
                </small>
            </div>
        </div>
    </div>
</div>

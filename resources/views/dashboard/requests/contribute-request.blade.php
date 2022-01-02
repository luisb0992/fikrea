<div class="main-card mb-3 card">
    <div class="card-body">
        <h5 class="card-title">@lang('Documento a solicitar')</h5>

        <hr>

        {{-- Formulario con los campos de un nuevo documento a requerir --}}
        <b-container fluid>
          
            <div>

                <b-form @submit.stop.prevent="addDocument">

                    {{-- El nombre del documento --}}
                    <b-form-group id="document-request" label="@lang('Documento') *" label-for="document-request">

                    	{{-- Mostrar cuando no se ha seleccionado el tipo de documento --}}
                        <span v-if="!validateDocumentState('name')" class="text-danger">
                            @lang('Debe seleccionar el tipo de documento a solicitar')
                        </span>

                        @if ($documentExamples)
                            {{-- La lista de documentos requeridos de ejemplo --}}
                            <div id="document-examples" data-documents="@json($documentExamples)"></div>
                            {{-- Componente Select2
                                @link https://github.com/godbasin/vue-select2

                                Para las opciones originales del Select2@hasSection ('
                                @link https://select2.org/configuration/options-api')
                            --}}

                            <Select2 
                                id="document"
                                class="select2"
                                :options="documentExamples"
                                :settings="{tags: true}" 
                                v-model="document.name"
                            />
                        @else 
                            {{-- Si no hay documentos requeridos de ejemplo --}}
                            <input id="document" v-model="document.name" class="form-control" placeholder="@lang('Documento que se solicita. Ejemplo: DNI.')" />
                        @endif
                    </b-form-group>
                    {{--/El nombre del documento --}}

                    {{-- El comentario del documento --}}
                    <b-form-group id="comment-request" label="@lang('Comentario')" label-for="comment-request">
                        
                        <b-form-textarea 
                                id="comment-request-input"
                                placeholder="@lang('Comentario')"
                                v-model="document.comment"
                        ></b-form-textarea>

                    </b-form-group>
                    {{-- /El comentario del documento --}}

                    {{-- El tipo de documento --}}
                    <b-form-group id="type-request" label="@lang('Tipo de archivo')" label-for="type-request">
                         
                        <select v-model="document.type" class="form-control">
                            <option value="" selected>@lang('Cualquiera')</option>
                            @foreach ($documentTypes as $documentType)
                                <option value="{{$documentType}}">
                                    @lang($documentType)
                                </option>
                            @endforeach
                        </select>
                    </b-form-group>
                    {{-- /El tipo de documento --}}

                    {{-- El periodo máximo de validez del documento --}}
                    <b-form-group id="type-request" label="@lang('Fecha de emisión')" label-for="type-request">
                        
                        {{-- Selección de día|mes|año --}}
                        <div class="text-right mb-2">
                            <div class="btn-group">

                                {{-- Selecciona un periodo en días --}}
                                <button 
                                	type="button"
                                	class="btn"
                                	@click.prevent="selectValidity(document, 0, TimeUnit.Days)"
                                    :class="isDaysSelected? 'btn-success': 'btn-secondary'"
                                >
                                    @lang('días')
                                </button>
                                {{-- Selecciona un periodo en meses --}}
                                <button type="button" class="btn" @click.prevent="selectValidity(document, 0, TimeUnit.Months)"
                                    :class="isMonthsSelected? 'btn-success': 'btn-warning'"
                                >
                                    @lang('meses')
                                </button>
                                {{-- Selecciona un periodo en años --}}
                                <button type="button" class="btn" @click.prevent="selectValidity(document, 0, TimeUnit.Years)"
                                    :class="isYearsSelected? 'btn-success': 'btn-info'"
                                >
                                    @lang('años')
                                </button>
                            </div>
                        </div>
                        {{-- / Selección de día|mes|año --}}

                        {{-- Texto con la descripción de la validez seleccionada --}}
                        <div v-if="document.validity > 0" class="col-md-12 bold btn btn-outline-primary p-2 no-events">
                            <span v-if="document.validity_unit == TimeUnit.Days"> 
                                @{{document.validity}} 
                                @lang('día(s) antes de la fecha actual')
                                <label class="text-success">
                                [ @lang('desde') @{{dateInit}} @lang('hasta hoy') ]
                                </label>
                            </span>
                            <span v-else-if="document.validity_unit == TimeUnit.Months">
                                @{{document.validity}} 
                                @lang('mes(es) antes de la fecha actual')
                                <label class="text-success">
                                [ @lang('desde') @{{dateInit}} @lang('hasta hoy') ]
                                </label>
                            </span>
                            <span v-else-if="document.validity_unit == TimeUnit.Years">
                                @{{document.validity}} 
                                @lang('año(s) antes de la fecha actual')
                                <label class="text-success">
                                [ @lang('desde') @{{dateInit}} @lang('hasta hoy') ]
                                </label>
                            </span>
                            <span v-else>
                                @lang('Sin determinar')
                            </span>
                        </div>
                        <div v-else class="col-md-12 bold btn btn-outline-primary p-2 no-events">
                            <span> 
                                @lang('Sin definir')
                            </span>
                        </div>
                        {{-- /Texto con la descripción de la validez seleccionada --}}

                        {{-- Input range para seleccionar valor entre 1 y 60 o 1 y 12 --}}
                        <input type="range" class="form-control" v-model="document.validity"
                        	min="0" :max="document.validity_unit == TimeUnit.Days ? 60 : 12"
                        	@change="selectValidity(document, document.validity, document.validity_unit)"
                        	value="0" />
                        <div class="text-info">
                            @lang('Deslice el control o seleccione un periodo determinado de validez del documento desde la fecha actual')
                        </div>
                        {{-- /Input range para seleccionar valor entre 1 y 60 o 1 y 12 --}}

                        {{-- Botones con intervalos pre-estalecidos --}}
                        <div class="col-md-12 text-center mt-2">

                        	{{-- Días --}}
                            <button type="button"
                            	@click.prevent="selectValidity(document, 7, TimeUnit.Days)"
                            	class="btn m-1"
                            	:class="document.validity == 7 ? 'btn-success':'btn-secondary'"
                            >
                            	7 @lang('días')
                            </button>
                            
                            <button type="button"
                            	@click.prevent="selectValidity(document, 15, TimeUnit.Days)"
                            	class="btn m-1"
                            	:class="document.validity == 15 ? 'btn-success':'btn-secondary'"
                            >
                        		15 @lang('días')
                        	</button>
                        	{{-- / Días --}}

                        	{{-- Meses --}}
                        	<button type="button"
                        		@click.prevent="selectValidity(document, 1, TimeUnit.Months)"
                        		class="btn m-1"
                            	:class="document.validity == 1 && document.validity_unit == TimeUnit.Months ? 'btn-success':'btn-warning'"
                        		>1 @lang('mes')</button>
                            <button type="button"
                            	@click.prevent="selectValidity(document, 3, TimeUnit.Months)"
                            	class="btn m-1"
                            	:class="document.validity == 3 ? 'btn-success':'btn-warning'"
                            	>3 @lang('meses')</button>
                            <button type="button"
                            	@click.prevent="selectValidity(document, 6, TimeUnit.Months)"
                            	class="btn m-1"
                            	:class="document.validity == 6 ? 'btn-success':'btn-warning'"
                            	>6 @lang('meses')</button>
                        	{{-- / Meses --}}

                        	{{-- Años --}}
                            <button type="button"
                            	@click.prevent="selectValidity(document, 1, TimeUnit.Years)"
                            	class="btn m-1"
                            	:class="document.validity == 1 && document.validity_unit == TimeUnit.Years ? 'btn-success':'btn-info'"
                            	>1 @lang('año')</button>
                        	{{-- / Años --}}
                            
                        </div>
                        {{-- / Botones con intervalos pre-estalecidos --}}

                    </b-form-group>
                    {{-- /El periodo máximo de validez del documento --}}

                    {{-- El tamaño máximo del documento --}}
                    <b-form-group id="size-request" label="@lang('Tamaño máximo')" label-for="size-request">
                        <select v-model="document.maxsize" class="form-control">
                            <option value="" selected>@lang('Cualquiera')</option>
                            @foreach ($documentSizes as $documentSize)
                                <option value="{{$documentSize}}">
                                    @filesize($documentSize)
                                </option>
                            @endforeach
                        </select>
                    </b-form-group>
                    {{-- /El tamaño máximo del documento --}}

                    {{-- La fecha de caducidad --}}
                    <b-form-group id="expiration--date-doc" label="@lang('Fecha de caducidad')"
                    	label-for="expiration--date-doc">

                    	<b-form-checkbox switch size="lg" v-model="document.has_expiration_date">
                    		<span class="text-warning">
                    			@lang('Solicitar fecha de caducidad a quien provea el documento') ?
                    		</span>
                    	</b-form-checkbox>

                    	<b-form-checkbox v-if="document.has_expiration_date" switch size="lg"
                    		v-model="document.notify"
                    		button-variant="success">
                    		<span class="text-success">
                    			@lang('Recibir notificaciones antes de esta fecha').
                    		</span>
                    	</b-form-checkbox>

                    </b-form-group>	
                    {{-- / La fecha de caducidad --}}

                    <hr>

                    {{-- Botones de envio y reseteo del formulario --}}
                    <b-form-group >

                     	<b-button v-if="editing" type="submit" variant="warning">
                            @lang('Actualizar')
                        </b-button>
                        <b-button v-else="editing" type="submit" variant="primary">
                            @lang('Añadir')
                        </b-button>

                        <b-button class="ml-2" @click="resetForm()">
                            @lang('Restablecer')
                        </b-button>
                    </b-form-group>
                    {{-- /Botones de envio y reseteo del formulario --}}

                </b-form>
            </div>

        </b-container>
        {{-- /Formulario con los campos de un nuevo documento a requerir --}}
    </div>

</div>
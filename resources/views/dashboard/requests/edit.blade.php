{{--
	Vista para crear o editar una solicitud de documentos
--}}
@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title')
@lang('Creando solicitud de documentos')
@stop

{{-- Css Personalizado --}}
@section('css')
    <link rel="stylesheet" href="@mix('assets/css/dashboard/slider.css')" />
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @if ($documentRequest)
        @lang('Editar Solicitud de Documentos')
    @else
        @lang('Crear Solicitud de Documentos')
    @endif
    <div class="page-title-subheading">
        <div>
            @lang('Solicite a los usuarios los documentos que precise')
        </div>
        <div>
            @lang('Por ejemplo, puede solicitar su documento nacional de identidad, su curriculum vitae, etc, ...')
        </div>
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
{{--/El mensaje flash que se muestra cuando la operación ha tenido éxito o error --}}

<div v-cloak id="app" class="col-md-12">

    {{-- Modales --}}
    @include('dashboard.modals.requests.help-for-document-request')
    {{--/Modales --}}

    {{-- Los datos del documento y de los firmantes a los que crearles la solicitud de documentos --}}
    <div id="data"
        data-message-non-selected-doc="@lang('Debe seleccionar el tipo de documento que va a requerir')"
        data-message-request-saved="@lang('Se ha adicionado el documento a la lista de documentos requeridos')"
        data-message-request-uncomplete="@lang('Debe completar los campos requeridos para guardar esta Solicitud')"
        data-texts="@json($validityTexts)"
    ></div>
    {{-- /Los datos del documento y de los firmantes a los que crearles la solicitud de documentos --}}
     
    <div class="row">

        {{-- Los botones de Acción --}}
        @include('dashboard.requests.edit-action-buttons')
        {{-- /Los botones de Acción --}}
        
        {{-- Datos de la nueva solicitud --}}
        <div class="col-md-12 col-lg-3">
            
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Datos de la nueva solicitud')</h5>

			        <hr>

			        <b-form>

				        {{-- El nombre de la solicitud --}}
	                    <b-form-group id="name-request" label="@lang('Nombre de la solicitud *')" label-for="name-request">
	                        
	                        <b-form-input 
	                                id="name-request-input"
	                                placeholder="@lang('Nombre')"
	                                v-model="$v.request.name.$model"
	                                :state="validateRequestState('name')"
	                                size="lg"
	                                aria-describedby="input-1-live-feedback"
	                        ></b-form-input>

	                        <b-form-invalid-feedback id="input-1-live-feedback">
	                        	@lang('Debe introducir el nombre de la solicitud')
	                        </b-form-invalid-feedback>

	                    </b-form-group>
				        {{-- El nombre de la solicitud --}}

				        {{-- El comentario de la solicitud --}}
	                    <b-form-group id="comment-request" label="@lang('Comentario de la solicitud *')" label-for="comment-request">
	                        
	                        <b-form-textarea 
	                                id="comment-request-input"
	                                placeholder="@lang('Comentario')"
	                                v-model="$v.request.comment.$model"
	                                :state="validateRequestState('comment')"
	                                size="lg"
	                                rows="6"
	                                aria-describedby="input-2-live-feedback"
	                        ></b-form-textarea>

	                        <b-form-invalid-feedback id="input-2-live-feedback">
	                        	@lang('Debe introducir un breve comentario para la solicitud')
	                        </b-form-invalid-feedback>

	                    </b-form-group>
				        {{-- El comentario de la solicitud --}}

			        </b-form>


                </div>
            </div>  

        </div>
        {{-- Datos de la nueva solicitud --}}

        {{-- Documento que se va a requerir --}}
        <div class="col-md-12 col-lg-9">
 			
 			{{-- Datos del Documento --}}
		    <div class="col-md-12">
				@include('dashboard.requests.contribute-request')
		    </div>
		    {{-- /Datos del Documento --}}
        
        </div>
        {{-- /Documento que se va a requerir --}}

        {{-- Listado de documentos requeridos --}}
	    @include('dashboard.requests.requests-list')
	    {{-- /Listado de documentos requeridos --}}

        {{-- Los botones de Acción --}}
        @include('dashboard.requests.edit-action-buttons')
        {{-- /Los botones de Acción --}}

    </div>

</div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')

{{--filesize plugin--}}
<script src="@asset('assets/js/libs/filesize.min.js')"></script>

{{-- Vuelidate plugin for vue --}}
<script src="@asset('assets/js/libs/vuelidate.min.js')"></script>
{{-- The builtin validators is added by adding the following line. --}}
<script src="@asset('assets/js/libs/validators.min.js')"></script>

<!-- Load Vue followed by BootstrapVue -->
<script src="@asset('assets/js/vue/bootstrap-vue.js')"></script>

{{-- Moment js --}}
<script src="@asset('assets/js/libs/moment-with-locales.min.js')"></script>

<script src="@mix('assets/js/dashboard/requests/edit.js')"></script>
@stop
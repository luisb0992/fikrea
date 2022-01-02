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
    @lang('Seleccionar los usuarios')
    <div class="page-title-subheading">
    		@lang('Puede generar una URL mediante la cual se podrán aportar los documentos que usted ha solicitado'),
       		@lang('o simplemente elija las personas a las que solicita la documentación').
       	<p>
       		@lang('Puede añadirlas desde la lista de contactos o añadir un nuevo contacto para ello').
       	</p>
    </div>
</div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')

<div v-cloak id="app" class="row no-margin col-md-12">
    {{-- Modales necesarias --}}
    @include('dashboard.modals.documents.link-sidebar-modal')
    @include('dashboard.modals.documents.cancel-signer-request-modal')
    {{-- /Modales necesarias --}}

    {{-- Data que se le pasa a Vue--}}
    @include('dashboard.config.request.data')
    {{-- /Data que se le pasa a Vue --}}

    {{-- Los botones con las acciones --}}
    @include('dashboard.config.request.action-buttons')
    {{-- /Los botones con las acciones --}}

    {{-- El número máximo de firmantes que pueden ser seleccionados con el plan actual --}}
    <input type="hidden" id="max-signers" value="{{$user->subscription->plan->signers}}" />
    {{--/El número máximo de firmantes que pueden ser seleccionados con el plan actual --}}

    	{{-- Check para generar URL o seleccionar firmante --}}
    	<div class="row container">
    		<div class="col-sm-12 ml-3 mb-4">
		        <div class="main-card card">
		            <div class="card-body">
		                <b-form-group id="signer-from">
			             
				            <b-form-checkbox switch size="lg"
					    		v-model="shareUrl"
					    		button-variant="primary">
					    		<span class="">
					    			@lang('Generar URL').
					    		</span>
					    	</b-form-checkbox>

					    	<b-form-text class="ml-5">
				            	@lang('Se generará una URL única que podrá compartir con la persona que debe aportar la documentación').
				            </b-form-text>

				        </b-form-group>
		            </div>
		        </div>
		    </div>
    	</div>
    	{{-- / Check para generar URL o seleccionar firmante --}}

	    {{-- Generar URL para solicitar la documentación --}}
	    <div v-show="shareUrl" class="row container">
	    	@include('dashboard.config.request.generate-url')
	    </div>
	    {{-- / Generar URL para solicitar la documentación --}}

	    {{-- Selección de firmantes --}}
	    <div v-show="!shareUrl" class="row">
	    	{{-- Añadir Firmante desde Mis Contactos --}}
		    <div class="col-md-6 col-s-12">
		        <div class="main-card mb-3 card">
		            <div class="card-body">
		                <h5 class="card-title">@lang('Seleccionar Contacto')</h5>
		                <div class="mb-2">@lang('Seleccione un contacto y se añadirá a la lista')</div>
		                @include('dashboard.partials.contacts-table')
		            </div>
		        </div>
		    </div>
		    {{--/ Añadir Firmante desde Mis Contactos --}}
		    
		    {{-- Crear un Nuevo Firmante --}}
		    @include('dashboard.config.request.new-signer')
		    {{--/ Crear un Nuevo Firmante --}}

		    {{-- Lista de Firmantes del Documento --}}
		    <div class="col-md-12">
		       @include('dashboard.partials.signers-table')
		    </div>
		    {{--/Lista de Firmantes del Documento --}}
	    </div>
	    {{-- / Selección de firmantes --}}

    {{-- Los botones con las acciones --}}
    @include('dashboard.config.request.action-buttons')
    {{-- /Los botones con las acciones --}}

</div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')

{{-- Load Vue followed by BootstrapVue --}}
<script src="@asset('assets/js/vue/bootstrap-vue.js')"></script>

<script src="/assets/js/dashboard/requests/signers.js"></script>
@stop
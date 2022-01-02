{{--
    Configura y prepara el documento para ser firmado por los firmantes
    
    Se señalan en las páginas del documento, los lugares donde seben realizarse las firmas
    por cada uno de los firmantes

    Cada firmante podrá firmar el documento a través de una página con un token de acceso exclusivo

    @author javieru <javi@gestoy.com>
    @author rosellpp <rpupopolanco@gmail.com>
    @copyright 2021 Retail Servicios Externos
--}}

@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@section('css')
<link rel="stylesheet" href="@mix('assets/css/workspace/document.css')" />
<link rel="stylesheet" href="@mix('assets/css/dashboard/document/config.css')" />
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @lang('Prepare su documento para ser firmado')
    <div class="page-title-subheading">
        @lang('Seleccione cada página que debe se firmada e indique la posición que debe ocupar la firma')
    </div>
</div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')

<div v-cloak id="app" class="col-md-12">

    {{-- Modales Necesarias --}}
        {{-- Modal de selección del firmante del documento --}}
        @include('dashboard.modals.signature.select-signer')

        {{-- Modal de selección de un sello para ser estampado en el documento --}}
        @include('dashboard.modals.signature.select-stamp')

        {{-- Modal de ayuda para la configuración de la firma del documento --}}
        @include('dashboard.modals.signature.help-config-document')

        {{-- Modal que se muestra cuado el número de firmantes del documento no coincide --}}
        @include('dashboard.modals.signature.number-of-signers-not-mismatch')

        {{-- Modal de proceso de configuración de firma finalizado con éxito --}}
        @include('dashboard.modals.signature.document-sign-config-success')
    {{--/ Modales Necesarias --}}

    {{-- Data y mensajes --}}
    @include('dashboard.documents.config.data')
    {{--/Data y mensajes --}}
    
    {{-- Los botones de acción --}}
    @include('dashboard.documents.config.action-buttons')
    {{--/Los botones de acción --}}

    {{-- Miniaturas de cada página del documento --}}
    @include('dashboard.documents.config.preview')
    {{--/ Miniaturas de cada página del documento --}}

    {{-- Barra de Herramientas --}}
    @include('dashboard.documents.config.toolbar')
    {{-- /Barra de Herramientas --}}

    {{-- Spinner cuando se esta cargando el pdf--}}
    <div v-show="false" class="text-center mt-5 mb-5">
        <b-spinner class="spinner-loading-pdf"
            label="@lang('Cargando documento Pdf')"
            data-toggle="tooltip"
            data-placement="bottom"
            data-original-title="@lang('Cargando documento Pdf')"
        ></b-spinner>
    </div>
    {{-- /Spinner cuando se esta cargando el pdf--}}

    {{-- @include('dashboard.documents.config.tracert-canvas') --}}
    {{-- Muestra el visor del documento --}}
    <div class="document"
        ref="document">
    	{{-- Muestra el documento PDF --}}
	    <canvas 
            {{-- @mousemove="mouseMoveOverCanvasEvent" --}}
            id="pdf" v-show="true" data-id="{{$document->id}}" 
	        data-pdf="@route('dashboard.document.pdf', ['token' => $document->id])"
	        data-request="@route('dashboard.document.config.save', ['id' => $document->id])"
	        data-signs="@route('dashboard.document.get.signs', ['token' => $document->id])"
	        {{--
	            Si se ha seleccionado verificación de datos (formulario) se debe
	            configurar al terminar sino ...
	            Si se ha seleccionado validacion por solicitud de documentos
	            la misma de debe configurar al terminar este proceso
	         --}}
	        @if($document->mustBeValidateByFormData())
	        data-redirect-to="@route('dashboard.document.formdata', ['id' => $document->id])"
	        @elseif(!$document->isRequestValidationConfigured())
	        data-redirect-to="@route('dashboard.document.request.validations', ['id' => $document->id])"
	        @else
	        data-redirect-to="@route('dashboard.document.list')"
	        @endif

	        @mousedown="select" class="cursor-for-sign">
	    </canvas>
	    {{--/Muestra el documento PDF --}}  

        {{-- El contenedor de marcadores de firmas --}}
        <div id="signs"></div>
        {{-- /El contenedor de marcadores de firmas --}}
    
    </div>
    {{--/ Muestra el visor del documento --}}

    {{-- Firmas que debe firmar el creador --}}
    @include('dashboard.documents.config.creator-signs')
    {{--/Firmas que debe firmar el creador --}}

    {{-- Barra de Herramientas --}}
    @include('dashboard.documents.config.toolbar')
    {{-- /Barra de Herramientas --}}

    {{-- Los botones de acción --}}
    @include('dashboard.documents.config.action-buttons')
    {{--/Los botones de acción --}}

    {{-- Templates para marcador de firma, de sello, y firma por defecto --}}
    @include('dashboard.documents.config.templates')
    {{--/Templates para marcador de firma, de sello, y firma por defecto --}}

</div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')

<!-- Load Vue followed by BootstrapVue -->
<script src="@asset('assets/js/vue/bootstrap-vue.js')"></script>

{{--
    Pdf.js
    @link https://mozilla.github.io/pdf.js/
--}}
<script src="@asset('assets/js/libs/pdf.min.js')"></script>
<script src="@asset('assets/js/libs/pdf.worker.min.js')"></script>

{{--
    Signature Pad 
    @link https://github.com/szimek/signature_pad
--}}
<script src="@asset('assets/js/dashboard/vendor/signature_pad.umd.min.js')"></script>

{{-- Moment.js --}}
<script src="@asset('assets/js/libs/moment-with-locales.min.js')"></script>

{{-- Script de la página --}}
<script src="@mix('assets/js/dashboard/documents/config.js')"></script>
@stop
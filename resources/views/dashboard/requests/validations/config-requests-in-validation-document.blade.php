{{--
    Vista para seleccionar para cada firmante que debe validarse con una "Solicitud de Documentos"
    los documentos que debe aportar

    @author    rosellpp <rpupopolanco@gmail.com>
    @copyright 2021 Retail Servicios Externos   
--}}

@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@section('css')
<link rel="stylesheet" href="@mix('assets/css/dashboard/slider.css')" />
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @lang('Solicite los documentos que desee a los firmantes')
    <div class="page-title-subheading">
        @lang('Para cada uno de los firmantes que debe validarse mediante una "Solicitud de Documentos" seleccione los documentos' )
        @lang('que este debe proveer en el proceso de validación')
    </div>
</div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')

<div v-cloak id="app" class="col-md-12">

    {{-- Los datos del documento y de los firmantes a los que crearles la solicitud de documentos --}}
    <div id="data"
        data-document="@json($document)"
        data-signers="@json($signers)"
        data-message-non-selected-signer="@lang('Debe seleccionar al menos un firmante')"
        data-message-non-selected-doc="@lang('Debe seleccionar el tipo de documento a solicitar')"
        data-message-non-request-for-signer="@lang('Debe tener como mínimo una solicitud por cada firmante')"
        data-message-request-saved="@lang('Se ha creado su solicitud de documento para el firmante seleccionado')"
        data-texts="@json($validityTexts)"
    ></div>
    {{-- /Los datos del documento y de los firmantes a los que crearles la solicitud de documentos --}}

    <div class="row">

        {{-- Los botones de Acción --}}
        @include('dashboard.partials.action-buttons-validations-requests')
        {{-- /Los botones de Acción --}}
        
        {{-- Listado de firmantes a seleccionar para requerir documentos --}}
        @include('dashboard.requests.validations.signers-list')
        {{-- Listado de firmantes a seleccionar para requerir documentos --}}

        {{-- Documento que se va a requerir al o a los firmantes seleccionados --}}
        <div class="col-sm-12 col-md-9">
            @include('dashboard.requests.validations.contribute-request')
        </div>
        {{-- /Documento que se va a requerir al o a los firmantes seleccionados --}}

        {{-- Listado de documentos que se han requerido --}}
        @include('dashboard.requests.validations.signer-requests-list')
        {{-- / Listado de documentos que se han requerido --}}

        {{-- Los botones de Acción --}}
        @include('dashboard.partials.action-buttons-validations-requests')
        {{-- /Los botones de Acción --}}

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

{{-- Script de la página --}}
<script src="@mix('assets/js/dashboard/documents/config-signers-request.js')"></script>

@stop
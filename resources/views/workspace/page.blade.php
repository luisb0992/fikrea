@extends('workspace.layouts.main')

{{-- Título de la Página --}}
@section('title', 'WorkSpace')

{{-- Css Personalizado --}}
@section('css')
<link rel="stylesheet" href="@mix('assets/css/workspace/page.css')" />
<link rel="stylesheet" href="@mix('assets/css/dashboard/document/config.css')" />
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    <p>
        @lang('Debe firmar las páginas indicadas')
    </p>
    <div class="page-title-subheading">
        <p>
            @lang('Firme en cada región del documento identificada').
            @lang('Si se equivoca puede pulsar la papelera <i class="fa fa-trash"></i> para borrar la zona de firma').
        </p>
        @if ($signer->document->mustBeValidateByScreenCapture())
        <p>
            @lang('Debe iniciar la compartición de su pantalla para poder realizar las firmas solicitadas').
        </p>
        @endif
    </div>
</div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')

<div v-cloak id="app" class="col-md-12 mb-3">

    {{-- Modales --}}

    {{-- 
        Modal de documento no completado
        Se muestra cuando no se han realizado todas las firmas requeridas    
    --}}
    @include('workspace.modals.document-not-completed')

    {{--
        Modal de documento firmando con éxito
        Se muestra cuando el proceso de firma ha concluido satisfactoriamente
    --}}
    @include('workspace.modals.document-signed')

    {{--/Modales --}}

    {{-- El registro de la visita del usuario --}}
    <div id="visit" data-visit="@json($visit)"></div>
    {{--/El registro de la visita del usuario --}}

    {{-- Los mensajes de la aplicación --}}
    <div id="messages" 
        data-capture-record-success="@lang('La captura de pantalla ha finalizado con éxito')"
        data-recording="@lang('Detenga la grabación antes de finalizar')"
        data-cannot-finish="@lang('Usted no puede finalizar, aún no ha completado todas sus firmas')"
        data-must-init-screen-capture-recording="@lang('Debe comenzar la grabación de su pantalla antes de poder firmar')"
    ></div>
    {{--/Los mensajes de la aplicación --}}
    
    {{-- Data a Vue si estoy en desktop o no--}}
    <div id="data"
        @desktop
        data-desktop="@json(true)"
        @else
        data-desktop="@json(false)"
        @enddesktop
    ></div>
    {{-- /Data a Vue si estoy en desktop o no--}}

    {{-- Control de la captura de pantalla --}}
    @include('workspace.partials.page.capture-controls')
    {{--/Control de la captura de pantalla --}}

    {{-- Los botones de acción --}}
    @include('workspace.partials.page.action-buttons')
    {{--/Los botones de acción --}}

    {{-- Barra de Herramientas --}}
    @include('workspace.partials.page.toolbar')
    {{-- Barra de Herramientas --}}

    {{-- Lista de Páginas a Firmar --}}
    @include('workspace.partials.page.pages-list')
    {{--/Lista de Páginas a Firmar --}}

    {{-- La página del documento a firmar --}}
    @include('workspace.partials.page.sign-page')
    {{--/La página del documento a firmar --}}

    {{-- Barra de Herramientas --}}
    @include('workspace.partials.page.toolbar')
    {{-- Barra de Herramientas --}}

    {{-- El marcador donde se debe situar una firma en el documento --}}
    @include('workspace.partials.page.sign-marker')
    {{--/ El marcador donde se debe situar una firma en el documento --}}

    {{-- Los botones de acción --}}
    @include('workspace.partials.page.action-buttons')
    {{--/Los botones de acción --}}

@stop

{{-- Los scripts personalizados --}}
@section('scripts')

{{--
    Signature Pad 
    @link https://github.com/szimek/signature_pad
--}}
<script src="@asset('assets/js/dashboard/vendor/signature_pad.umd.min.js')"></script>

{{--
    Pdf.js
    @link https://mozilla.github.io/pdf.js/
--}}
<script src="@asset('assets/js/libs/pdf.min.js')"></script>
<script src="@asset('assets/js/libs/pdf.worker.min.js')"></script>

{{-- Moment.js --}}
<script src="@asset('assets/js/libs/moment-with-locales.min.js')"></script>

{{-- Script de la página --}}
<script src="@mix('assets/js/workspace/page.js')"></script>

@stop
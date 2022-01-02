@extends('workspace.layouts.main')

{{-- Título de la Página --}}
@section('title', 'WorkSpace')

{{-- Css Personalizado --}}
@section('css')
<link rel="stylesheet" href="@mix('assets/css/workspace/page.css')" />
<link rel="stylesheet" href="@mix('assets/css/workspace/document.css')" />
<link rel="stylesheet" href="@mix('assets/css/dashboard/document/config.css')" />
<link rel="stylesheet" href="@mix('assets/css/dashboard/document/config-textboxs.css')" />
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @lang('Se destacan las páginas del documento donde debe completar las cajas de textos')
    <div class="page-title-subheading">
        <p>
            @lang('Acceda a cada una de ellas y complete la información solicitada').
        </p>
        @if ($signer->document->mustBeValidateByScreenCapture())
        <p>
            @lang('Debe iniciar la compartición de su pantalla para poder completar los textos solicitados').
        </p>
        @endif
    </div>
</div>
@stop

{{-- 
    Aquí incluímos el contenido de la página
--}}
@section('content')

<div v-cloak id="app" class="col-md-12">

    {{-- Templates para cajas de textos --}}
    @include('workspace.textboxs.templates')
    {{-- Templates para cajas de textos --}}

    {{-- Modales --}}
    @include('workspace.textboxs.modals')
    {{-- / Modales --}}

    {{-- Data y mensajes --}}
    @include('workspace.textboxs.data')
    {{--/Data y mensajes --}}

    {{-- Control de la captura de pantalla --}}
    @include('workspace.partials.page.capture-controls')
    {{--/Control de la captura de pantalla --}}

    {{-- Los botones de acción --}}
    @include('workspace.textboxs.action-buttons')
    {{--/Los botones de acción --}}

    {{-- Miniaturas de cada página del documento --}}
    @include('dashboard.documents.config-texts.preview')
    {{--/ Miniaturas de cada página del documento --}}

    {{-- Barra de Herramientas --}}
    @include('dashboard.documents.config-texts.toolbar')
    {{-- /Barra de Herramientas --}}

    {{-- Muestra el visor del documento --}}
    @include('workspace.textboxs.document-visor')
    {{--/ Muestra el visor del documento --}}

    {{-- Cajas que debe completar el firmante --}}
    @include('workspace.textboxs.signer-texts')
    {{--/Cajas que debe completar el firmante --}}

    {{-- Barra de Herramientas --}}
    @include('dashboard.documents.config-texts.toolbar')
    {{-- /Barra de Herramientas --}}

    {{-- Los botones de acción --}}
    @include('workspace.textboxs.action-buttons')
    {{--/Los botones de acción --}}


</div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
{{--
    Pdf.js
    @link https://mozilla.github.io/pdf.js/
--}}
<script src="@asset('assets/js/libs/pdf.min.js')"></script>

{{-- Moment.js --}}
<script src="@asset('assets/js/libs/moment-with-locales.min.js')"></script>


{{-- Script de la página --}}
{{--
    <script src="@mix('assets/js/dashboard/documents/config-texts.js')"></script>
--}}
<script src="@mix('assets/js/workspace/textboxs.js')"></script>

@stop
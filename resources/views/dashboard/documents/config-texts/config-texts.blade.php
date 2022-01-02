{{--
    Configura y prepara el documento para ser cumplimentado por los firmantes
    
    Se señalan en las páginas del documento, las cajas de texos que deben
    cumplimentar cada uno de los firmantes

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
<link rel="stylesheet" href="@mix('assets/css/dashboard/document/config-textboxs.css')" />
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @lang('Prepare su documento con cajas de texto')
    <div class="page-title-subheading">
        <p>
            @lang('Posicione las cajas de textos que debe cumplimentar el firmante, y los requerimientos de estas según sus necesidades').
            <br>
            @lang('Puede arrastrar las cajas sobre el documento o dar click en la posición deseada para agregar una caja de texto.')
        </p>
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

        {{-- Modal de selección de caja de texto --}}
        @include('dashboard.modals.signature.select-box-type')

        {{-- Modal de ayuda para la configuración de la firma del documento --}}
        @include('dashboard.modals.signature.help-textboxs-document')

        {{-- Modal que se muestra cuado el número de firmantes del documento no coincide --}}
        @include('dashboard.modals.signature.number-of-signers-not-mismatch')

        {{-- Modal de proceso de configuración de firma finalizado con éxito --}}
        @include('dashboard.modals.signature.document-sign-config-success')
    {{--/ Modales Necesarias --}}

    {{-- Data y mensajes --}}
    @include('dashboard.documents.config-texts.data')
    {{--/Data y mensajes --}}
    
    {{-- Los botones de acción --}}
    @include('dashboard.documents.config-texts.action-buttons')
    {{--/Los botones de acción --}}

    {{-- Miniaturas de cada página del documento --}}
    @include('dashboard.documents.config-texts.preview')
    {{--/ Miniaturas de cada página del documento --}}

    {{-- Barra de Herramientas --}}
    @include('dashboard.documents.config-texts.toolbar')
    {{-- /Barra de Herramientas --}}
    
    <div class="row mt-1">

        <div class="col-sm-12 col-md-9 visor">

            <div class="row">
                {{-- Muestra el visor del documento --}}
                @include('dashboard.documents.config-texts.document-visor')
                {{--/ Muestra el visor del documento --}}
            </div>
            <div class="row">
                {{-- Cajas que debe completar el creador --}}
                @include('dashboard.documents.config-texts.creator-texts')
                {{--/Cajas que debe completar el creador --}}

                {{-- Barra de Herramientas --}}
                @include('dashboard.documents.config-texts.toolbar')
                {{-- /Barra de Herramientas --}}

            </div>

        </div>

        <div class="col-sm-12 col-md-3 p-2">

            {{-- Muestra los tipos de cajas de texto y panel con opciones de caja seleccionada --}}
            @include('dashboard.documents.config-texts.textbox-options')
            {{-- /Muestra los tipos de cajas de texto y panel con opciones de caja seleccionada --}}

        </div>
        
    </div>

    {{-- Los botones de acción --}}
    @include('dashboard.documents.config-texts.action-buttons')
    {{--/Los botones de acción --}}

    {{-- Templates para cajas de textos --}}
    @include('dashboard.documents.config-texts.templates')
    {{-- Templates para cajas de textos --}}

</div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')

<!-- Load Vue followed by BootstrapVue -->
<script src="@asset('assets/js/vue/bootstrap-vue.js')"></script>

<script src="@asset('assets/js/vue/v-drag.js')"></script>

{{--
    Pdf.js
    @link https://mozilla.github.io/pdf.js/
--}}
<script src="@asset('assets/js/libs/pdf.min.js')"></script>
<script src="@asset('assets/js/libs/pdf.worker.min.js')"></script>

{{-- Moment.js --}}
<script src="@asset('assets/js/libs/moment-with-locales.min.js')"></script>

{{-- Script de la página --}}
<script src="@mix('assets/js/dashboard/documents/config-texts.js')"></script>
@stop
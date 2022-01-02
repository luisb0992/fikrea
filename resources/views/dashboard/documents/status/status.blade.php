@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@section('css')
<link rel="stylesheet" href="@mix('assets/css/dashboard/document/status.css')" />
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @lang('Puede ver el estado de validación del documento')
    <div class="page-title-subheading">
        @lang('También puede aprovechar para enviar un recordatorio a los usuarios que no han atendido su petición (una vez al día)')
    </div>
</div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')

<div  id="app" class="col-md-12">
    
    {{-- Modales Necesarias --}}

    {{-- Modal de envío de solicitud de firma con éxito --}}
    @include('dashboard.modals.status.document-sended-success')

    {{-- Modal de confirmación de elimnación del dopcumento --}}
    @include('dashboard.modals.documents.remove-document-confirmation')

    {{-- / Modales Necesarias --}}

    {{-- Certificado del proceso de validación --}}
    <x-certificate-button
            :route="@route('dashboard.document.certificate', ['id' => $document->id])"></x-certificate-button>
    {{--/Certificado del proceso de validación --}}

    {{-- Información del documento --}}
    @include('dashboard.documents.status.document-info')
    {{--/Información del documento --}}

    {{-- Listado de Firmantes --}}
    @include('dashboard.documents.status.signers-list')
    {{--/Listado de Firmantes --}}

    {{--  Estado de cada Validación   --}} {{-- get_browser() --}}
    @include('dashboard.documents.status.validations-status')
    {{--/ Estado de cada Validación --}}

    {{-- Listado de documentos resultantes de la validación --}}
    @include('dashboard.documents.status.resulting-documents')
    {{--/ Listado de documentos resultantes de la validación --}}

    {{-- Listado de los envíos realizados --}}
    @include('dashboard.documents.status.sending-list')
    {{--/Listado de los envíos realizados --}}

    {{-- Certificado del proceso de validación --}}
    <x-certificate-button
            :route="@route('dashboard.document.certificate', ['id' => $document->id])"></x-certificate-button>
    {{--/Certificado del proceso de validación --}}

</div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
<script src="@mix('assets/js/dashboard/documents/status.js')"></script>
@stop
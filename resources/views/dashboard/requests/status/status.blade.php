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
        @lang('Se muestra el estado de esta solicitud')
        <div class="page-title-subheading">
            @lang('Puede descargar los archivos que han proporcionado los usuarios')
        </div>
    </div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')

    <div id="app" class="col-md-12">

        {{-- Modales Necesarias --}}

        {{-- Modal de envío de solicitud de firma con éxito --}}
        @include('dashboard.modals.status.document-sended-success')
        {{-- Modales Necesarias --}}

        {{-- Botones de acción --}}
        <x-certificate-button
                :route="@route('dashboard.document.request.certificate', ['id' => $request->id])"></x-certificate-button>
        {{-- Botones de acción --}}

        {{-- Estado de la solicitud --}}
        @include('dashboard.requests.status.request-progress')
        {{--/Estado de la solicitud --}}

        {{-- Información de la solicitud de documentos --}}
        @include('dashboard.requests.status.request-info')
        {{--/Información de la solicitud de documentos --}}

        {{-- Lista de documentos proporcionados --}}
        @include('dashboard.requests.status.request-contributted-files')
        {{-- Lista de documentos proporcionados --}}

        {{-- Listado de los envíos realizados --}}
        @include('dashboard.requests.status.request-sends')
        {{--/Listado de los envíos realizados --}}

        {{-- Botones de acción --}}
        <x-certificate-button
                :route="@route('dashboard.document.request.certificate', ['id' => $request->id])"></x-certificate-button>
        {{-- Botones de acción --}}

    </div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
    <script src="@mix('assets/js/dashboard/requests/status.js')"></script>
@stop
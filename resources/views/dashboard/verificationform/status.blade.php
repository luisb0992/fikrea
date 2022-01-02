@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@section('css')
@stop

{{-- El encabezado con la ayuda para la página --}}
@section('help')
    <div>
        @lang('Se muestra el estado de esta verificación de datos')
        <div class="page-title-subheading">
            @lang('Puede ver todos los cambios que ha proporcionado el usuario asignado')
        </div>
    </div>
@stop

{{-- El contenido de la página --}}
@section('content')

    <div id="app" class="col-md-12">

        {{-- Modal de envío de verificación de datos con éxito --}}
        @include('dashboard.verificationform.partials.status.modal-sended-success')
        {{-- /Modal de envío de verificación de datos con éxito --}}

        {{--  includes requeridos para el proceso
            - Botones de accion
            - formulario
            - Envios realizados
            -----------------------------------------}}

        {{-- Botones de acción --}}
        <x-certificate-button
            :route="@route('dashboard.verificationform.certificate', ['id' => $verificationForm->id])">
        </x-certificate-button>
        {{-- / Botones de acción --}}

        {{-- progreso de la verificaciónd e datos --}}
        @include('dashboard.verificationform.partials.status.progress-verification')
        {{-- /progreso de la verificaciónd e datos --}}

        {{-- informacion del formulario de datos --}}
        @include('dashboard.verificationform.partials.status.verification-info')
        {{-- /informacion del formulario de datos --}}

        {{-- Listado de los envíos realizados --}}
        @include('dashboard.verificationform.partials.status.verification-sends')
        {{-- /Listado de los envíos realizados --}}

        {{-- Botones de acción --}}
        <x-certificate-button
            :route="@route('dashboard.verificationform.certificate', ['id' => $verificationForm->id])">
        </x-certificate-button>
        {{-- / Botones de acción --}}

    </div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
    <script src="@mix('assets/js/dashboard/verificationform/status.js')"></script>
@stop

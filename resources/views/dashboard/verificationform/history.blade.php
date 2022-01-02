@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

    {{-- El encabezado con la ayuda para la página --}}
@section('help')
    <div>
        @lang('Se muestra la lista de visitas realizadas para satisfacer la verificación de datos')
        <div class="page-title-subheading">
            @lang('Aquí se listan las veces que las personas han entrado a su área de trabajo para atender a la
            verificación')
        </div>
    </div>
@stop

{{-- El contenido de la página --}}
@section('content')
    <div id="app" class="col-md-12">

        {{-- Información requerida --}}
        @include('dashboard.verificationform.partials.status.progress-verification')
        {{-- /Información requerida --}}

        {{-- informacion del formulario de datos --}}
        @include('dashboard.verificationform.partials.status.verification-info')
        {{-- /informacion del formulario de datos --}}

        {{-- Tabla con histórico de las visitas --}}
        @include('dashboard.verificationform.partials.history.visits-history')
        {{-- /Tabla con histórico de las visitas --}}

    </div>
@stop

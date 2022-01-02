@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

    {{-- Css Personalizado --}}
@section('css')
    <link rel="stylesheet" href="@mix('assets/css/dashboard/select-validations.css')" />
@stop

{{-- El encabezado con la ayuda para la página --}}
@section('help')
    <div>
        @lang('Seleccione los procesos de validación del documento para cada firmante')
        <div class="page-title-subheading">
            @lang('A un firmante se le puede requerir su firma manuscrita, que proporcione un archivo de audio, video')...
        </div>
    </div>
@stop

{{-- El contenido de la página --}}
@section('content')
    <div id="app" v-cloak class="row no-margin col-md-12">

        {{-- Modales necesarias --}}
        @include('dashboard.modals.documents.link-sidebar-modal')
        {{-- /Modales necesarias --}}

        {{-- Los botones de Acción --}}
        @include('dashboard.partials.action-buttons-select-validations')
        {{-- /Los botones de Acción --}}

        {{-- Se muestra una tabla de validaciones para cada uno de los firmantes del documento
         lo que incluye al propio autor del documento --}}
        @include('dashboard.partials.user-validation', [
            'document'          => $document,
            'validations'       => $validations,
            'notAvailableMsj'   => $notAvailableMsj,
            'notAvailableIcon'  => $notAvailableIcon
        ])

        {{-- Los botones de Acción --}}
        @include('dashboard.partials.action-buttons-select-validations')
        {{-- /Los botones de Acción --}}

    </div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
    <script src="/assets/js/dashboard/documents/validations.js"></script>
@stop

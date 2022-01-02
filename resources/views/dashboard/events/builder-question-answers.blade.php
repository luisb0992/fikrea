@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

    {{-- Css Personalizado --}}
@section('css')
@stop

{{-- El encabezado con la ayuda --}}
@section('help')
    <div>
        @lang('Crear la preguntas y respuestas')
        <div class="page-title-subheading">
            @lang('Puede crear las preguntas y respuestas desde 0 para el evento, o seleccionar algunas de las plantillas
            guardadas anteriormente').
        </div>
    </div>
@stop

{{-- El contenido de la página --}}
@section('content')

    <div v-cloak id="app" class="row no-margin col-md-12">

        {{-- Modales necesarias --}}
        @include('dashboard.modals.documents.link-sidebar-modal')
        @include('dashboard.events.partials.census.modal-cancel-census', [
        'route' => route('dashboard.event.list')
        ])
        {{-- /Modales necesarias --}}

        {{-- Los botones con las acciones --}}
        @include('dashboard.events.partials.census.action-buttons')
        {{-- /Los botones con las acciones --}}


    </div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
    <script src="@mix('assets/js/dashboard/events/census.js')"></script>
@stop

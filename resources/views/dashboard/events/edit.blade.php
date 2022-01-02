{{-- Vista para crear o editar una solicitud de documentos --}}
@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title')
    @lang('Eventos')
@stop

@section('css')
@stop

{{-- El encabezado con la ayuda para la página --}}
@section('help')
    <div>
        @if ($event)
            @lang('Editar un Evento')
        @else
            @lang('Crear un Evento')
        @endif
        <div class="page-title-subheading">
            <div>
                @lang('En esta herramienta encontrará una sección que tiene validez legal para la recogida de firmas,
                encuestas o
                votaciones de manera digital, fácil y rápida')
            </div>
        </div>
    </div>
@stop

{{-- El contenido de la página --}}
@section('content')

    <div id="app" class="row no-margin col-md-12">

        {{-- Mensajes de la aplicación --}}
        <div id="messages"
            data-invalid-file="@lang('El archivo seleccionado es inválido')"
            data-anonymous-empty-answers="@lang('Debe seleccionar una opción para las respuestas anónimas')"
            data-event-success="@lang('El evento se ha guardado con éxito')"
            data-server-error="@lang('Ha ocurrido un error, intente más tarde')"
        >
        </div>
        {{-- /Mensajes de la aplicación --}}

        {{-- Tipos de evento --}}
        <div id="eventTypes" data-vote="{{ \App\Enums\Event\EventType::VOTE }}"
            data-survey="{{ \App\Enums\Event\EventType::SURVEY }}"
            data-signature="{{ \App\Enums\Event\EventType::SIGNATURE_COLLECTION }}"
            data-survey-and-signature="{{ \App\Enums\Event\EventType::SURVEY_AND_SIGNATURE_COLLECTION }}">
        </div>
        {{-- /Tipos de evento --}}

        {{-- botones de accion --}}
        @include('dashboard.events.partials.edit.action-buttons', [
        'event' => $event
        ])

        {{-- formulario del evento --}}
        @include('dashboard.events.partials.edit.form-event', [
        'purposes' => $purposes
        ])

        {{-- botones de accion --}}
        @include('dashboard.events.partials.edit.action-buttons', [
        'event' => $event
        ])

    </div>
@stop
{{-- /Cuerpo de la pagina --}}

{{-- Los scripts personalizados --}}
@section('scripts')

    {{-- Moment.js --}}
    <script src="@asset('assets/js/libs/moment-with-locales.min.js')"></script>

    {{-- principal --}}
    <script src="@mix('assets/js/dashboard/events/edit.js')"></script>
@stop

@extends('backend.layouts.main')

{{-- Título de la Página --}}
@section('title')
@lang('Listado de mensajes SMS enviados')
@endsection

{{-- Css Personalizado --}}
@section('css')
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @lang('Revise el listado de las notificaciones enviadas vía SMS')
    <div class="page-title-subheading">
        @lang('Puede ver los mensajes que se han enviado como recordatorio a firmantes sin dirección email definida, y si un número telefónico').
    </div>
</div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')

<div id="app" class="col-md-12 pr-0">

    {{-- Datos sobre el fondo y consumo del api de mensajería --}}
    @include('backend.sms.info')
    {{--/Datos sobre el fondo y consumo del api de mensajería --}}

	{{-- Muestra la lista de sms --}}
    <div class="main-card mb-3 card">
        <div class="card-body">
            <h5>@lang('Lista de SMS')</h5>
            @include('backend.sms.smses-table')
        </div>
    </div>
    {{--/Muestra la lista de sms --}}

</div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
@stop
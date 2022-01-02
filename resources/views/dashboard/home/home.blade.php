@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

    {{-- Css Personalizado --}}
@section('css')
    <link rel="stylesheet" href="@mix('assets/css/dashboard/home.css')" />
@stop

{{-- El encabezado con la ayuda para la página --}}
@section('help')
    <div>@lang('Comencemos...')
        <div class="page-title-subheading">
            @lang('Puede ir a <strong>Gestor de Archivos > Subir Archivo</strong> para subir un nuevo archivo')
            <br>
            @if ($notifications->count())
                @lang('Aquí visualizará sus notificaciones pendientes. Puede eliminarlas o marcarlas como <b>leídas</b>')
            @endif
        </div>
    </div>
@stop

@section('content')
    <div id="app" class="row col-md-12 notifications">

        <input type="hidden" id="requests" data-read-notification="@route('dashboard.notification.read')" />

        {{-- Muestra las notificaciones no leídas --}}
        @forelse($notifications as $notification)
            @include('dashboard.home.notification')
        @empty
            <div class="alert alert-success w-100 ml-4">
                <i class="fas fa-bell fa-2x"></i>
                <span class="fa-2x-text">
                    @lang('No tiene ninguna notificación')
                </span>
            </div>
        @endforelse
        {{-- /Muestra las notificaciones no leídas --}}
    </div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
<script src="@mix('assets/js/dashboard/home.js')"></script>
@stop

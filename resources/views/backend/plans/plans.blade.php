@extends('backend.layouts.main')

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
    @lang('Gestione los planes de suscripción')
    <div class="page-title-subheading">
         @lang('Puede ver, crear, editar y eliminar los planes')
    </div>
</div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')

{{-- Los botones con las acciones --}}
@include('backend.partials.button-action')
{{-- /Los botones con las acciones --}}

<div v-cloak id="app" class="col-md-12 pr-0">
	{{-- Muestra la lista de planes --}}
    <div class="main-card mb-3 card">
        <div class="card-body">
            <h5>@lang('Lista de Planes')</h5> 
            @include('backend.partials.table-plans')
        </div>
    </div>
    {{--/Muestra la lista de usuarios --}}
</div>

{{-- Los botones con las acciones --}}
@include('backend.partials.button-action')
{{-- /Los botones con las acciones --}}

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
<script src="@mix('assets/js/backend/subscription.js')"></script>
@stop
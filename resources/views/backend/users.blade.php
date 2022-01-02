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
    @lang('Lista de usuarios de :app', ['app' => config('app.name')])
    <div class="page-title-subheading">
        @lang('La lista de todos los usuarios de la aplicación incluyendo invitados')
    </div>
</div>
@stop

{{-- 
    Aquí incluímos el contenido de la página
--}}
@section('content')

{{-- El mensaje flash que se muestra cuando la operación ha tenido éxito --}}
<div class="offset-md-3 col-md-6">
    @include('dashboard.sections.body.message-success')
    @include('dashboard.sections.body.message-error')
</div>
{{--/El mensaje flash que se muestra cuando la operacion ha tenido éxito --}}

<div class="col-md-12">
    
    {{-- Muestra la lista de usuarios --}}
    <div class="main-card mb-3 card">
        <div class="card-body">
            <h5>@lang('Lista de Usuarios')</h5> 
            @include('backend.partials.table-users')
        </div>
    </div>
    {{--/Muestra la lista de usuarios --}}

</div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
@stop
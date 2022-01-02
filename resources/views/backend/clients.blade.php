@extends('backend.layouts.main')

{{-- Título de la Página --}}
@section('title')
@lang('Administración → Listado de clientes')
@endsection

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
        @lang('La lista de todos los usuarios con cuentas de pago en :app.', ['app' => config('app.name')])
    </div>
</div>
@stop

{{-- 
    Aquí incluímos el contenido de la página
--}}
@section('content')
<div class="col-md-12">
    
    {{-- El mensaje flash que se muestra cuando la operación ha tenido éxito --}}
    <div class="offset-md-3 col-md-6">
        @include('dashboard.sections.body.message-success')
        @include('dashboard.sections.body.message-error')
    </div>
    {{--/El mensaje flash que se muestra cuando la operacion ha tenido éxito --}}

    {{-- Muestra la lista de usuarios --}}
    <div class="main-card mb-3 card">
        <div class="card-body">
            <h5>@lang('Lista de Clientes')</h5> 
            @include('backend.partials.table-users')
        </div>
    </div>
    {{--/Muestra la lista de usuarios --}}

</div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
@stop
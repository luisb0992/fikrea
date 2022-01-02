@extends('backend.layouts.main')

{{-- Título de la Página --}}
@section('title')
@lang('Administración → Listado de subscripciones')
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
        @lang('La lista de todas las subscripciones de pago en :app.', ['app' => config('app.name')])
    </div>
</div>
@stop

{{-- 
    Aquí incluímos el contenido de la página
--}}
@section('content')
{{-- Los botones con las acciones --}}
    <div class="col-md-12 mb-4">
        <div class="input-group" role="group">
            <a href="@route('backend.subscriptions.subscriptionCreate')" class="btn btn-lg btn-success mr-1">
                @lang('Crear subscripción')
            </a>
        </div>
    </div>
{{-- /Los botones con las acciones --}}

{{-- El mensaje flash que se muestra cuando la operación ha tenido éxito --}}
<div class="offset-md-3 col-md-6">
    @include('dashboard.sections.body.message-success')
</div>
{{--/El mensaje flash que se muestra cuando la oepracion ha tenido éxito --}}

<div class="col-md-12">
    
    {{-- Muestra la lista de usuarios --}}
    <div class="main-card mb-3 card">
        <div class="card-body">
            <h5>@lang('Lista de subscripciones')</h5>

            @include('backend.partials.table-subscriptions')
        </div>
    </div>
    {{--/Muestra la lista de usuarios --}}

</div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
@stop
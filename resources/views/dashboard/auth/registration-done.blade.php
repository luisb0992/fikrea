@extends('dashboard.layouts.no-menu')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@section('css')
@stop

{{-- 
    El contenido de la página
--}}
@section('content')
<div class="col-md-12">
    
    <div class="offset-md-3 col-md-6 col-s-12 main-card mb-3 card">

        <div class="card-body">

            <div class="container">

                <div class="text-success bold text-center">@lang('Su cuenta ha sido registrada')</div>

                <div class="text-center mt-4">
                    @lang('Un correo electrónico ha sido enviado a la dirección de correo proporcionada.')
                    @lang('En el mismo encontrará un botón donde hacer click para proceder a la validación de su cuenta de usuario.')
                </div>
                
                <div class="text-center mt-4">
                    @lang('Pulse en el botón para iniciar sesión')
                </div>

                <div class="text-center mt-4">
                    <a class="btn btn-lg btn-success" href="@route('dashboard.login')">@lang('Iniciar Sesión')</a>
                </div>
            </div>
            
        </div>
    </div>

</div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
@stop

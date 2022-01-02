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
                <div class="text-success bold text-center">
                    @lang('Se ha enviado un correo a la dirección de correo proporcionada que le permitirá crear una contraseña de acceso a su cuenta')
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

    {{-- Logo --}}
    <div class="col-s-12 col-md-9 text-right">
        <a href="@route('landing.home')">
            <img aria-hidden="true" target="_blank" src="@asset('/assets/images/dashboard/logos/fikrea-medium-logo.png')" alt="">
        </a>
    </div>
    {{--/Logo --}}

</div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
@stop

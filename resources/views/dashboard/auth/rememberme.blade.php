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
    
    {{-- Formulario de Recuperación de Contraseña --}}
    <div class="offset-md-3 col-md-6 col-s-12 main-card mb-3 card">
        
        <div class="card-body">

            <div class="container">
                <div class="text-success bold text-center">
                    @lang('Introduzca la dirección de correo que utilizó para registrarse')
                </div>
        
                <div class="text-success bold text-center">
                    @lang('Se enviará un correo a la dirección proporcionada para cambiar la contraseña')
                </div>
        
                <form class="mt-4" action="@route('dashboard.password.request')" method="post">
                    @csrf
        
                    <div class="form-group text-center">
                        @error('email')
                        <input value="" id="email" name="email" type="text" class="form-control col-md-12 is-invalid" placeholder="@lang('dirección de correo')" />               
                        <div class="invalid-feedback" role="alert">{{$message}}</div>               
                        @else
                        <input value="" id="email" name="email" type="text" class="form-control col-md-12" placeholder="@lang('dirección de correo')" />               
                        @enderror
                    </div>
                    
                    <div class="text-center mt-4">
                        <input type="submit" class="btn btn-lg btn-success" value="@lang('Enviar Correo')" />
                    </div>
        
                </form>
        
            </div>
        </div>
    </div>
    {{--/ Formulario de Recuperación de Contraseña --}}

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

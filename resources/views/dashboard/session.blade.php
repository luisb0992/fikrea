@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@section('css')
<link href="@mix('assets/css/dashboard/session.css')" rel="stylesheet" />
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @lang('¿Desea recuperar todos los datos de una sesión anterior?')
    <div class="page-title-subheading">
        @lang('Para ello debe suministrar la dirección de correo que empleó en aquella sesión').
    </div>
</div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')
<div id="app" class="col-md-12">

    {{-- Introducción de la dirección de correo de recuperación del token --}}
    <div class="offset-md-3 col-md-6 col-s-12 main-card mb-3 card">

        <div class="card-body">

            <form action="@route('dashboard.password.request')" method="post">

                @csrf 

                <div class="container">
             
                    <div class="text-secondary bold text-center">
                        <p>@lang('Introduzca la dirección de correo que utilizó')</p>
                    </div>

                    <div class="form-group text-center">
                        @error('email')
                        <input id="email" name="email" class="form-control col-md-12 is-invalid" type="text" value="@old('email')" v-model="email" 
                        maxlength="255" />
                        <div class="invalid-feedback text-left">{{$message}}</div>
                        @else
                        <input id="email" name="email" class="form-control col-md-12" type="text" v-model="email" maxlength="255" 
                            :class="emailIsNotValid ? 'is-invalid' : 'is-valid'" />       
                        @enderror
                    </div>
                    
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-lg btn-success" :disabled="emailIsNotValid">
                        @lang('Enviar Correo')
                    </button>
                </div>

            </form>

        </div>
    </div>
    {{--/Introducción de la dirección de correo de recuperación del token --}}

</div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
<script src="@mix('assets/js/dashboard/session.js')"></script>
@stop
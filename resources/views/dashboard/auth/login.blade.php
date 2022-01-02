@extends('dashboard.layouts.no-menu-no-navbar')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@section('css')
<link rel="stylesheet" href="@mix('assets/css/dashboard/login.css')" />
@stop

{{-- 
    El contenido de la página
--}}
@section('content')
<div v-cloak id="app" class="col-md-12">
    
    {{-- Formulario de Login --}}
    <div class="offset-md-4 col-md-4 col-s-12 main-card mb-4 card">
        
        <div class="card-body">

            {{-- Formulario de login --}}
            <section id="app" class="main">
            
                {{-- Campos de entrada --}}
                <div class="container login">

                    {{-- Selector de Idioma --}}
                    <div class="mb-4 text-right">
                        <a href="#" data-toggle="modal" data-target="#modal-language-selector">
                            <span class="language-text text-purple">{{language()->getName()}}</span> 
                            <span class="flag">{{language()->flag()}}</span>
                        </a>
                    </div>
                    {{--/Selector de Idioma --}}

                    {{-- Logo --}}
                    <div class="row d-flex mb-4 contact-info">
                        <div class="col-md-12 text-center">
                            <a href="@route('landing.home')">
                                <img class="img-fluid logo" aria-hidden="true" target="_blank" src="@asset('/assets/images/dashboard/logos/fikrea-square-medium-logo.png')" alt="" />
                            </a>
                        </div>
                    </div>
                    {{--/Logo --}}
              
                    <div class="row text-center">
                        <div class="col-12">
                            <form class="login-form" action="@route('login')" method="post">
                                @csrf

                                {{-- Dirección de Correo --}}
                                <div class="form-group text-center">
                                    @error('session')
                                        <input v-model="email" id="email" name="email" type="text" class="form-control input-field is-invalid" value="@old('email')" required autofocus />

                                        <div class="text-danger blink-me mt-2" role="alert">
                                            <span class="h5">
                                                {{$message}}.
                                            </span>
                                        </div>
                                        
                                        <div>
                                            <a href="@route('dashboard.register')">
                                                @lang('Pulse aquí para registrarse')
                                            </a>
                                        </div>              
                                    @else
                                        <input v-model="email" id="email" name="email" type="text" class="form-control input-field" placeholder="@lang('Dirección de correo')" autocomplete="email" required autofocus />                  
                                    @enderror
                                </div>
                                {{--/Dirección de Correo --}}
                   
                                <div class="form-group">
                                    
                                    <div class="input-group">

                                        {{-- Contraseña --}}
                                        <input v-model="password" id="password" name="password" :type="type" class="form-control input-field" placeholder="@lang('Contraseña')" required />
                                        {{--/Contraseña --}}
                                        
                                        {{-- Botón para ver la contraseña --}}
                                        @include('dashboard.auth.partials.show-password-button')
                                        {{--/Botón para ver la contraseña --}}

                                    </div>
                                </div>

                                {{-- Recordarme --}}
                                <div class="form-group text-right">
                                    <label class="check-container">
                                        <span class="text-info bold">@lang('Recordarme')</span>
                                        <input id="remember" name="remember" type="checkbox" />      
                                        <span class="square-check"></span>
                                    </label>
                                </div>
                                {{--/Recordarme --}}
                                
                                {{-- Botón Iniciar Sesión --}}
                                <div class="form-group submit-zone">
                                    <input :disabled="isDisabled" type="submit" value="@lang('Iniciar Sesión')" class="btn btn-purple py-2 px-5" />
                                </div>
                                {{--/Botón Iniciar Sesión --}}
                    
                            </form>
                        </div>
                    </div>
                </div>
            
                {{--/ Campos de entrada --}}
            
                {{-- Enlaces--}}
            
                <div class="container links">
            
                    {{-- Enlace de olvido contraseña --}}
                    <div class="form-group text-center">
                        <a href="@route('dashboard.rememberme')" class="text-primary">@lang('¿Ha olvidado su contraseña?')</a>
                    </div>
                    {{--/Enlace de olvido contraseña --}}
            
                    {{-- Enlace para crear una cuenta --}}
                    <div class="form-group text-center">
                        <a href="@route('dashboard.register')" class="btn btn-primary">@lang('¿No tiene aún cuenta en :app?', ['app' => config('app.name')])</a>
                    </div>
                    {{--/Enlace para crear una cuenta --}}
            
                    {{-- Enlace para usar la aplicación sin registro previo 
                         No visible en dispositivos móviles    
                    --}}
                    @desktop
                    <div class="form-group text-center">
                        <a href="@route('dashboard.home')" class="text-secondary">@lang('¿Desea usar :app sin registro previo?', ['app' => config('app.name')])</a>
                    </div>
                    @enddesktop
                    {{--/Enlace para usar la aplicación sin registro previo --}}
                    
                </div>
            
                {{--/ Enlaces --}}

            </section>
            {{--/Formulario de login --}}

        </div>

    </div>
    {{--/ Formulario de Login --}}

</div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
<script src="@mix('assets/js/dashboard/login.js')"></script>
@stop
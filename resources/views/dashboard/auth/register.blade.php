@extends('dashboard.layouts.no-menu-no-navbar')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@section('css')
<link rel="stylesheet" href="@mix('assets/css/dashboard/register.css')" />
@stop

{{-- 
    El contenido de la página
--}}
@section('content')
<div v-cloak id="app" class="col-md-12">
    
    {{-- Formulario de Registro --}}
    <div class="offset-md-3 col-md-6 col-s-12 main-card mb-3 card">
        
        <div class="card-body">

            {{-- Formulario de Registro --}}
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
                                <img class="img-fluid logo w-75" aria-hidden="true" target="_blank" src="@asset('/assets/images/dashboard/logos/fikrea-square-medium-logo.png')" alt="" />
                            </a>
                        </div>
                    </div>
                    {{--/Logo --}}

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="alert alert-warning bold ml-2 mr-2">
                                @lang('Se enviará un correo a la dirección indicada con un enlace para validar su cuenta')
                            </div>
                        </div>
                    </div>
              
                    <div class="row">
            
                        <div class="col-md-12 col-s-12 mt-2">

                            <form id="form" class="register-form ml-2 mr-2" action="@route('register')" method="post">
                                @csrf
            
                                <div class="form-group">
            
                                    <div class="row">
                                        <div class="col-md-4 mt-2">
                                            <label for="name" class="bold">@lang('Nombre')</label>
                                            @error('name')
                                            <input id="name" name="name" type="text" class="form-control input-field is-invalid" 
                                                value="@old('name')" v-model="name" data-name="@exists($user->name)" placeholder="@lang('Tu nombre')" autofocus />               
                                            <div class="invalid-feedback" role="alert">{{$message}}</div>               
                                            @else
                                            <input id="name" name="name" value="@old('name')" v-model="name" data-name="@exists($user->name)"
                                                type="text" class="form-control input-field" placeholder="@lang('Tu nombre')" autofocus />               
                                            @enderror
                                        </div>
            
                                        <div class="col-md-8 mt-2">
                                            <label for="lastname" class="bold">@lang('Apellidos')</label>
                                            @error('lastname')
                                            <input id="lastname" name="lastname" type="text" class="form-control input-field is-invalid" 
                                                value="@old('lastname')" v-model="lastname" data-name="@exists($user->lastname)" placeholder="@lang('Tus apellidos')" autofocus />               
                                            <div class="invalid-feedback" role="alert">{{$message}}</div>               
                                            @else
                                            <input id="lastname" name="lastname" value="@old('lastname')" v-model="lastname"
                                                data-name="@exists($user->lastname)" type="text" class="form-control input-field" placeholder="@lang('Tus apellidos')" autofocus />               
                                            @enderror
                                        </div>
                                    </div>
                                </div>
            
                                <div class="form-group">
                                    <label for="email" class="bold">@lang('Dirección de Correo')</label>
                                    @error('email')
                                    <input id="email" name="email" type="text" class="form-control input-field is-invalid" 
                                        value="@old('email')" v-model="email" data-email="@exists($user->email)" placeholder="@lang('Tu dirección de correo')" />               
                                    <div class="invalid-feedback" role="alert">{{$message}}</div>               
                                    @else
                                    <input id="email" name="email" value="@old('email')" v-model="email" data-email="@exists($user->email)"
                                        type="text" class="form-control input-field" placeholder="@lang('Tu dirección de correo')" />               
                                    @enderror
                                </div>
            
                                <div class="form-group mt-4">
                                    <label for="password" class="bold">@lang('Contraseña')</label>
                                    <div class="alert alert-info">
                                        @lang('Debe contener 8 caracteres, un número, una letra mayúscula, una minúscula y un carácter de entre los siguientes'):
                                        <span class="text-danger">. @ $ ! % * # ? &</span>
                                    </div>
                             
                                    <div class="input-group">
                                        @error('password')
                                        <input id="password" name="password" v-model="password" :type="type" class="form-control input-field is-invalid"
                                        placeholder="@lang('Contraseña')" autocomplete="off" />
                                        <div class="invalid-feedback blink-me" role="alert">{{$message}}</div>               
                                        @else
                                        <input id="password" name="password" v-model="password" :type="type" class="form-control input-field"
                                        placeholder="@lang('Contraseña')" autocomplete="off" />
                                        @enderror
            
                                        {{-- Botón para ver la contraseña --}}
                                        @include('dashboard.auth.partials.show-password-button')
                                        {{--/Botón para ver la contraseña --}}
            
                                    </div>
            
                                    {{-- Muestra la barra de color con la fortaleza de la contraseña 
                                         @link https://github.com/miladd3/vue-simple-password-meter    
                                    --}}
                                    <div>
                                        <template>    
                                            <password-meter :password="password" />
                                        </template>
                                    </div>
                                    {{--/Muestra la barra de color con la fortaleza de la contraseña --}}
            
                                </div>
                                
                                <div class="form-group">
            
                                    <div class="input-group">
                                        @error('password_confirmation')
                                        <input id="password_confirmation" name="password_confirmation" v-model="password_confirmation" :type="type" 
                                            class="form-control input-field is-invalid" placeholder="@lang('Repetir contraseña')" autocomplete="off" />
                                        <div class="invalid-feedback blink-me" role="alert">{{$message}}</div>               
                                        @else
                                        <input id="password_confirmation" name="password_confirmation" v-model="password_confirmation" :type="type" 
                                            :class="passwordNoMismatch ? 'is-invalid' : 'is-valid'"
                                            class="form-control input-field" placeholder="@lang('Repetir contraseña')" autocomplete="off" />
                                        @enderror
            
                                        {{-- Botón para ver la contraseña --}}
                                        @include('dashboard.auth.partials.show-password-button')
                                        {{--/Botón para ver la contraseña --}}
                                    </div>
            
                                </div>
            
                                <div class="form-group text-right">
                                    <label class="check-container">
                                        <span class="text bold">@lang('Aceptación de las condiciones de uso')</span>
                                        <input v-model="acceptUseConditions" class="form-control" type="checkbox" />
                                        <span class="square-check"></span>
                                    </label>
                                </div>
            
                                <div class="form-group submit-zone text-center">
                                    <button @click.prevent="register" :disabled="isDisabled" type="submit" class="btn btn-primary py-2 px-5">
                                        @lang('Continuar')
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            
                {{--/ Campos de entrada --}}

            </section>
            {{--/Formulario de Registro --}}

        </div>

    </div>
    {{--/ Formulario de Registro --}}

</div>
@stop

@section('scripts')
{{--
    Manipulación de Cookies
    @link https://github.com/js-cookie/js-cookie
--}}
<script src="https://cdn.jsdelivr.net/npm/js-cookie@rc/dist/js.cookie.min.js"></script>

{{-- El script personalizados --}}
<script src="@mix('assets/js/dashboard/register.js')"></script>
@stop

@extends('dashboard.layouts.no-menu-no-navbar')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@section('css')
<link rel="stylesheet" href="@mix('assets/css/dashboard/customer-contact.css')" />
@stop

{{-- 
    El contenido de la página
--}}
@section('content')
<div v-cloak id="app" class="col-md-12">
    
    {{-- Logo --}}
    <div class="offset-md-3 col-md-6 col-s-12 text-right mb-2">
        <a href="@route('landing.home')">
            <img aria-hidden="true" target="_blank" src="@asset('/assets/images/dashboard/logos/fikrea-medium-logo.png')" alt="">
        </a>
    </div>
    {{--/Logo --}}

    {{-- Mensaje cuando la información de contacto se ha guardado con éxito --}}
    <div v-show="success" class="offset-md-3 col-md-6 col-s-12">
        <div class="alert alert-success">
            <div class="bold text-success">
                <i class="fas fa-check-double"></i>
                @lang('Se he enviado la información de contacto con éxito')
            </div>
            <div class="mt-2">
                @lang('Nuestro personal comercial se pondrá en contacto con usted')
            </div>
            <div class="text-right mt-2">
                <a href="@route('dashboard.home')" class="btn btn-success">
                    @lang('Volver a :app', ['app' => config('app.name')])
                </a>
            </div>
        </div>
    </div>
    {{--/Mensaje cuando la información de contacto se ha guardado con éxito --}}

    {{-- El Formulario de Contacto --}}
    <div class="offset-md-3 col-md-6 col-s-12 main-card mb-4 card">
        
        <div class="card-body">

            <form id="form" v-on:submit.prevent="contact" action="@route('contact.save')">
            
                <div class="container">

                    {{-- Selector de Idioma --}}
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <a href="#" data-toggle="modal" data-target="#modal-language-selector">
                                <span class="language-text text-purple">{{language()->getName()}}</span> 
                                <span class="flag">{{language()->flag()}}</span>
                            </a>
                        </div>
                    </div>
                    {{--/Selector de Idioma --}}

                    {{-- Campos de Entrada del formulario --}}
                    <div class="row">
                        <div class="col-lg-12">

                            <div class="form-group">
                                <label for="email" class="col-form-label bold required">@lang('Su dirección de correo o teléfono y contactaremos con usted')</label>
                                <input value="javi@gestoy.com" v-model="email" id="email" name="email" type="text"  class="form-control required"
                                    placeholder="indique su email o teléfono" required />
                            </div>

                            <div class="form-group">
                                <label for="subject" class="col-form-label bold required">@lang('Asunto')</label>
                                <input value="asunto de test" v-model="subject" id="subject" name="subject" type="text" class="form-control required"
                                    placeholder="@lang('indique, de forma breve, el motivo de su consulta')" required />
                            </div>
                   
                            <div class="form-group">
                                <label for="content" class="col-form-label bold required">@lang('Contenido')</label>
                                <textarea id="content" v-model="content" name="content" rows="16" cols="30" class="form-control" 
                                    placeholder="@lang('¿que quiere contarnos?')">
                                </textarea>
                            </div>

                            <div class="form-group text-right">
                                <label class="check-container">
                                    <span data-toggle="modal" data-target="#modal-use-conditions" class="text-info small">
                                        <a href="#" data-toggle="modal" data-target="#modal-use-conditions">
                                            @lang('Acepto que mis datos sean tratados de acuerdo a las condiciones de uso')
                                        </a>
                                    </span>
                                    <input v-model="acceptUseConditions" class="form-control" type="checkbox" />
                                    <span class="square-check"></span>
                                </label>
                            </div>

                        </div>
                    </div>
                </div>
                {{-- Campos de Entrada del formulario --}}

                {{-- Botón de envío del formulario --}}
                <div class="col-md-12">
                    <div class="text-right">
                        <button :disabled="isDisabled" type="submit" class="btn btn-primary">
                            <i class="fas fa-share-square"></i>
                            @lang('Enviar Mensaje')
                        </button>
                    </div>
                </div>
                {{--/Botón de envío del formulario --}}

            </form>

            {{-- Condiciones de Uso y Políticas Aplicacables al sitio web --}}
            <div class="row mt-4 mb-4">

                <div class="col-md-12 text-center">
                    <a href="#" data-toggle="modal" data-target="#modal-legal-warning">
                        @lang('Aviso Legal')
                    </a>
                    |
                    <a href="#" data-toggle="modal" data-target="#modal-privacity-policy">
                        @lang('Política de Privacidad')
                    </a>
                    |
                    <a href="#" data-toggle="modal" data-target="#modal-cookies-policy">
                        @lang('Política de Cookies')
                    </a>
                    |
                    <a href="#" data-toggle="modal" data-target="#modal-return-policy">
                        @lang('Política de Devoluciones')
                    </a>
                    |
                    <a href="#" data-toggle="modal" data-target="#modal-use-conditions">
                        @lang('Condiciones de Uso')
                    </a>
                </div>
            </div>
            {{--/Condiciones de Uso y Políticas Aplicacables al sitio web --}}

        </div>

    </div>
     {{-- El Formulario de Contacto --}}

</div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
<script src="@mix('assets/js/dashboard/contact.js')"></script>
@stop
@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@section('css')
<link href="@mix('assets/css/dashboard/profile.css')" rel="stylesheet" />
@stop


{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @lang('Seleccione los procesos de validación del documento para cada firmante')
    <div class="page-title-subheading">
        @lang('Aquí podrá ver los datos de facturación del usuario '.$company->user->name)
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
</div>
{{--/El mensaje flash que se muestra cuando la oepracion ha tenido éxito --}}

<div id="app" class="row col-md-12 pr-0">
    <div class="col-md-8 pr-0">
        {{-- Datos de Facturación --}}
        <div class="main-card mb-3 card">
            <div class="card-body">
                <h5 class="card-title">@lang('Datos de Facturación')</h5>

                <div class="form-row">
                    {{-- Empresa/Razón Social --}}
                    <div class="col-md-8">
                        <div class="position-relative form-group">
                            <label for="companyName" class="">@lang('Empresa/Razón Social')</label>
                            <input value="@exists($company->name)" name="companyName" id="companyName" type="text"
                                class="form-control" form="form-user-data" disabled />
                        </div>
                    </div>
                    {{-- /Empresa/Razón Social --}}

                    {{-- CIF-NIF --}}
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <label for="companyCif" class="">@lang('CIF-NIF')</label>
                            <input value="@exists($company->cif)" name="companyCif" id="companyCif" type="text"
                                maxlength="50" class="form-control" form="form-user-data" disabled/>
                        </div>
                    </div>
                    {{-- /CIF-NIF --}}

                </div>

                {{-- Dirección de Correo para Facturación --}}
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label for="companyEmail" class="">@lang('Dirección de Correo para Facturación')</label>
                            <input value="@exists($company->email)" name="companyEmail" id="companyEmail" type="email"
                                class="form-control" form="form-user-data" disabled/>
                        </div>
                    </div>
                </div>
                {{-- /Dirección de Correo para Facturación --}}

                {{-- Dirección Postal --}}
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label for="companyAddress" class="">@lang('Dirección Postal')</label>
                            <input value="@exists($company->address)" name="companyAddress" id="companyAddress" type="text"
                                class="form-control" form="form-user-data" disabled/>
                        </div>
                    </div>
                </div>
                {{-- /Dirección Postal --}}

                <div class="form-row">

                    {{-- Código Postal --}}
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <label for="companyCodePostal" class="">@lang('Código Postal')</label>
                            <input value="@exists($company->code_postal)" name="companyCodePostal" id="companyCodePostal" maxlength="6" type="text" class="form-control" form="form-user-data" @keypress="soloNumeros" disabled/>
                        </div>
                    </div> 
                    {{-- /Código Postal --}}

                    {{-- Teléfono --}}
                    <div class="col-md-8">
                        <label for="phone" class="">@lang('Teléfono')</label>
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <input type="hidden" name="companyDialCode" form="form-user-data" id="company-bdial" value="@exists($company->dial_code)">
                                    {{-- Componente Select2
                                        @link https://github.com/godbasin/vue-select2

                                        Para las opciones originales del Select2@hasSection ('
                                        @link https://select2.org/configuration/options-api')
                                    --}}
                                    <Select2 id="company-select-country"  class="select2"
                                        :options="codeCountries" :settings="{tags: true, templateResult: changeTemplate, templateSelection: changeTemplate}" v-model="company_dial_code" data-bdial="@exists($company->dial_code)" @change="setCompanyDialCode" disabled/>
                                    
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <input value="@exists($company->phone)" name="companyPhone" id="companyPhone" type="text"
                                        class="form-control" @keypress="soloNumeros" form="form-user-data" disabled/>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- /Teléfono --}}

                </div>

                <div class="form-row">

                    {{-- Localidad --}}
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <label for="companyCity" class="">@lang('Localidad')</label>
                            <input value="@exists($company->city)" name="companyCity" id="companyCity" type="text" class="form-control"
                            @keypress="soloLetras" form="form-user-data" disabled/>
                        </div>
                    </div>

                    {{-- Provincia --}}
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <label for="companyProvince" class="">@lang('Provincia')</label>
                            <input value="@exists($company->province)" name="companyProvince" id="companyProvince" type="text"
                                class="form-control" @keypress="soloLetras" form="form-user-data" disabled/>
                        </div>
                    </div>
                    {{-- /Provincia --}}

                    {{-- País --}}
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <label for="companyCountry" class="">@lang('País')</label>

                            <Select2 value=""
                                    class="select2"
                                    :options="optionsCountries"
                                    v-model="companyCountry"
                                    form="form-user-data"
                                    disabled
                                />
                            
                        </div>
                    </div>
                    {{-- Dato país de la compañía que se envía, relacionado con el select del país --}}
                    <div>
                        <input type="hidden" name="companyCountry" id="companyCountry" v-model="companyCountry" form="form-user-data">
                    </div>
                    {{-- /País --}}

                </div>

            </div>
        </div>

        {{-- div oculto con informacion requerida para el vue --}}
        <div style="display: none;">
            {{-- Usuario invitado (1) o registrado (0) --}}
            @guest
            <input type="hidden" id="guest" value="1" />
            @else
            <input type="hidden" id="guest" value="0" />
            @endguest
            <select v-model="type" class="form-control" name="type" id="type">
                @foreach(
                    [
                        '0' => new \App\Enums\UserType(\App\Enums\UserType::PERSONAL_ACCOUNT), 
                        '1' => new \App\Enums\UserType(\App\Enums\UserType::BUSSINESS_ACCOUNT),
                    ] 
                as $value => $text)
                    @if ($value == $user->type)
                    <option selected value="{{$value}}">{{$text}}</option>
                    @else
                    <option value="{{$value}}">{{$text}}</option>
                    @endif
                @endforeach
            </select>
            <p id="select-country" data-dial="{{$user->dial_code}}"></p>
        </div>
    </div>
    {{--/Datos de Facturación --}}

    <div class="col-md-4 pr-0">
        {{-- Imágen del Perfil --}}
        <div class="main-card mb-3 card">
            <div class="card-body">
                <h5 class="card-title">@lang('Imagen del Perfil')</h5>

                <div class="col-md-12 text-center">
                    <div class="position-relative form-group">
                        @if ($company->user->image)
                        <img @click="openFileBrowser()" id="profileImage" class="profile-image" :src="profileImage" data-src="data:image/*;base64,{{$company->user->image}}" alt="" />
                        @else
                        {{-- Si el usuario no tiene una imagen del perfil, se mostrará la imagen por defecto --}}
                        <img @click="openFileBrowser()" id="profileImage" class="profile-image" :src="profileImage" data-src="@asset('assets/images/dashboard/avatars/empty-user.png')" alt="" />
                        @endif
                    </div>
                </div>

            </div>
        </div>
        {{--/ Imágen del Perfil --}}
    </div>
    {{-- Data pasada a vue --}}
    <div id="data"
      data-countries="@json($countries)"
      data-billing-country="@json($company->country ?? null)"
    ></div>
    {{-- /Data pasada a vue --}}

</div>

@stop

@section('scripts')

{{--
    API Google Maps
    @link https://developers.google.com/maps/documentation/javascript/overview
--}}
<script src="@config('google.api.maps.url')?key=@config('google.api.maps.key')&amp;libraries=places"></script> 

{{--
    Manipulación de Cookies
    @link https://github.com/js-cookie/js-cookie
--}}
<script src="https://cdn.jsdelivr.net/npm/js-cookie@rc/dist/js.cookie.min.js"></script>

{{-- Script de la pagina --}}
<script src="@mix('assets/js/dashboard/profile.js')"></script>

@stop
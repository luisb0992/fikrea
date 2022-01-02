@extends('workspace.layouts.no-signer-no-token')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

    {{-- Css Personalizado --}}
@section('css')
    <link href="@mix('assets/css/dashboard/profile.css')" rel="stylesheet" />
@stop


{{-- El encabezado con la ayuda para la página --}}
@section('help')
    <div>
        <span>@lang('Hola, bienvenido al espacio de trabajo de :app', ['app' => config('app.name')])</span>
        <div class="page-title-subheading">
            @lang('Aquí podrá ver los datos de facturación del usuario <b>:user</b>',[
            'user' => $company->user ? $company->user->getFullNameUser() : null
            ])
        </div>
    </div>
@stop

{{-- Aquí incluímos el contenido de la página --}}
@section('content')

    <div id="app" class="row col-md-12">

        {{-- Data pasada a vue --}}
        <div id="data" data-countries="@json($countries)" data-billing-country="@json($company->country ?? null)"></div>
        {{-- /Data pasada a vue --}}

        {{-- descargar los datos --}}
        @include('workspace.sharingdata.partials.action-buttons')

        {{-- Datos de Facturación --}}
        <div class="col-md-8 pr-0">
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
                                    maxlength="50" class="form-control" form="form-user-data" disabled />
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
                                    class="form-control" form="form-user-data" disabled />
                            </div>
                        </div>
                    </div>
                    {{-- /Dirección de Correo para Facturación --}}

                    {{-- Dirección Postal --}}
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label for="companyAddress" class="">@lang('Dirección Postal')</label>
                                <input value="@exists($company->address)" name="companyAddress" id="companyAddress"
                                    type="text" class="form-control" form="form-user-data" disabled />
                            </div>
                        </div>
                    </div>
                    {{-- /Dirección Postal --}}

                    <div class="form-row">

                        {{-- Código Postal --}}
                        <div class="col-md-4">
                            <div class="position-relative form-group">
                                <label for="companyCodePostal" class="">@lang('Código Postal')</label>
                                <input value="@exists($company->code_postal)" name="companyCodePostal"
                                    id="companyCodePostal" maxlength="6" type="text" class="form-control"
                                    form="form-user-data" @keypress="soloNumeros" disabled />
                            </div>
                        </div>
                        {{-- /Código Postal --}}

                        {{-- Teléfono --}}
                        <div class="col-md-8">
                            <label for="phone" class="">@lang('Teléfono')</label>
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <input type="hidden" name="companyDialCode" form="form-user-data" id="company-bdial"
                                            value="@exists($company->dial_code)">
                                        {{-- Componente Select2
                                        @link https://github.com/godbasin/vue-select2

                                        Para las opciones originales del Select2@hasSection('
                                        @link https://select2.org/configuration/options-api') --}}
                                        <Select2 id="company-select-country" class="select2" :options="codeCountries"
                                            :settings="{tags: true, templateResult: changeTemplate, templateSelection: changeTemplate}"
                                            v-model="company_dial_code" data-bdial="@exists($company->dial_code)"
                                            @change="setCompanyDialCode" disabled />

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <input value="@exists($company->phone)" name="companyPhone" id="companyPhone"
                                            type="text" class="form-control" @keypress="soloNumeros" form="form-user-data"
                                            disabled />
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
                                <input value="@exists($company->city)" name="companyCity" id="companyCity" type="text"
                                    class="form-control" @keypress="soloLetras" form="form-user-data" disabled />
                            </div>
                        </div>

                        {{-- Provincia --}}
                        <div class="col-md-4">
                            <div class="position-relative form-group">
                                <label for="companyProvince" class="">@lang('Provincia')</label>
                                <input value="@exists($company->province)" name="companyProvince" id="companyProvince"
                                    type="text" class="form-control" @keypress="soloLetras" form="form-user-data"
                                    disabled />
                            </div>
                        </div>
                        {{-- /Provincia --}}

                        {{-- País --}}
                        <div class="col-md-4">
                            <div class="position-relative form-group">
                                <label for="companyCountry" class="">@lang('País')</label>

                                <Select2 value="" class="select2" :options="optionsCountries" v-model="companyCountry"
                                    form="form-user-data" disabled />
                            </div>
                        </div>
                        {{-- Dato país de la compañía que se envía, relacionado con el select del país --}}
                        <div>
                            <input type="hidden" name="companyCountry" id="companyCountry" v-model="companyCountry"
                                form="form-user-data">
                        </div>
                        {{-- /País --}}

                    </div>

                </div>
            </div>

            {{-- div oculto con informacion requerida para el vue --}}
            <div class="d-none">
                <input type="hidden" id="guest" value="1" />
                <select v-model="type" class="form-control" name="type" id="type">
                    <option selected value="{{ \App\Enums\UserType::PERSONAL_ACCOUNT }}">@lang('Cuenta Personal')</option>
                </select>
                <p id="select-country" data-dial="{{ $company->dial_code }}"></p>
            </div>
        </div>
        {{-- /Datos de Facturación --}}

        {{-- Imágen del Perfil --}}
        <div class="col-md-4 pr-0">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Imagen del Perfil')</h5>
                    <div class="form-group w-100">
                        @if ($company->user->image)
                            <img class="img-fluid profile-image" @click="openFileBrowser()" id="profileImage"
                                :src="profileImage" data-src="data:image/*;base64,{{ $company->user->image }}"
                                alt="profile" />
                        @else
                            <img class="img-fluid profile-image" @click="openFileBrowser()" id="profileImage"
                                :src="profileImage" data-src="@asset('assets/images/dashboard/avatars/empty-user.png')"
                                alt="profile" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
        {{-- / Imágen del Perfil --}}

        {{-- datos de la firma digital si es necesario --}}
        @includeWhen($company->signature, 'workspace.sharingdata.partials.data-signature', ['company' => $company])

        {{-- descargar los datos --}}
        @include('workspace.sharingdata.partials.action-buttons')
    </div>

@stop

@section('scripts')

    {{-- API Google Maps
    @link https://developers.google.com/maps/documentation/javascript/overview --}}
    <script src="@config('google.api.maps.url')?key=@config('google.api.maps.key')&amp;libraries=places"></script>

    {{-- Manipulación de Cookies
    @link https://github.com/js-cookie/js-cookie --}}
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@rc/dist/js.cookie.min.js"></script>

    {{-- Script de la pagina --}}
    <script src="@mix('assets/js/dashboard/profile.js')"></script>

@stop

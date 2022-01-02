@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

    {{-- Css Personalizado --}}
@section('css')
    <link href="@mix('assets/css/dashboard/profile.css')" rel="stylesheet" />
@stop

{{-- El encabezado con la ayuda para la página --}}
@section('help')
    <div>@lang('Complete la información de su perfil')
        <div class="page-title-subheading">@lang('También puede cambiar las credenciales de acceso')</div>
    </div>
@stop

{{-- Aquí incluímos el contenido de la página --}}
@section('content')

    {{-- El mensaje flash que se muestra cuando la operación ha tenido éxito --}}
    <div class="offset-md-3 col-md-6">
        @include('dashboard.sections.body.message-success')
    </div>
    {{-- /El mensaje flash que se muestra cuando la oepracion ha tenido éxito --}}

    <div v-cloak id="app" class="row col-md-12 pr-0">

        {{-- Modales --}}
        @include('dashboard.modals.profile.help-for-guest-user')
        @include('dashboard.modals.profile.share-billing-data')
        @include('dashboard.modals.profile.share-billing-data-via-link')
        @include('dashboard.modals.profile.not-save-billing-data')
        @include('dashboard.modals.profile.not-empty-billing-data')
        {{-- /Modales --}}

        {{-- Data pasada a vue --}}
        <div id="data" data-countries="@json($countries)"
            data-billing-country="@json(Auth::user()->billing->country ?? null)"></div>
        {{-- /Data pasada a vue --}}

        <div class="col-md-6 pr-0">

            {{-- Datos Personales --}}
            @include('dashboard.profile.person-data')
            {{-- / Datos Personales --}}

            {{-- Datos de Facturación --}}
            @auth
                @include('dashboard.profile.billing-data')
            @endauth
            {{-- /Datos de Facturación --}}

            {{-- Botones --}}
            @include('dashboard.profile.action-buttons')
            {{-- /Botones --}}

        </div>

        <div class="col-md-6 pr-0">

            {{-- Credenciales de Acceso --}}
            @include('dashboard.profile.credentials')
            {{-- /Credenciales de Acceso --}}

            {{-- Imágen del Perfil --}}
            @include('dashboard.profile.profile-image')
            {{-- / Imágen del Perfil --}}

        </div>
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

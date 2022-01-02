{{--
    Estructura de la landing page

    @author javieru <javi@gestoy.com>
    @copyright 2021 Retail Servicios Externos
--}}
@extends('landing.layouts.main')

{{-- Título de la Página --}}
@section('title', __('Página de Inicio'))

{{-- Css Personalizado --}}
@section('css')
@stop

@push('page-styles')
    <link rel="stylesheet" href="@asset('assets/css/landing/fixed-bottom-button.css')" />
@endpush

@section('body')

{{-- Menú de la aplicación --}}
@include('landing.sections.body.menu')

{{-- Carousel - Qué es --}}
@include('landing.sections.body.carousel-what-is')

{{-- Más información sobre la aplicación
     Mostrando las secciones que figuran debajo
--}}
<div id="more-info"></div>

{{-- Servicios --}}
@include('landing.sections.body.services')

{{-- Contador --}}
@include('landing.sections.body.counter')

{{-- Precios/Planes --}}
@include('landing.sections.body.prices')

{{-- Características --}}
@include('landing.sections.body.features')

{{-- Como funciona --}}
@include('landing.sections.body.howItWorks')

{{-- Nuestros clientes --}}
@include('landing.sections.body.ourClients')

{{-- Pié de página --}}
@include('landing.sections.body.footer')

{{-- Loader --}}
@include('landing.sections.body.loader')

{{-- Botón de Whatsapp --}}
@include('landing.sections.body.whatsapp')

@stop

{{-- Scripts Personalizados --}}
@section('scripts')

{{-- Consentimiento de Cookies --}}
<script src="@asset('assets/js/landing/vendor/cookieconsent.js')"></script>
<script src="@mix('assets/js/landing/cookies.js')"></script>
@stop

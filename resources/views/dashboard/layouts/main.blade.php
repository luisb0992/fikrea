{{--
    Layout de la paǵina de Dashboard

    @author javieru <javi@gestoy.com>
    @copyright 2021 Retail Servicios Externos    
--}}

<!doctype html>
<html lang="@locale">

<head>

    {{-- Google Anayltics global tag --}}
    @include('vendor.google.analytics.gtag')

    {{-- El título de la página --}}
    <title>@config('app.name') → @yield('title')</title>

    @include('dashboard.sections.head.meta')
    @include('dashboard.sections.head.csrf')
    @include('dashboard.sections.head.favicon')

    @include('dashboard.sections.head.css')

    {{-- El css personalizado --}}
    @section('css')
    @show

    @stack('page-styles')

</head>

<body>

{{--Scroll top button--}}
@include('common.scroll-up.scrollup')
{{--/Scroll top button--}}

<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
    @include('dashboard.sections.body.navbar')
    <div class="app-main">
        @include('dashboard.sections.body.menu')
        @include('dashboard.sections.body.content')
    </div>
</div>

@include('dashboard.sections.body.footer')


{{-- Modales --}}
@include('dashboard.modals.languages.language-selector')
{{-- /Modales --}}

{{-- Usar las rutas de la aplicación de manera simple desde Javascript urilizando el helper route() --}}
@routes

@include('dashboard.sections.body.scripts')
@section('scripts')
@show

@stack('page-scripts')
</body>

</html>
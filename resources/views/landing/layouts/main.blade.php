<!doctype html>
<html lang="@locale" prefix="og: https://ogp.me/ns#">

<head>
    {{-- Google Anayltics global tag --}}
    @include('vendor.google.analytics.gtag')

    {{-- El título de la página --}}
    <title>@config('app.name') → @yield('title')</title>

    {{-- La sección de etiquetas meta --}}
    @include('landing.sections.head.meta')

    {{--    @include('dashboard.sections.head.csrf')--}}
    @include('landing.sections.head.favicon')

    {{--    @include('dashboard.sections.head.css')--}}
    @include('landing.sections.head.css')

    {{-- El css personalizado --}}
    @section('css')
    @show
    @stack('page-styles')

    @stack('page-styles')
</head>

<body>
{{-- El cuerpo de la página --}}
@section('body')
@show

{{--@include('dashboard.sections.body.footer')--}}

{{-- Modales --}}
@include('landing.modals.languages.language-selector')
{{-- /Modales --}}

{{-- Usar las rutas de la aplicación de manera simple desde Javascript urilizando el helper route() --}}
@routes

@include('landing.sections.body.scripts')
{{--@include('dashboard.sections.body.scripts')--}}
@section('scripts')
@show
@stack('page-scripts')
</body>
</html>
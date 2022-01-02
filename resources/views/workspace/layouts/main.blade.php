{{-- Layout de la paǵina del WorkSpace del usuario firmante

    @author javieru <javi@gestoy.com>
    @copyright 2021 Retail Servicios Externos --}}
<!doctype html>
<html lang="@locale">

<head>

    {{-- El título de la página --}}
    <title>@config('app.name') → @yield('title')</title>

    {{-- Usamos las mismas secciones head que para el Dashboard --}}
    @include('dashboard.sections.head.meta')
    @include('dashboard.sections.head.csrf')
    @include('dashboard.sections.head.favicon')
    @include('dashboard.sections.head.css')

    {{-- El css personalizado --}}
    @section('css')
    @show

</head>

<body>

    {{-- Scroll top button --}}
    @include('common.scroll-up.scrollup')
    {{-- Scroll top button --}}

    <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">

        @include('workspace.sections.body.navbar')

        <div class="app-main">

            @include('workspace.sections.body.menu')

            @include('workspace.sections.body.content')

        </div>
    </div>

    {{-- Usamos el mismo pié de página que para el dashboard --}}
    @include('dashboard.sections.body.footer')

    {{-- Modales --}}

    {{-- Usamos la misma modal de selección de idioma que para el dashboard --}}
    @include('dashboard.modals.languages.language-selector')

    {{-- /Modales --}}

    @include('dashboard.sections.body.scripts')
    @section('scripts')
    @show
</body>

</html>

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

    <link rel="stylesheet" href="@mix('assets/css/workspace/home.css')"/>
    @stack('page-styles')
</head>

<body>
{{-- Scroll top button --}}
@include('common.scroll-up.scrollup')
{{-- Scroll top button --}}

<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
    @include('workspace.sections.body.navbar')
    <div class="app-main">
        <div id="menu" class="app-sidebar sidebar-shadow">
            {{-- Rutas de las solicitudes --}}
            <div id="request" data-cancel-process="@route('workspace.signer.cancel', ['token' => $token])"
                 data-process-canceled="@route('workspace.signer.canceled', ['token' => $token])">
            </div>
            {{-- /Rutas de las solicitudes --}}
            {{--  mensajes de interaccion para el workspace  --}}
            <div id="msjExitWorkspace" data-success="@lang('Saliendo del espacio de trabajo')..."></div>

            {{--  url para salir del proceso  --}}
            <div id="exitWorkspace" data-exit-url="@route('workspace.exit', ['token' => $token])"></div>

            <div class="app-header__logo">
                <div class="logo-src"></div>
                <div class="header__pane ml-auto">
                    <div>
                        <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                                data-class="closed-sidebar">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="app-header__mobile-menu">
                <div>
                    <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
                    </button>
                </div>
            </div>

            <div class="scrollbar-sidebar">
                <div class="app-sidebar__inner">
                    <ul class="vertical-nav-menu">
                        {{-- Inicio --}}
                        <li class="app-sidebar__heading">@lang('Inicio')</li>
                        <li>
                            <a href="javascript:void(0)">
                                <i class="metismenu-icon pe-7s-home"></i>
                                @lang('Inicio')
                            </a>
                        </li>
                        <li class="app-sidebar__heading">@lang('Personas Intervinientes')</li>
                        <li>
                            @if ( $sharing->user->email )
                                <a href="mailto:{{ $sharing->user->email }}">
                                    <i class="metismenu-icon pe-7s-user"></i>
                                    @if ($sharing->user->name || $sharing->user->lastname)
                                        <span>
                                            {{ $sharing->user->name }} {{ $sharing->user->lastname }}
                                        </span>
                                    @else
                                        <span>
                                            {{ $sharing->user->email }}
                                        </span>
                                    @endif
                                </a>
                            @elseif ( $sharing->user->phone )
                                <a href="tel:{{ $sharing->user->phone }}">
                                    <i class="metismenu-icon pe-7s-user"></i>
                                    @if ($sharing->user->name || $sharing->user->lastname)
                                        {{ $sharing->user->name }} {{ $sharing->user->lastname }}
                                    @else
                                        <span>
                                            {{ $sharing->user->phone }}
                                        </span>
                                    @endif
                                </a>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="app-main__outer">
            <div class="app-main__inner">
                <div class="app-page-title">
                    <div class="page-title-wrapper">
                        <div class="page-title-heading">
                            <div class="page-title-icon">
                                <i class="pe-7s-light icon-gradient bg-mean-fruit"></i>
                            </div>
                            @yield('help')
                        </div>
                    </div>
                </div>
                @yield('content')
            </div>

        </div>
    </div>
</div>

{{-- Usamos el mismo pié de página que para el dashboard --}}
@include('dashboard.sections.body.footer')

{{-- Modales --}}

{{-- Usamos la misma modal de selección de idioma que para el dashboard --}}
@include('dashboard.modals.languages.language-selector')

{{-- /Modales --}}

{{-- Scripts--}}
<script src="@asset('assets/js/vue/vue.min.js')"></script>
<script src="@asset('assets/js/vue/polyfill.min.js')"></script>
<script src="@asset('assets/js/vue/bootstrap-vue.js')"></script>
<script src="@asset('assets/js/vue/v-tooltip.min.js')"></script>
<script src="@asset('assets/js/dashboard/vendor/axios.min.js')"></script>
<script src="@asset('assets/js/csrf/axios.csrf.js')"></script>
<script src="@asset('assets/js/dashboard/vendor/jquery.min.js')"></script>
<script src="@asset('assets/js/dashboard/vendor/popper.min.js')"></script>
<script src="@asset('assets/js/dashboard/vendor/toastr.min.js')"></script>
<script src="@mix('assets/js/config/config.js')"></script>
<script src="@asset('assets/js/dashboard/vendor/HoldOn.min.js')"></script>
<script src="@asset('assets/js/libs/interact.min.js')"></script>
<script src="@mix('assets/js/common/scroll-to-top.js')"></script>

@routes
@stack('page-scripts')
</body>
</html>

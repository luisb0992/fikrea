<div class="app-sidebar sidebar-shadow">
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
    <div class="app-header__menu">
        <span>
            <button type="button"
                class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                <span class="btn-icon-wrapper">
                    <i class="fa fa-ellipsis-v fa-w-6"></i>
                </span>
            </button>
        </span>
    </div>

    <div class="scrollbar-sidebar">
        <div class="app-sidebar__inner">
            <ul class="vertical-nav-menu">

            {{-- Inicio --}}

                <li class="app-sidebar__heading">@lang('Inicio')</li>

                {{-- Enlace a dashboard de backend --}}
                <li>
                    @if (request()->is('admin'))
                    <a href="@route('backend.home')" class="mm-active">
                    @else
                    <a href="@route('backend.home')">    
                    @endif
                        <i class="metismenu-icon pe-7s-home"></i>
                        <span class="title">
                            @lang('Inicio')
                        </span>
                    </a>
                </li>
                {{-- / Enlace a dashboard de backend --}}

                {{-- Regresar a zona de usuario --}}
                <li>
                    <a href="@route('dashboard.home')">    
                        <i class="metismenu-icon pe-7s-user"></i>
                        @lang('Zona Usuario')
                    </a>
                </li>
                {{-- / Regresar a zona de usuario --}}

            {{-- / Inicio --}}

            {{-- Cuentas de Usuario --}}

                <li class="app-sidebar__heading">@lang('Cuentas de Usuario')</li>

                {{-- Listado de usuarios --}}
                <li>
                    @if (request()->is('admin/users/list'))
                    <a href="@route('backend.users.list')" class="mm-active">
                    @else
                    <a href="@route('backend.users.list')">
                    @endif
                        <i class="metismenu-icon pe-7s-users"></i>
                        <span class="title">
                            @lang('Usuarios')
                        </span>

                        <span class="ml-auto badge badge-pill badge-danger badge-contacts">
                            {{$stats->users}}
                        </span>
                    </a>
                </li>
                {{-- / Listado de usuarios --}}

                {{-- Listado de usuarios registrados --}}
                <li>
                    @if (request()->is('admin/registered/list'))
                    <a href="@route('backend.registered.list')" class="mm-active">
                    @else
                    <a href="@route('backend.registered.list')">
                    @endif
                        <i class="metismenu-icon pe-7s-users"></i>
                        <span class="title">
                            @lang('Registrados')
                        </span>

                        <span class="ml-auto badge badge-pill badge-warning badge-contacts">
                            {{$stats->registered}}
                        </span>
                    </a>
                </li>
                {{-- / Listado de usuarios registrados --}}
                
                {{-- Listado de clientes --}}
                <li>
                    @if (request()->is('admin/clients/list'))
                    <a href="@route('backend.clients.list')" class="mm-active">
                    @else
                    <a href="@route('backend.clients.list')">
                    @endif
                        <i class="metismenu-icon pe-7s-users"></i>
                        <span class="title">
                            @lang('Clientes')
                        </span>

                        <span class="ml-auto badge badge-pill badge-success badge-contacts">
                            {{$stats->clients}}
                        </span>
                    </a>
                </li>
                {{-- / Listado de clientes --}}

            {{-- / Cuentas de Usuario --}}

            {{-- Subscripciones --}}

                <li class="app-sidebar__heading">@lang('Subscripciones')</li>

                {{-- Listado de Subscripciones --}}
                <li>
                    @if (request()->is('admin/subscriptions/list'))
                    <a href="@route('backend.subscriptions.list')" class="mm-active">
                    @else
                    <a href="@route('backend.subscriptions.list')">
                    @endif
                        <i class="metismenu-icon pe-7s-users"></i>
                        <span class="title">
                            @lang('Subscripciones')
                        </span>

                        <span class="ml-auto badge badge-pill badge-success badge-contacts">
                            {{$stats->clients}}
                        </span>
                    </a>
                </li>
                {{-- / Listado de Subscripciones --}}

                {{-- Listado de facturas --}}
                <li>
                    @if (request()->is('admin/orders/list'))
                    <a href="@route('backend.orders.list')" class="mm-active">
                    @else
                    <a href="@route('backend.orders.list')">
                    @endif
                        <i class="metismenu-icon pe-7s-users"></i>
                        <span class="title">
                            @lang('Facturas')
                        </span>

                        <span class="ml-auto badge badge-pill badge-info badge-contacts">
                            {{$stats->orders}}
                        </span>
                    </a>
                </li>
                {{-- / Listado de facturas --}}
                
                {{--  Listado de planes --}}
                <li>
                    @if (request()->is('admin/plans/list'))
                    <a href="@route('backend.plans.plans')" class="mm-active">
                    @else
                    <a href="@route('backend.plans.plans')">
                    @endif
                        <i class="metismenu-icon pe-7s-users"></i>
                        <span class="title">
                            @lang('Planes')
                        </span>
                    </a>
                </li>
                {{-- / Listado de planes --}}

            {{-- / Subscripciones --}}

            {{-- Envíos de mensajes SMS --}}

                <li class="app-sidebar__heading">@lang('Mensajes enviados')</li>

                {{-- Listado de mensajes sms --}}
                <li>
                     
                    <a href="@route('backend.sms.list')"
                        class="{{ request()->is('admin/sms/list') ? 'mm-active' : '' }}">
                        <i class="metismenu-icon pe-7s-mail-open"></i>
                        <span class="title">
                            @lang('SMSs')
                        </span>

                        <span class="ml-auto badge badge-pill badge-primary">
                            {{$stats->smses}}
                        </span>
                    </a>
                </li>
                {{-- / Listado de mensajes sms --}}

            {{-- / Envíos de mensajes SMS --}}
            
            {{-- Google Analítica --}}

                <li class="app-sidebar__heading">@lang('Analíticas')
                    <span class="text-lowercase">
                        @config('google.analytics.user')
                    </span>
                </li>
            
                <li>
                    <a href="https://analytics.google.com/analytics/web">
                        <i class="metismenu-icon pe-7s-display1"></i>
                        <span class="text">
                            @lang('Google Analytics')
                        </span>

                        <span class="ml-auto badge badge-pill badge-dark badge-contacts">
                            {{$stats->visitors->count()}}
                        </span>
                    </a>
                 </li>

            {{-- / Google Analítica --}}

            </ul>

        </div>
    </div>
</div>

<div class="app-header header-shadow">
    
    <div class="app-header__logo">
        <div class="pt-1">
            <a href="/">
                <img class="logo-src" aria-hidden="true" target="_blank" src="@asset('/assets/images/dashboard/logos/fikrea-mini-logo.png')" alt="">
            </a>
        </div>
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
                    <i class="fa fa-search"></i>
                </span>
            </button>
        </span>
    </div>
    <div class="app-header__content">
        <div class="app-header-left">
            <div class="search-wrapper">
                @if (!isset($search))
                <div class="input-holder">
                @else
                <div class="input-holder d-none">
                @endif
                    <input type="text" class="search-input" placeholder="@lang('documento a buscar')" />
                    <button class="search-icon" 
                        data-search-request="@route('dashboard.search.find.document', ['query' => 'query'])">
                        <span></span>
                    </button>
                </div>
                <button class="close"></button>
            </div>
            <ul class="header-menu nav">
                @auth
                    @if (!isset($config))
                    <li class="dropdown nav-item">
                        <a href="@route('dashboard.config')" class="nav-link">
                            <i class="nav-link-icon fa fa-cog"></i>
                            @lang('Configuración')
                        </a>
                    </li>
                    @endif
                @endauth
            </ul>
        </div>
        <div class="app-header-right">
            <div class="header-btn-lg pr-0">
                <div class="widget-content p-0">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="btn-group">
                                
                                <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="p-0 btn">
                                    {{-- La imagen del perfil del usuario --}}
                                    @if ($user && $user->image)
                                        <img class="profile-icon rounded-circle" src="data:image/*;base64,{{$user->image}}" alt="" />
                                    @else
                                        <img class="profile-icon rounded-circle" src="@asset('assets/images/dashboard/avatars/empty-user.png')" alt="" />
                                    @endif
                                    {{--/ La imagen del perfil del usuario --}}
                                    <i class="fa fa-angle-down ml-2 opacity-8"></i>
                                </a>
                               
                                <div id="user-menu" tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu dropdown-menu-right">
                                    
                                    <a href="#" data-toggle="modal" data-target="#modal-language-selector" class="dropdown-item">
                                        <span class="flag">{{language()->flag()}}</span>
                                        <span class="language-text">{{language()->getName()}}</span> 
                                    </a>
                                    
                                    <div tabindex="-1" class="dropdown-divider"></div>
                                    
                                    <a href="@route('dashboard.profile')" class="dropdown-item">
                                        <i class="metismenu-icon pe-7s-id"></i> 
                                        @lang('Perfil')
                                    </a>

                                    @auth
                                    <a href="@route('dashboard.config')" class="dropdown-item">
                                        <i class="metismenu-icon pe-7s-portfolio"></i>
                                        @lang('Configuración')
                                    </a>
                                    @endauth

                                    @if ($user && $user->notifications->filter(fn ($notification) => !$notification->read_at)->isNotEmpty())
                                    <a href="@route('dashboard.home')" class="dropdown-item">
                                        <i class="metismenu-icon pe-7s-bell"></i>
                                        @lang('Notificaciones')
                                    </a>
                                    @endif

                                    @auth
                                    <div tabindex="-1" class="dropdown-divider"></div>
                                    <a href="@route('dashboard.logout')" class="dropdown-item">
                                        <i class="metismenu-icon pe-7s-user"></i>
                                        @lang('Cerrar Sesión')
                                    </a>
                                    @endauth

                                    @guest
                                    <a href="@route('dashboard.login')" class="dropdown-item">
                                        <i class="metismenu-icon pe-7s-user"></i>
                                        @lang('Iniciar Sesión')
                                    </a>

                                    <a href="@route('dashboard.register')" class="dropdown-item">
                                        <i class="metismenu-icon pe-7s-add-user"></i>
                                        @lang('Registrarse')
                                    </a>
                                    @endguest
                                    
                                </div>
                            </div>
                        </div>
                        <div class="widget-content-left  ml-3 header-user-info">
                        
                            {{-- Nombre y direción de correo del usuario --}}
                            <div class="widget-heading">
                                @exists($user->name)
                            </div>
                            <div class="widget-subheading">
                                @auth
                                    {{$user->email}}
                                @else
                                    {{-- Si el usuario es invitado y ha modificado su perfil se muestra la dirección de correo.
                                         En caso contrario se oculta
                                    --}}
                                    @if ($user->guestHasChangedProfile())
                                        {{$user->email}}
                                    @endif
                                @endauth
                            </div>
                            {{--/Nombre y direción de correo del usuario --}}
                 
                        </div>

                        @if ($user)
                        <div class="widget-content-right header-user-info ml-3">
                            @if ($user->notifications->filter(fn ($notification) => !$notification->read_at)->isEmpty())
                            <button disabled type="button" class="btn-shadow p-1 btn btn-secondary btn-sm"
                                data-toggle="tooltip" data-placement="bottom" title="@lang('No tiene notificaciones')">
                                <i class="fas fa-bell fa-2x pr-1 pl-1"></i>
                            </button>
                            @else
                            <a href="@route('dashboard.home')" class="btn-shadow p-1 btn btn-primary btn-sm"
                                data-toggle="tooltip" data-placement="bottom" title="@lang('Tiene notificaciones')">
                                <i class="fas fa-bell fa-2x pl-2 pt-1 pb-1 pr-1"></i>
                                <span class="notification-counter" class="text-warning bold">                       
                                    {{$user->notifications->filter(fn ($notification) => !$notification->read_at)->count()}}
                                </span>
                            </a>
                            @endif
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
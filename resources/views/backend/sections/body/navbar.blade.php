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
                    <i class="fa fa-ellipsis-v fa-w-6"></i>
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
                    <input type="text" class="search-input" placeholder="@lang('usuario a buscar')">
                    <button class="search-icon" 
                        data-search-request="@route('backend.search.find.user', ['query' => 'query'])">
                        <span></span>
                    </button>
                </div>
                <button class="close"></button>
            </div>
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
                                        <span class="language-text">{{language()->getName()}}</span> 
                                        <span class="flag">{{language()->flag()}}</span>
                                    </a>
                                    
                                    <div tabindex="-1" class="dropdown-divider"></div>
                                    
                                    <a href="@route('dashboard.profile')" class="dropdown-item">
                                        <i class="metismenu-icon pe-7s-id"></i> 
                                        @lang('Perfil')
                                    </a>

                                    <a href="@route('dashboard.home')" class="dropdown-item">
                                        <i class="metismenu-icon pe-7s-home"></i>
                                        @lang('Inicio')
                                    </a>
                                    
                                    @auth
                                    <a href="@route('dashboard.config')" class="dropdown-item">
                                        <i class="metismenu-icon pe-7s-portfolio"></i>
                                        @lang('Configuración')
                                    </a>
                                    @endauth

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
                                @exists($user->email)
                            </div>
                            {{--/Nombre y direción de correo del usuario --}}
                 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
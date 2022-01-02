{{--
    Menú responsivo de la aplicación que se muestra en la parte superior
--}}

<nav id="ftco-navbar" class="navbar navbar-expand-lg ftco_navbar ftco-navbar-light scrolled awake">
    <div class="container">

        <a class="navbar-brand" href="@route('landing.home')">
            <img aria-hidden="true" target="_blank" src="@asset('/assets/images/dashboard/logos/fikrea-mini-logo.png')" alt="" />
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="oi oi-menu"></span> 
            @lang('Menu')
        </button>

        <div class="collapse navbar-collapse" id="ftco-nav">
            {{-- 
                Opciones del Menú

                La opción activa del menú se marca añadiendo la clase "active"    
            --}}
            <ul class="navbar-nav ml-auto">
                
                {{-- Inicio --}}
                @if (request()->is('landing'))
                <li class="nav-item active">
                    <a href="@route('landing.home')" class="nav-link">@lang('Inicio')</a>
                </li>
                @else
                <li class="nav-item">
                    <a href="@route('landing.home')" class="nav-link">@lang('Inicio')</a>
                </li>
                @endif
      
                {{-- Acceso al Dashboard o Zona de Usuario --}}
                @guest
                <li class="nav-item">
                    <a href="@route('dashboard.profile')" class="nav-link nav-link-big">@lang('Úsalo Gratis')</a>
                </li>
                @endguest
       
                {{-- Iniciar Sesion --}}
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="@route('dashboard.login')">@lang('Iniciar Sesión')</a>
                    </li>
                @endguest

                {{-- Registrate --}}
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="@route('dashboard.register')">@lang('Regístrate')</a>
                    </li>
                @endguest
                
                {{-- Contacto --}}
                @guest
                    @if (request()->is('landing/contact'))
                    <li class="nav-item active">
                        <a href="@route('contact.show')" class="nav-link">@lang('Contacto')</a>
                    </li>
                    @else
                    <li class="nav-item">
                        <a href="@route('contact.show')" class="nav-link">@lang('Contacto')</a>
                    </li>
                    @endif
                @endguest

                {{-- Zona de Usuario --}}
                @auth
                    @if (request()->is('landing/user'))
                    <li class="nav-item active">
                        <a href="@route('dashboard.home')" class="nav-link">@lang('Zona de Usuario')</a>
                    </li>
                    @else
                    <li class="nav-item">
                        <a href="@route('dashboard.home')" class="nav-link">@lang('Zona de Usuario')</a>
                    </li>
                    @endif
                @endauth

                {{-- Cerrar Sesión --}}
                @auth
                <li class="nav-item">
                    <a href="#" class="nav-link" data-toggle="modal" data-target="#modal-logout">@lang('Cerrar Sesión')</a>
                </li>
                @endauth

                {{-- Selector de idioma de la página --}}
                <li class="nav-item text-right">
                    <a href="#" class="nav-link" data-toggle="modal" data-target="#modal-language-selector">
                        <span class="language-title">@lang('Idioma') :</span>
                        <span class="language-text">{{language()->getName()}}</span>
                        {{language()->flag()}}
                    </a>
                </li>
            </ul>
            {{--/Opciones del Menú --}}

        </div>
    </div>
</nav>

{{-- Las modales que se abren desde el menú --}}
<div id="main-menu">
    
    {{-- La modal de confirmación del cierre de sesión --}}
    @include('landing.modals.auth.logout')

</div>
{{--/ Las modales que se abren desde el menú --}}
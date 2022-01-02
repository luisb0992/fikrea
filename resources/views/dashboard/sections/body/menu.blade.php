<div id="app-menu">
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
            <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                <span class="btn-icon-wrapper">
                    <i class="fa fa-ellipsis-v fa-w-6"></i>
                </span>
            </button>
        </span>
    </div>

    <div class="scrollbar-sidebar">
        <div class="app-sidebar__inner">
            <ul class="vertical-nav-menu">

            {{-- Home --}}

                {{-- Inicio --}}
                <li class="app-sidebar__heading">@lang('Inicio')</li>
                <li>
                    @if (request()->is('dashboard'))
                        <a href="@route('dashboard.home')" class="mm-active">
                        @else
                            <a href="@route('dashboard.home')">
                    @endif
                    <i class="metismenu-icon pe-7s-home"></i>
                    <span class="title">
                        @lang('Inicio')
                    </span>
                    </a>
                </li>
                {{-- /Inicio --}}

                {{-- Backend de administración --}}
                @admin
                <li>
                    <a href="@route('backend.home')">
                        <i class="metismenu-icon pe-7s-key"></i>
                        <span class="bg-info text-white p-1 rounded">@lang('Admin')</span>
                    </a>
                </li>
                @endadmin
                {{-- / Backend de administración --}}

            {{-- / Home --}}

            {{-- Perfil de usuario --}}

                <li class="app-sidebar__heading">@lang('Perfil')</li>

                {{-- Config de usuario --}}
                <li>
                    @if (request()->is('dashboard/profile'))
                        <a href="@route('dashboard.profile')" class="mm-active">
                        @else
                            <a href="@route('dashboard.profile')">
                    @endif
                    <i class="metismenu-icon pe-7s-id"></i>
                    <span class="title">
                        @lang('Perfil')
                    </span>
                    </a>
                </li>
                {{-- /Config de usuario --}}

                {{-- Notificaciones al usuario --}}
                @if ($user)
                    <li>
                        <a href="@route('dashboard.home')" class="dropdown-item">
                            <i class="metismenu-icon pe-7s-bell"></i>
                            @lang('Notificaciones')
                            <span class="ml-auto badge badge-pill badge-primary" id="menu-notification-total">
                                {{ $user->notifications->filter(fn($notification) => !$notification->read_at)->count() }}
                            </span>
                        </a>
                    </li>
                @endif
                {{-- / Notificaciones al usuario --}}

                {{-- Configuración del usuario --}}
                @auth
                    <li>
                        @if (request()->is('dashboard/config'))
                            <a href="@route('dashboard.config')" class="mm-active">
                            @else
                                <a href="@route('dashboard.config')">
                        @endif
                        <i class="metismenu-icon pe-7s-config"></i>
                        <span class="title">
                            @lang('Configuración')
                        </span>
                        </a>
                    </li>
                @endauth
                {{-- / Configuración del usuario --}}

                {{-- Subscripción del usuario --}}
                <li>
                    @if (request()->is('dashboard/subscription'))
                        <a href="@route('dashboard.subscription')" class="mm-active">
                        @else
                            <a href="@route('dashboard.subscription')">
                    @endif
                    <i class="metismenu-icon pe-7s-stopwatch"></i>
                    <span class="title">
                        @lang('Subscripción')
                    </span>
                    </a>
                </li>
                {{-- / Subscripción del usuario --}}

                {{-- Recuperación de la sesión --}}
                @guest
                    <li>
                        @if (request()->is('dashboard/profile/session'))
                            <a href="@route('dashboard.profile.session')" class="mm-active">
                            @else
                                <a href="@route('dashboard.profile.session')">
                        @endif
                        <i class="metismenu-icon pe-7s-refresh-cloud"></i>
                        <span class="title">
                            @lang('Recuperar Sesión')
                        </span>
                        </a>
                    </li>
                @endguest
                {{-- Recuperación de la sesión --}}

            {{-- / Perfil de usuario --}}

            {{-- Gestor de Archivos --}}

                <li class="app-sidebar__heading">@lang('Gestor de Archivos')</li>

                {{-- Subir achivo --}}
                <li>
                    @if (request()->is('dashboard/file/upload'))
                        <a href="@route('dashboard.file.upload')" class="mm-active">
                        @else
                            <a href="@route('dashboard.file.upload')">
                    @endif
                    <i class="metismenu-icon pe-7s-cloud-upload"></i>
                    <span class="title">
                        @lang('Subir Archivo')
                    </span>
                    </a>
                </li>
                {{-- / Subir achivo --}}

                {{-- Listado de achivos --}}
                <li>
                    @if (request()->is('dashboard/file/list'))
                        <a href="@route('dashboard.file.list')" class="mm-active">
                        @else
                            <a href="@route('dashboard.file.list')">
                    @endif
                    <i class="metismenu-icon pe-7s-copy-file"></i>
                    <span class="title">
                        @lang('Archivos')
                    </span>

                    <span class="ml-auto badge badge-pill badge-warning badge-files" id="regular-files-badge">
                        {{ $user->regular_files_count }}
                    </span>

                    </a>
                </li>
                {{-- / Listado de achivos --}}

                {{-- Achivos bloqueados --}}
                <li>
                    <a href="{{ route('dashboard.files.locked') }}"
                       class="{{ request()->is('dashboard/file/locked') ? 'mm-active' : '' }}">
                        <i class="metismenu-icon pe-7s-lock"></i>
                        <span class="title">@lang('Bloqueados')</span>
                        <span class="ml-auto badge badge-pill badge-warning" id="locked-files-badge">
                            {{ $user->locked_files_count }}
                        </span>
                    </a>
                </li>
                {{-- / Achivos bloqueados --}}

                {{-- Achivos seleccionados --}}
                <li class="hide" style="display: none;" id="selected-files-menu">
                    <a href="{{ route('dashboard.files.selected') }}" id="selected-files-url"
                       class="{{ request()->is('dashboard/file/selected') ? 'mm-active' : '' }}">
                        <i class="metismenu-icon pe-7s-check"></i>
                        <span class="title">@lang('Seleccionados')</span>
                        <span class="ml-auto badge badge-pill badge-warning" id="selected-files-badge"></span>
                    </a>
                </li>
                {{-- / Achivos seleccionados --}}

                {{-- Achivos compartidos --}}
                <li>
                    <a href="{{ route('dashboard.files.sharing') }}"
                       class="{{ request()->is('dashboard/file/sharing') ? 'mm-active' : '' }}">
                        <i class="metismenu-icon pe-7s-share"></i>
                        <span class="title">@lang('Compartidos')</span>
                        <span class="ml-auto badge badge-pill badge-info">{{ $user->fileSharings->count() }}</span>
                    </a>
                </li>
                {{-- / Achivos compartidos --}}

                {{-- Histórico de archivos --}}
                @if( \Illuminate\Support\Facades\Route::currentRouteName() === 'dashboard.files.history' )
                <li>
                    <a href="javascript:void(0)" class="mm-active">
                        <i class="metismenu-icon pe-7s-note2"></i>
                        <span class="title">@lang('Histórico')</span>
                    </a>
                </li>
                @endif
                {{-- / Histórico de archivos --}}
            
            {{-- /Gestor de Archivos --}}

            {{-- Documentos para Firma --}}
                
                <li class="app-sidebar__heading">@lang('Documentos para Firma')</li>

                {{-- Nuevo documento --}}
                <li>
                    @if (request()->is('dashboard/document/edit'))
                        <a href="@route('dashboard.document.edit')" class="mm-active">
                        @else
                            <a href="@route('dashboard.document.edit')">
                    @endif
                    <i class="metismenu-icon pe-7s-magic-wand"></i>
                    <span class="title">
                        @lang('Crear Documento')
                    </span>
                    </a>
                </li>
                {{-- / Nuevo documento --}}

                {{-- Listado de documentos para firmar --}}
                <li>
                    @if (request()->is('dashboard/document/list'))
                        <a href="@route('dashboard.document.list')" class="mm-active">
                        @else
                            <a href="@route('dashboard.document.list')">
                    @endif
                    <i class="metismenu-icon pe-7s-pen"></i>
                    <span class="title">
                        @lang('Para Firmar')
                    </span>
                    <span class="ml-auto badge badge-pill badge-info">
                        {{ $user->documents->where('purged', false)->count() }}
                    </span>

                    </a>
                </li>
                {{-- / Listado de documentos para firmar --}}

                {{-- Listado de documentos enviados para firmar --}}
                <li>
                    @if (request()->is('dashboard/document/sent'))
                        <a href="@route('dashboard.document.sent')" class="mm-active">
                        @else
                            <a href="@route('dashboard.document.sent')">
                    @endif
                    <i class="metismenu-icon pe-7s-mail"></i>
                    <span class="title">
                        @lang('Enviados')
                    </span>

                    <span class="ml-auto badge badge-pill badge-success">
                        {{ $user->documents->where('sent', true)->count() }}
                    </span>

                    </a>
                </li>
                {{-- / Listado de documentos enviados para firmar --}}

                {{-- Listado de documentos eliminados --}}
                <li>
                    @if (request()->is('dashboard/document/trash'))
                        <a href="@route('dashboard.document.removed')" class="mm-active">
                        @else
                            <a href="@route('dashboard.document.removed')">
                    @endif
                    <i class="metismenu-icon pe-7s-trash"></i>
                    <span class="title">
                        @lang('Eliminados')
                    </span>

                    <span class="ml-auto badge badge-pill badge-danger">
                        {{ $user->documents->where('purged', true)->count() }}
                    </span>

                    </a>
                </li>
                {{-- / Listado de documentos eliminados --}}

                {{-- Listado de documentos compartidos --}}
                <li>
                    <a href="@route('dashboard.list.document.sharing')" class="{{ request()->is('dashboard/document/list/sharing') ? 'mm-active' : '' }}">
                    <i class="metismenu-icon pe-7s-share"></i>
                    <span class="title">
                        @lang('Compartidos')
                    </span>

                    <span class="ml-auto badge badge-pill badge-info">
                        {{ $user->getCountDocumentSharing() }}
                    </span>

                    </a>
                </li>
                {{-- / Listado de documentos compartidos --}}

            {{-- /Documentos para Firma --}}

                {{-- Eventos - encustas, votaciones, firmas --}}
                <li class="app-sidebar__heading">@lang('Mis Eventos')</li>
                <li>
                    <a href="@route('dashboard.event.edit')" class="{{ request()->is('dashboard/event/edit') ? 'mm-active' : '' }}">
                        <i class="metismenu-icon pe-7s-back-2"></i>
                        <span class="title">@lang('Crear Evento')</span>
                    </a>
                </li>
                {{--  <li>
                    <a href="#" class="{{ request()->is('dashboard/event') ? 'mm-active' : '' }}">
                        <i class="metismenu-icon pe-7s-wallet"></i>
                        <span class="title">@lang('Tablón de Eventos')</span>
                        <span class="ml-auto badge badge-pill badge-primary"></span>
                    </a>
                </li>  --}}
                <li>
                    <a href="@route('dashboard.event.list')" class="{{ request()->is('dashboard/event/list') ? 'mm-active' : '' }}">
                        <i class="metismenu-icon pe-7s-wallet"></i>
                        <span class="title">@lang('Listado de Eventos')</span>
                        <span class="ml-auto badge badge-pill badge-primary"></span>
                    </a>
                </li>
                <li>
                    <a href="@route('dashboard.event.list.templatesAndDraft')" class="{{ request()->is('dashboard/event/list/templatesdraft') ? 'mm-active' : '' }}">
                        <i class="metismenu-icon pe-7s-back-2"></i>
                        <span class="title">@lang('Plantillas y Borradores')</span>
                    </a>
                </li>
                {{-- /Eventos - encustas, votaciones, firmas --}}

                {{-- Solicitudes de Documentos --}}
                <li class="app-sidebar__heading">@lang('Solicitudes de Documentos')</li>
                {{-- Nueva solicitud --}}
                <li>
                    @if (request()->is('dashboard/document/request/edit'))
                        <a href="@route('dashboard.document.request.edit')" class="mm-active">
                        @else
                            <a href="@route('dashboard.document.request.edit')">
                    @endif
                    <i class="metismenu-icon pe-7s-back-2"></i>
                    <span class="title">
                        @lang('Crear Solicitud')
                    </span>
                    </a>
                </li>
                {{-- / Nueva solicitud --}}

                {{-- Listado de Solicitudes --}}
                <li>
                    @if (request()->is('dashboard/document/request/list'))
                        <a href="@route('dashboard.document.request.list')" class="mm-active">
                        @else
                            <a href="@route('dashboard.document.request.list')">
                    @endif
                    <i class="metismenu-icon pe-7s-wallet"></i>
                    <span class="title">
                        @lang('Solicitudes')
                    </span>
                    <span class="ml-auto badge badge-pill badge-primary">
                        {{ $user->findDocumentRequests()->count() }}
                    </span>
                    </a>
                </li>
                {{-- / Listado de Solicitudes --}}

            {{-- /Solicitudes de Documentos --}}

            {{-- Certificación de datos --}}

                <li class="app-sidebar__heading">@lang('Certificación de datos')</li>

                {{-- Nueva Certificación de datos --}}
                <li>
                    <a href="@route('dashboard.verificationform.edit')" class="{{ request()->is('dashboard/verificationform/edit') ? 'mm-active' : '' }}">
                    <i class="metismenu-icon pe-7s-note"></i>
                    <span class="title">
                        @lang('Crear Verificación')
                    </span>
                    </a>
                </li>
                {{-- / Nueva Certificación de datos --}}

                {{-- Listado de Certificaciones de datos --}}
                <li>
                    <a href="@route('dashboard.verificationform.list')" class="{{ request()->is('dashboard/verificationform/list') ? 'mm-active' : '' }}">
                    <i class="metismenu-icon pe-7s-note2"></i>
                    <span class="title">
                        @lang('Verificaciónes')
                    </span>
                    <span class="ml-auto badge badge-pill badge-success">
                        {{ $user->verificationForm()->count() }}
                    </span>
                    </a>
                </li>
                {{-- / Listado de Certificaciones de datos --}}
            
            {{-- /Certificación de datos --}}

            {{-- Grabaciones de pantalla--}}

                <li class="app-sidebar__heading">@lang('Grabaciones de pantalla')</li>

                {{-- Nueva Grabación de pantalla--}}
                <li>
                    {{-- Nueva grabación de pantalla--}}
                    <a href="@route('dashboard.screen.edit')"
                        class="{{ request()->is('dashboard/screen/edit') ? 'mm-active' : '' }}"
                    >
                        <i class="metismenu-icon pe-7s-monitor"></i>
                        <span class="title">
                            @lang('Crear grabación')
                        </span>
                    </a>
                </li>
                {{-- / Nueva grabación de pantalla--}}

                {{-- Listado de grabaciones de pantalla--}}
                <li>
                    <a href="@route('dashboard.screen.list')"
                        class="{{ request()->is('dashboard/screen/list') ? 'mm-active' : '' }}"
                    >
                        <i class="metismenu-icon pe-7s-display2"></i>
                        <span class="title">
                            @lang('Grabaciones')
                        </span>
                        <span class="ml-auto badge badge-pill badge-dark">
                            {{ $user->screens->count() }}
                        </span>
                    </a>
                </li>
                {{-- / Listado de grabaciones de pantalla--}}
            
            {{-- / Grabaciones de pantalla--}}

            {{-- Contactos --}}

                <li class="app-sidebar__heading">@lang('Contactos')</li>
                
                {{-- Nuevo Contacto --}}
                <li>
                    @if (request()->is('dashboard/contact/edit'))
                        <a href="@route('dashboard.contact.edit')" class="mm-active">
                        @else
                            <a href="@route('dashboard.contact.edit')">
                    @endif
                    <i class="metismenu-icon pe-7s-add-user"></i>
                    <span class="title">
                        @lang('Crear Contacto')
                    </span>
                    </a>
                </li>
                {{-- / Nuevo Contacto --}}

                {{-- Listado de Contactos --}}
                <li>
                    @if (request()->is('dashboard/contact/list'))
                        <a href="@route('dashboard.contact.list')" class="mm-active">
                        @else
                            <a href="@route('dashboard.contact.list')">
                    @endif
                    <i class="metismenu-icon pe-7s-users"></i>
                    <span class="title">
                        @lang('Contactos')
                    </span>

                    <span class="ml-auto badge badge-pill badge-success badge-contacts">
                        {{ $user->contacts->count() }}
                    </span>
                    </a>
                </li>
                {{-- / Listado de Contactos --}}
            
            {{-- /Contactos --}}

            {{-- Autenticación --}}

                <li class="app-sidebar__heading">@lang('Sesión')</li>

                <li>
                    {{-- Salir --}}
                    @auth
                        <div tabindex="-1" class="dropdown-divider"></div>
                        <a href="@route('dashboard.logout')" class="dropdown-item">
                            <i class="metismenu-icon pe-7s-user"></i>
                            @lang('Cerrar Sesión')
                        </a>
                    @endauth
                    {{-- / Salir --}}

                    @guest
                        {{-- Iniciar --}}
                        <a href="@route('dashboard.login')" class="dropdown-item">
                            <i class="metismenu-icon pe-7s-user"></i>
                            @lang('Iniciar Sesión')
                        </a>
                        {{--  Iniciar --}}

                        {{-- Registrarse --}}
                        <a href="@route('dashboard.register')" class="dropdown-item">
                            <i class="metismenu-icon pe-7s-add-user"></i>
                            @lang('Registrarse')
                        </a>
                        {{-- / Registrarse --}}
                    @endguest
                </li>
            
            {{-- /Autenticación --}}

            </ul>
        </div>
    </div>
</div>
</div>
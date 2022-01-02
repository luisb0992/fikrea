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

    @if (!$signer->verificationForm)
        {{-- Modales necesarias --}}
        @include('workspace.modals.cancel-process')
        {{-- /Modales necesarias --}}
    @endif

    @if ($signer->process->isPending())
        {{--  Modal para Enviar comentario  --}}
        @include('workspace.modals.send-comment-modal')
        {{--  /Modal para Enviar comentario  --}}
    @endif

    @if (request()->is("workspace/{$token}"))
        {{--  Modal de confirmacion para salir del workspace  --}}
        @include('workspace.modals.exit-workspace-modal')
        {{--  /Modal de confirmacion para salir del workspace  --}}
    @endif

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
                    <a href="@route('workspace.home', ['token' => $token])">
                        <i class="metismenu-icon pe-7s-home"></i>
                        @lang('Inicio')
                    </a>
                </li>

                {{-- Personas intervinientes, involucradas en el proceso
                    cuando se trata de una firma y validación de un documento --}}

                @if ($signer->document)

                    <li class="app-sidebar__heading">@lang('Personas Intervinientes')</li>

                    @foreach ($signer->document->signers as $_signer)
                        <li>
                            @if ($_signer->email)
                                <a href="mailto:{{ $_signer->email }}">
                                    <i class="metismenu-icon pe-7s-user"></i>
                                    @if ($_signer->name || $_signer->lastname)
                                        <span>
                                            {{ $_signer->name }} {{ $_signer->lastname }}
                                        </span>
                                    @else
                                        <span>
                                            {{ $_signer->email }}
                                        </span>
                                    @endif
                                </a>
                            @elseif ($_signer->phone)
                                <a href="tel:{{ $_signer->phone }}">
                                    <i class="metismenu-icon pe-7s-user"></i>
                                    @if ($_signer->name || $_signer->lastname)
                                        {{ $_signer->name }} {{ $_signer->lastname }}
                                    @else
                                        <span>
                                            {{ $_signer->phone }}
                                        </span>
                                    @endif
                                </a>
                            @endif
                        </li>
                    @endforeach
                @endif

                {{-- Documentos

                    Sólo si se ha enviado un documento al usuario firmante se muestra
                    la opción para descargar ese documento --}}

                @if ($signer->document)

                    <li class="app-sidebar__heading">@lang('Documentos')</li>

                    <li>
                        <a href="@route('workspace.document.download', ['token' => $token])" data-toggle="tooltip"
                            data-placement="top" data-original-title="sha1: {{ $signer->document->original_sha1 }}">
                            <i class="metismenu-icon pe-7s-cloud-download"></i>
                            @lang('Descargar Original')
                        </a>
                    </li>

                    {{-- Sólo se ofrece la posibilidad de descargar el documento firmado
                         si ha sido completado por todos y cada uno de los firmantes
                         y no está siendo procesado en este momento --}}
                    @if ($signer->document->hasBeenSigned())
                        @if ($signer->document->isInProcess())
                            <li>
                                <a href="#!" class="disabled" data-toggle="tooltip" data-placement="top"
                                    data-original-title="@lang('El documento está siendo procesado')">
                                    <i class="metismenu-icon pe-7s-clock"></i>
                                    @lang('Generando Firmado')
                                </a>
                            </li>
                        @else
                            <li>
                                <a href="@route('workspace.document.download.signed', ['token' => $token])"
                                    data-toggle="tooltip" data-placement="top"
                                    data-original-title="sha1: {{ $signer->document->signed_sha1 }}">
                                    <i class="metismenu-icon pe-7s-pen"></i>
                                    @lang('Descargar Firmado')
                                </a>
                            </li>
                        @endif
                    @endif

                @endif

                {{-- Archivos Aportados al proceso de validación

                    Sólo si el firmante ha aportado algún o algunos archivos
                    en el proceso de validación, se ofrece la posibilidad de descargarlos --}}

                @if ($signer->document)

                    @if ($signer->audios->isNotEmpty() || $signer->videos->isNotEmpty() || $signer->passports->isNotEmpty() || $signer->captures->isNotEmpty())
                        <li class="app-sidebar__heading">@lang('Archivos Aportados')</li>

                        <li>
                            <a href="@route('workspace.document.download.files', ['token' => $token])">
                                <i class="metismenu-icon pe-7s-cloud-upload"></i>
                                @lang('Descargar')
                            </a>
                        </li>
                    @endif

                @endif

                {{-- Archivos aportados en el proceso de solicitud de documentos --}}
                @if ($signer->request())

                    @if ($signer->request()->files->isNotEmpty())
                        <li class="app-sidebar__heading">@lang('Archivos Aportados')</li>

                        <li>
                            <a href="@route('workspace.document.download.files', ['token' => $token])">
                                <i class="metismenu-icon pe-7s-cloud-upload"></i>
                                @lang('Descargar')
                            </a>
                        </li>
                    @endif

                @endif

                {{-- Si existe una verificación de datos y fue aprobada --}}
                @if ($signer->verificationForm)
                    @if ($signer->verificationFormIsDone())
                        <li class="app-sidebar__heading">@lang('verificación de datos')</li>
                        <li>
                            <a href="@route('workspace.verificationform.certificate', ['token' => $token])">
                                <i class="metismenu-icon pe-7s-cloud-upload"></i>
                                @lang('Descargar')
                            </a>
                        </li>
                    @endif
                @endif

                {{-- Emisión del Justificante de participación en el proceso
                    si el usuario firmante ha completado todas las validaciones que se le han propuesto --}}

                @if ($signer->document && $signer->hasDoneAllValidations())
                    <li class="app-sidebar__heading">@lang('Informe Acreditativo')</li>

                    <li>
                        <a href="@route('workspace.signer.report.get', ['token' => $token])" data-toggle="tooltip"
                            data-placement="top" data-original-title="@lang('Informe acreditativo del proceso')">
                            <i class="metismenu-icon pe-7s-shield"></i>
                            @lang('Obtener')
                        </a>
                    </li>
                @endif

                {{--  link para mostrar modal de enviar comentarios  --}}
                @if (!$signer->document  && $signer->request() && $signer->process->isPending())
                    <li class="app-sidebar__heading">@lang('Comentarios')</li>
                    <li>
                        <a href="#" @click.prevent="showSendCommentModal">
                            <i class="metismenu-icon pe-7s-comment"></i>
                            @lang('Enviar comentario')
                        </a>
                    </li>
                @endif

                {{--  cancelar el proceso actual  --}}
                @if (request()->is("workspace/{$token}") && !$signer->verificationForm)
                    @if ($canCancelProcess)
                        <li class="app-sidebar__heading">@lang('Cancelación del Proceso')</li>
                        <li>
                            <a href="#" @click.prevent="showCancelProcessModal">
                                <i class="metismenu-icon pe-7s-hammer"></i>
                                @lang('Cancelar')
                            </a>
                        </li>
                    @endif
                @endif

                {{--  Salir del workspace  --}}
                @if (request()->is("workspace/{$token}"))
                    <hr>
                    <li class="app-sidebar__heading text-danger">@lang('Salir del espacio de trabajo')</li>
                    <li>
                        <a href="#" @click.prevent="$bvModal.show('exit-workspace-modal')" class="text-danger border border-danger">
                            <i class="metismenu-icon pe-7s-close"></i>
                            @lang('Salir')
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>

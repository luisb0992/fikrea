{{-- Si es una verificación de datos --}}

{{-- Modales necesarias --}}
@include('workspace.modals.cancel-verificationform')
{{-- /Modales necesarias --}}

<div class="container-md mb-5">

    {{-- Mensaje informativo del proceso --}}
    @if (!$signer->process->isCanceled())
        <div class="ml-5 ml-md-0 mt-5">
            <div class="alert alert-info p-4 text-justify" role="alert">
                <h4 class="alert-heading"><i class="fas fa-info-circle"></i> @lang('IMPORTANTE')</h4>
                <p>
                    @lang('En este proceso le solicitamos que revise la información que ha sido introducida por él
                    solicitante
                    en cada campo del formulario y en caso de ausencia de texto, complemente usted la información que le
                    solicita')
                </p>
                <hr>
                <p class="mb-0">
                    @lang('Pulse el botón')
                    <span class="text-success font-weight-bold">@lang('ACCEDE PARA REALIZAR')</span>
                    @lang('para ver más detalles')
                </p>
            </div>
        </div>
    @endif

    {{-- Cuerpo de la tabla e informacion relevante --}}
    <div class="card ml-5 ml-md-0 mx-md-auto p-3 justify-content-center mt-2">
        <div class="card-title d-flex align-items-center border-bottom">
            <div class="flex-grow-1 mb-3">
                <h5>
                    @if ($signer->process->isDone())
                        <i class="far check fa-check-square fa-2x text-success"></i>
                        <span class="text text-success">
                        @elseif ($signer->process->isCanceled())
                            <i class="far check fa-window-close fa-2x text-danger"></i>
                            <span class="text text-danger">
                            @else
                                <i class="far check fa-square fa-2x"></i>
                                <span class="text">
                    @endif

                    @lang('Verificación de datos')
                    </span>
                </h5>
            </div>
            <div class="mb-3">
                {{-- El status de la verificación para el firmante --}}
                <a href="#" class="btn btn-{{ $signer->process->workspaceStatus->getColor() }} disabled"
                    style="font-size: .70rem; float: right;">
                    {{ (string) \App\Enums\WorkspaceStatu::fromValue($signer->process->workspace_statu_id) }}
                </a>
                {{-- /El status de la verificación para el firmante --}}
            </div>
        </div>

        {{-- informacion relevante de la verificación de datos --}}
        <div class="card-body">
            <div class="text-secondary small text-justify">
                <p>@lang('Detalles de la verificación de datos')</p>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <tbody>
                            <tr>
                                <td class="bold">@lang('Nombre'):</td>
                                <td>{{ $signer->verificationForm->formatname }}</td>
                            </tr>
                            <tr>
                                <td class="bold">@lang('Tipo de formulario'):</td>
                                <td>{{ $signer->verificationForm->fieldsRow()->first()->formattype }}</td>
                            </tr>
                            <tr>
                                <td class="bold">@lang('Comentarios'):</td>
                                <td>{{ $signer->verificationForm->formatcomment }}</td>
                            </tr>
                            <tr>
                                <td class="bold">@lang('Cantidad de campos del formulario a revisar'):</td>
                                <td>
                                    @if ($signer->process->isDone())
                                        <i class="fas fa-check text-success"></i>
                                    @endif

                                    <button class="btn btn-outline-light">
                                        <span
                                            class="{{ $signer->process->isCanceled() ? 'text-danger' : ($signer->process->isDone() ? 'text-success' : 'text-dark') }}">
                                            @if ($signer->verificationForm->fieldsRow()->count() > 1)
                                                @lang('Campos'):
                                            @else
                                                @lang('Campo'):
                                            @endif
                                        </span>
                                        <span
                                            class="badge badge-light {{ $signer->process->isCanceled() ? 'text-danger' : ($signer->process->isDone() ? 'text-success' : 'text-dark') }}">
                                            {{ $signer->verificationForm->fieldsRow()->count() }}
                                        </span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <hr>

        <div class="d-flex justify-content-end">
            {{-- Se verifica si el usuario firmante ha realizado la verificación --}}
            @if ($signer->process->isPending())

                <a href="@route('workspace.verificationform.form', ['token' => $token])" class="btn btn-success mr-2">
                    @lang('ACCEDE PARA REALIZAR')
                </a>

                <a href="#" @click.prevent="showCancelVerificationFormModal" class="btn btn-danger">
                    @lang('Rechazar')
                </a>

            @elseif ($signer->process->isDone())

                {{-- Listado de documentos que he aportado --}}
                @lang('Verificación realizada el ') @datetime($signer->verificationform_at). @lang('Gracias por
                atenderla')!
                {{-- /Listado de documentos que he aportado --}}

            @elseif ($signer->process->isCanceled())

                <span class="text-danger">
                    @lang('Ha cancelado la verificación de datos').
                </span>

            @endif
        </div>

        {{--  si la certificacion de datos posee comentarios  --}}
        @if ($signer->verificationForm->feedback)
            <div class="mt-4">
                <div class="alert alert-primary text-justify" role="alert">
                    <div class="alert-heading mb-2 border-bottom border-primary font-weight-bold">
                        <i class="fas fa-comment"></i> @lang('Comentario')
                    </div>
                    <p>
                        <small class="font-italic">"{{ $signer->verificationForm->feedback->comment }}"</small>
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>

{{--
    Si es una solicitud de documentos se muestran los detalles de la misma
--}}

{{-- Modales necesarias --}}
@include('workspace.modals.cancel-request')
{{--/Modales necesarias --}}

<div class="container-md my-5 p-4 bg-white ml-4 ml-md-auto">

    {{--  detalles de la solicitud de documentos  --}}
    <li class="list-group-item-request">

        @if ($signer->process->isDone())
        <i class="far check fa-check-square fa-2x text-success"></i>
        @elseif ($signer->process->isCanceled())
        <i class="far check fa-window-close fa-2x text-danger"></i>
        @else
        <i class="far check fa-square fa-2x"></i>
        @endif

        @if ($signer->process->isDone())
        <span class="text text-success">
        @elseif ($signer->process->isCanceled())
        <span class="text text-danger">
        @else
        <span class="text">
        @endif

        @if ($signer->request()->expiringDocuments()->count())
        @lang('Renovación de Documentos')
        @else
        @lang('Documentación Requerida')
        @endif

        {{-- El status de la solicitud para el firmante --}}
        <a href="#" class="btn btn-{{$signer->process->workspaceStatus->getColor()}} disabled"
            style="font-size: .70rem; float: right;"
        >
            {{ (string) \App\Enums\WorkspaceStatu::fromValue($signer->process->workspace_statu_id) }}
        </a><br/>
        {{-- /El status de la solicitud para el firmante --}}

        </span>

        <div class="text-right">

            {{-- Detalles de la solicitud de documentos --}}
            @include('workspace.home.request-details',
                [
                    'request'	=>	$signer->request()
                ]
            )
            {{-- /Detalles de la solicitud de documentos --}}

            {{-- Se verifica si el firmante ha realizado la aportación de documentos --}}
            @if ($signer->process->isPending())
                
                <a href="@route('workspace.document.request', ['token' => $token])" class="btn btn-success">
                    @lang('ACCEDE PARA REALIZAR')
                </a>
                
                <a href="#" @click.prevent="showCancelRequestModal('document')" class="btn btn-danger">
                    @lang('Rechazar')
                </a>

            @else
                @if ($signer->process->isDone())
                {{-- Listado de documentos que he aportado --}}
                @include('workspace.home.contributed-files',
                    [
                        'request'   =>  $signer->request()
                    ]
                )
                @endif
                {{-- /Listado de documentos que he aportado --}}
            @endif
            {{-- /Se verifica si el firmante ha realizado la aportacion de documentos --}}

            {{-- Se muestra cuando la solicitud ya ha sido atendida y tengo documentos al expirar --}}
            @if (
                $signer->requestIsDone()
                &&
                $signer->request()->expiringDocuments()->count()
            )
            <a class="btn btn-warning square"
                href="@route('workspace.document.request.renew', ['token'=>$token])">
                @lang('Renovar documentos')
            </a>
            @endif
            {{-- /Se muestra cuando la solicitud ya ha sido atendida y tengo documentos al expirar --}}

        </div>
    </li>
    {{--  /detalles de la solicitud de documentos  --}}

    <hr>

    {{-- Los comentarios opcionales al documento --}}
    <div class="d-flex">
        <div class="card w-100">
            <div class="card-body">
                <h3>
                    <i class="fa fa-comments"></i> @lang('Lista de comentarios')
                    @if ($signer->process->isDone())
                        <small class="text-success"> - @lang('Proceso completado')</small>
                    @else
                        <small class="text-muted"> - @lang('Envia comentarios al solicitante')</small>
                    @endif
                </h3>
                <hr>
                @foreach($comments as $comment)
                <div class="border border-light p-2 mb-2">
                    <h5>
                        {{$comment->signer->fullname}} - <small class="text-muted">@datetime($comment->created_at)</small>
                    </h5>
                    <blockquote class="blockquote ml-4">
                        <p class="text-muted">
                            <i class="fa fa-comment-alt text-info"></i>
                            {{$comment->comment}}
                        </p>
                    </blockquote>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    {{--/Los comentarios opcionales al documento --}}

</div>
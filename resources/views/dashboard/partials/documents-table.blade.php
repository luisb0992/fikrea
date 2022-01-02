{{-- Visualiza una tabla que contiene una lista de documentos

    @param Document[] $document                 Una lista de documentos a mostrar
    @param bool   $selection                    true si la primera columna de la tabla es para selección
                                                false para un valor autonumérico --}}

{{--  Modal para mover un documento a mis archivos  --}}
@include('dashboard.modals.documents.move-document-to-files')
{{--  /Modal para mover un documento a mis archivos  --}}

<div class="table-responsive col-md-12">
    @include('dashboard.partials.button-selection')
    <table class="mb-0 table table-striped">
        <thead>
            <tr>
                @if (isset($selection))
                    <th class="checkbox-centered">
                        <label class="check-container align-middle">
                            <input class="form-control" type="checkbox" @change.prevent="selectAll" id="inputCheckAll"/>
                            <span class="square-check"></span>
                        </label>
                    </th>
                @else
                    <th>#</th>
                @endif
                <th>@lang('Nombre')</th>
                <th>@lang('Tipo')</th>
                <th class="text-center">@lang('Tamaño')</th>
                <th class="text-center">@lang('Páginas')</th>
                <th class="text-center">@lang('Progreso')</th>
                <th class="text-center">@lang('Creado')</th>
                @if (request()->is('dashboard/document/trash'))
                    <th class="text-center">@lang('Eliminado')</th>
                @else
                    <th class="text-center">@lang('Enviado')</th>
                @endif

                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($documents as $document)
                <tr>
                    @if (isset($selection))
                        <td class="checkbox-centered" data-label="@lang('Selección')">
                            <label class="check-container">
                                <input @change.prevent="select" name="documents[]" value="{{ $document->id }}"
                                    v-model="documents" class="form-control document" type="checkbox"
                                    data-copy="{{ ($document->sent && !$document->copy_at) ? 'yescopy' : 'nocopy' }}"
                                    data-share="{{ ($document->sent) ? 'yesshare' : 'noshare' }}"
                                    data-name="{{ $document->name }}"/>
                                <span class="square-check"></span>
                            </label>
                        </td>
                    @else
                        <th scope="row">
                            {{ $loop->iteration }}
                        </th>
                    @endif

                    <td data-label="@lang('Nombre')">
                        <a
                            href="@route('dashboard.document.download',['id' => $document->id])">{{ $document->name }}</a>
                        <div class="small text-secondary">{{ $document->guid }}</div>
                    </td>

                    <td data-label="@lang('Tipo')">
                        @include('dashboard.partials.file-icon', ['type' => $document->type])
                    </td>

                    <td class="text-center" data-label="@lang('Tamaño')">@filesize($document->size)</td>
                    <td class="text-center" data-label="@lang('Páginas')">{{ $document->pages }}</td>


                    <td class="text-center" data-label="@lang('Progreso')">

                        {{-- Progreso de las validaciones, animado cuando se está atendiendo una validación --}}
                        @if ($document->isActive())
                            <div class="progress" style="height: 10px;" data-toggle="tooltip" data-placement="top"
                                data-html="true" data-original-title="{{ $document->getActivity() }}">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-info w-100"
                                    role="progressbar"></div>
                            </div>
                            <div class="animated infinite pulse text-secondary">
                                {!! $document->getActivity() !!}
                            </div>
                        @else
                            <progress min="0" max="100" value="{{ $document->progress }}"></progress>
                            <div class="text-center bold">{{ $document->progress }} %</div>
                        @endif
                        {{-- /Progreso de las validaciones, animado cuando se está atendiendo una validación --}}

                    </td>

                    <td class="text-nowrap" data-label="@lang('Creado')">
                        @date($document->created_at)
                        <div class="text-info">
                            @time($document->created_at)
                        </div>
                        {{-- {{ $document->signers->filter(fn($signer) => !$signer->creator)->map(fn($signer) => $signer->token) }} --}}
                    </td>

                    {{-- Si el documento está eliminado se muestra la fecha de eliminación --}}
                    @if (request()->is('dashboard/document/trash'))
                        <td class="text-center text-danger" data-label="@lang('Eliminado')">
                            @datetime($document->deleted_at)</td>
                    @else
                        {{-- En caso contrario, se muestra si ha sido enviado o no y la fecha --}}
                        @if ($document->sent_at)
                            <td class="text-nowrap" data-label="@lang('Enviado')">
                                @date($document->sent_at)
                                <div class="text-info">
                                    @time($document->sent_at)
                                </div>
                            </td>
                        @else
                            <td class="text-center" data-label="@lang('Enviado')">
                                <i class="fa fa-times fa-2x text-danger"></i>
                            </td>
                        @endif
                    @endif

                    <td class="text-right">
                        <div class="btn-group-vertical" role="group">
                            <div class="btn-group" role="group">
                            {{-- Si el documento no está eliminado --}}
                            {{--
                                Si el documento ha sido ya enviado a los firmantes, no puede ser:

                                a) Eliminado.
                                b) Reconfigurado: Añadir o quitar firmantes, cambiar las validaciones establidas
                                   o las firmas.
                                c) Editar su nombre o comentario

                                Todo ello para preservar la integridad del documento y de su proceso
                            --}}

                            {{--
                                Eliminar el documento

                                Está desactivado si el documento ya ha sido enviado a los firmantes
                                También está desactivado en la vista de consulta del estado del documento

                                Si el documento ha sido procesado por los firmantes (progreso del documento 100%),
                                puede ser eliminado, lo que permite eliminar documentos antiguos ya procesados
                            --}}
                            @if (!$document->purged)
                                @if (($document->sent && $document->progress < 100) || request()->is('dashboard/document/status/*'))
                                    <div data-toggle="tooltip" data-placement="top" data-original-title="@lang('Eliminar Documento')">
                                        <a href="#" class="btn btn-danger square disabled">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                @else
                                    <div>
                                        <a @click.prevent="showAlertBeforeRemoveDocument({{ $document->id }})"
                                            href="#" class="btn btn-danger square" data-toggle="tooltip"
                                            data-placement="top" data-original-title="@lang('Eliminar Documento')">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                @endif

                                {{-- Configuración de la firma del documento
                                 Está desactivada si el documento ya ha sido enviado a los firmantes --}}
                                @if ($document->sent)
                                    <div data-toggle="tooltip" data-placement="top" data-original-title="@lang('Configurar Proceso de Firma')">
                                        <a href="#" class="btn btn-info square disabled">
                                            <i class="fas fa-signature"></i>
                                        </a>
                                    </div>
                                    <div data-toggle="tooltip" data-placement="top" data-original-title="@lang('Editar Documento')">
                                        <button class="btn btn-warning square text-white" disabled>
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </div>
                                @else
                                    <div>
                                        <a href="@route('dashboard.document.signers', ['id' => $document->id])"
                                            class="btn btn-info square" data-toggle="tooltip" data-placement="top"
                                            data-original-title="@lang('Configurar Proceso de Firma')">
                                            <i class="fas fa-signature"></i>
                                        </a>
                                    </div>
                                    <div>
                                        <a href="@route('dashboard.document.edit',  ['id' => $document->id])"
                                            class="btn btn-warning square text-white" data-toggle="tooltip" data-placement="top"
                                            data-original-title="@lang('Editar Documento')">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </div>
                                @endif

                                {{-- Muestra el estado de validación del documento --}}
                                @if ($document->validations->isNotEmpty())
                                    <div>
                                        <a href="@route('dashboard.document.status', ['id' => $document->id])"
                                            class="btn btn-primary square" data-toggle="tooltip" data-placement="top"
                                            data-original-title="@lang('Ver Estado de Validación')">
                                            <i class="fas fa-thermometer-half"></i>
                                        </a>
                                    </div>
                                @else
                                    <div data-toggle="tooltip" data-placement="top" data-original-title="@lang('Ver Estado de Validación')">
                                        <button class="btn btn-primary square" disabled>
                                            <i class="fas fa-thermometer-half"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div class="btn-group" role="group">
                                {{-- Descarga el documento en su estado más actualizado de firmado --}}
                                @if ($document->isInProcess())
                                    {{-- Si el documento se está procesando en este momento (para generar el documento firmado --}}
                                    <div>
                                        <span class="btn btn-secondary square" data-toggle="tooltip" data-placement="top"
                                            data-original-title="@lang('El documento se está procesando...')">
                                            <i class="fas fa-hourglass-half"></i>
                                        </span>
                                    </div>
                                @else
                                    {{-- El documento está listo para su descarga --}}
                                    <div>
                                        <a href="@route('dashboard.document.signed.get', ['id' => $document->id])"
                                            class="btn btn-secondary square" data-toggle="tooltip" data-placement="top"
                                            data-original-title="@lang('Descargar el Documento')">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                @endif

                                {{-- compartir un documento con otros usuarios --}}
                                @if ($document->sent)
                                    <div>
                                        <form action="@route('dashboard.document.share')" method="post">
                                            @csrf
                                            <input type="hidden" name="dataSharing" value="{{ $document->id }}">
                                            <button type="submit" class="btn btn-success square" data-toggle="tooltip"
                                                data-placement="top"
                                                data-original-title="@lang('Compartir el documento')">
                                                <i class="fas fa-share-alt text-white"></i>
                                            </button>
                                        </form>
                                    </div>

                                    @if ($document->copy_at)
                                        <div>
                                            <span data-toggle="tooltip" title="@lang('Almacenar el documento')">
                                                <button class="btn btn-action-move square" disabled>
                                                    <i class="far fa-save text-white"></i>
                                                </button>
                                            </span>
                                        </div>
                                    @else
                                        <div>
                                            <span data-toggle="tooltip" title="@lang('Almacenar el documento')">
                                                <b-button v-b-modal.move-document-to-files-modal variant="btn-action-move square"
                                                    class="btn-action-move square"
                                                    @if (request()->is('dashboard/document/list'))
                                                    @click="loadDocumentSelected"
                                                    @else
                                                    @click="onlyLoadDocumentSelected"
                                                    @endif
                                                    data-id="{{ $document->id }}"
                                                    data-name="{{ $document->name }}">
                                                    <i class="far fa-save text-white"></i>
                                                </b-button>
                                            </span>
                                        </div>
                                    @endif
                                @else
                                    <div>
                                        <form>
                                            <button class="btn btn-success square" data-toggle="tooltip" data-placement="top"
                                                data-original-title="@lang('Compartir el documento')" disabled>
                                                <i class="fas fa-share-alt text-white"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <div>
                                        <span data-toggle="tooltip" title="@lang('Almacenar el documento')">
                                            <button class="btn btn-action-move square" disabled>
                                                <i class="far fa-save text-white"></i>
                                            </button>
                                        </span>
                                    </div>
                                @endif

                                {{-- Histórico de visitas de los firmantes para realizar las validaciones correspondientes --}}
                                @if ($document->visits->isEmpty())
                                    <div>
                                        <a href="#!" class="btn btn-action-copy-url square" data-toggle="tooltip"
                                            data-placement="top"
                                            data-original-title="@lang('Ninguna visita registrada')">
                                            <i class="fas fa-eye-slash"></i>
                                        </a>
                                    </div>
                                @else
                                    <div>
                                        <a href="@route('dashboard.document.history', [$document->id])"
                                            class="btn btn-action-copy-url square" data-toggle="tooltip" data-placement="top"
                                            data-original-title="@lang('Histórico de Firmantes')">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                @endif

                                {{-- Si el documento está eliminado --}}
                            @else
                                <div>
                                    <a @click.prevent="showAlertBeforeDestroyDocument({{ $document->id }})" href="#"
                                        class="btn btn-danger square" data-toggle="tooltip" data-placement="top"
                                        data-original-title="@lang('Eliminar definitivamente el Documento')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                                <div>
                                    <a href="@route('dashboard.document.recover',['id' => $document->id])"
                                        class="btn btn-success square" data-toggle="tooltip" data-placement="top"
                                        data-original-title="@lang('Recuperar el Documento')">
                                        <i class="fas fa-trash-restore-alt"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-danger bold">
                        @lang('Ningún documento registrado')
                    </td>
                </tr>
            @endforelse
        </tbody>

    </table>
    @include('dashboard.partials.button-selection')
</div>

{{--  formulario de envio para compartir varios documentos  --}}
<form action="@route('dashboard.document.share')" method="post" id="formSharing" class="d-none">
    @csrf
    <input type="hidden" name="dataSharing" id="inputDataSharing">
</form>
{{--  /formulario de envio para compartir varios documentos  --}}
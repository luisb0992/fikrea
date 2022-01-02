{{-- Selector de botones de accion para interactuar en al lista de documentos
    eliminar, limpiar y compartir documentos --}}
@if (isset($selection))
    <div class="text-right my-4">

        <span data-toggle="tooltip" data-placement="top" data-original-title="@lang('Eliminar la selección')">
            @if (request()->is('dashboard/document/list'))
                <button @click.prevent="showAlertBeforeRemoveDocument()" class="btn btn-danger mt-1"
                    :disabled="!documents.length">
                    @lang('Eliminar')
                </button>
            @endif
            @if (request()->is('dashboard/document/trash'))
                <button @click.prevent="showAlertBeforeDestroyDocument()" class="btn btn-danger mt-1"
                    :disabled="!documents.length">
                    @lang('Eliminar')
                </button>
            @endif
        </span>

        <span data-toggle="tooltip" data-placement="top" data-original-title="@lang('Limpiar la selección')">
            <button @click.prevent="clearFiles" :disabled="!documents.length" class="btn btn-warning mt-1">
                <i class="fas fa-broom"></i>
                @lang('Limpiar')
            </button>
        </span>

        {{--  solo visible en la lista de documentos  --}}
        @if (request()->is('dashboard/document/list'))
            <span data-toggle="tooltip" data-placement="top" data-original-title="@lang('Compartir la selección')">
                <button @click.prevent="shareFiles" :disabled="!documents.length" class="btn btn-success mt-1"
                    data-url-sharing="@route('dashboard.document.share')"
                    data-message-document-empty="@lang('Debe seleccionar documentos válidos para compartir')">
                    <i class="fas fa-share-alt"></i>
                    @lang('Compartir')
                </button>
            </span>

            <span data-toggle="tooltip" data-placement="top"
                data-original-title="@lang('Guardar en MIS ARCHIVOS la selección')">
                @if ($documents->filter(fn($document) => !$document->copy_at)->count())
                    <b-button v-b-modal.move-document-to-files-modal
                        class="light btn-action-move square mt-1 text-white" @click="loadDocumentSelected"
                        :disabled="!documents.length" data-multiple="true">
                        <i class="far fa-save"></i>
                        @lang('Guardar')
                    </b-button>
                @else
                    <button class="btn btn-action-move square mt-1 text-white" disabled>
                        <i class="far fa-save"></i>
                        @lang('Guardar')
                    </button>
                @endif
            </span>
        @endif
    </div>

@endif

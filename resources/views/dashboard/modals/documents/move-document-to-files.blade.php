<template>
    <b-modal id="move-document-to-files-modal" header-bg-variant="primary">

        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <h5 class="modal-title text-white">
                <i class="far fa-save"></i> <span>@lang('Guardar documento')</span>
                <p class="text-white font-weight-light">
                    <small>@lang('Almacena tus documentos en <b>MIS ARCHIVOS</b>')</small>
                </p>
            </h5>
        </template>

        {{-- El contenido de la modal --}}
        <div class="mt-2">

            @if (request()->is('dashboard/document/list'))
                <div class="form-group" v-if="(multipleSelection && copyDocuments.length) || nameDocument">
                    <div class="alert alert-primary alert-dismissible fade show" role="alert">
                        <h6 class="font-weight-light">
                            <i class="fas fa-info-circle"></i>
                            @lang('Tenga en cuenta que si no es un documento admitido para ser firmado este se obviara y
                            solo se guardara el documento original en <span class="font-weight-bold">MIS ARCHIVOS</span>.')
                        </h6>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            @endif

            <div class="form-group">

                @if (request()->is('dashboard/document/list'))
                    {{-- si es una seleccion multiple de documentos a copiar --}}
                    <div v-if="multipleSelection && copyDocuments.length">
                        <label>@lang('Documentos a guardar'): </label>
                        <ul class="list-group">
                            <li class="list-group-item" v-for="(document, index) in copyDocuments" :key="index">
                                @{{ document . name }}
                            </li>
                        </ul>
                    </div>
                    <div class="list-group" v-else-if="multipleSelection && !copyDocuments.length">
                        <li class="list-group-item list-group-item-warning">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            @lang('Los documentos seleccionados no están disponibles para ser guardados')
                        </li>
                    </div>

                    {{-- si es una seleccion individual solo se carga el nombre en la vista --}}
                    <span v-if="nameDocument">
                        @lang('Documento'): <strong>@{{ nameDocument }}</strong>
                        <hr>
                    </span>
                @else
                    <span>
                        <b><span id="showNameDocument"></span></b>
                        <hr>
                        <input type="hidden" id="inputIdDocument"></span>
                    </span>
                @endif
            </div>

            {{-- las carpetas disponibles --}}
            <div class="form-group">
                <label for="parent_id">@lang('Carpetas Disponibles')</label>
                <small class="text-muted">@lang('Seleccione una carpeta')</small>
                @if (request()->is('dashboard/document/list'))
                <select class="form-control" id="parent_id" name="parent_id" v-model="parentId"
                    :disabled="multipleSelection && !copyDocuments.length">
                @else
                <select class="form-control" id="parentID" name="parent_id">
                @endif
                    <option value="">-- @lang('PRINCIPAL') --</option>
                    @foreach ($user->getFoldersStructure() as $parent)
                        <option value="{{ $parent->id }}">
                            @for ($i = 0, $levels = count($parent->full_path ?? []); $i < $levels; $i++)
                                &nbsp;&nbsp;&nbsp;&nbsp;
                            @endfor
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click="cancel()" variant="secondary" id="closeCopyToFilesModal">@lang('Volver')</b-button>
            @if (request()->is('dashboard/document/list'))
                <b-button @click="copyDocumentToFiles" variant="primary"
                    :disabled="multipleSelection && !copyDocuments.length"
                    data-url-copy-document="@route('dashboard.document.copytofiles')"
                    data-url-files="@route('dashboard.file.list')"
                    data-empty-selection="@lang('No ha sido seleccionado ningún documento, verifique nuevamente')"
                    data-error="@lang('Ha ocurrido un error seleccionando el documento, verifique nuevamente')">
                    @lang('Guardar')
                </b-button>
            @else
                <b-button @click="onlyCopyDocumentToFiles" variant="primary"
                    data-url-copy-document="@route('dashboard.document.copytofiles')"
                    data-url-files="@route('dashboard.file.list')"
                    data-empty-selection="@lang('No ha sido seleccionado ningún documento, verifique nuevamente')"
                    data-error="@lang('Ha ocurrido un error seleccionando el documento, verifique nuevamente')">
                    @lang('Guardar')
                </b-button>
            @endif
        </template>

    </b-modal>
</template>

{{-- Control de la Tabla --}}
<div class="control-wrapper mb-2">

    {{--Paginador --}}
    <div class="paginator-wrapper mt-1">
        {{$files->links()}}

        @lang('Se muestran :files de un total de :total archivos.', [
            'files' => $files->count(),
            'total' => $files->total(),
        ])
        <a href="@route('dashboard.files.selected')">
            <span class="bold">@{{files.length}}</span> @lang('archivos seleccionados')
        </a>
    </div>
    {{--/Paginador --}}

    {{-- Compartir Archivos --}}
    <div class="sharing-wrapper">
        <div class="btn-group-vertical" role="group" aria-label="">
            <div class="btn-group" role="group" aria-label="">
                <button @click.prevent="clearFiles" :disabled="files.length <= 1"
                        class="btn btn-action-uncheck-all" data-toggle="tooltip" data-placement="top"
                        data-original-title="@lang('Limpiar la selección')">
                    <i class="fas fa-broom"></i>
                    <span class="d-none d-lg-inline">@lang('Limpiar')</span>
                </button>
                <button @click="moveFiles"
                        {{-- No es necesario acción adicional; la misma que se hace al compartir archivos se puede reutilizar --}}
                        :disabled="files.length <= 1"
                        class="btn btn-action-move" data-toggle="tooltip" data-placement="top"
                        data-original-title="@lang('Mover los archivos seleccionados')">
                    <img src="@asset('assets/images/dashboard/images/move-file.png')" height="16px" alt="">
                    <span class="d-none d-lg-inline">@lang('Mover a')</span>
                </button>
                <button @click.prevent="downloadFiles" :disabled="files.length <= 1" class="btn btn-action-download"
                        data-toggle="tooltip" data-placement="top" data-original-title="@lang('Descargar')">
                    <i class="fa fa-file-download"></i>
                    <span class="d-none d-lg-inline">@lang('Descargar')</span>
                </button>

                {{-- Firmar múltiples archivos --}}
                <button class="btn btn-action-sign" data-toggle="tooltip" data-placement="top"
                        data-original-title="@lang('Firmar')"
                        :disabled="files.length <= 1"
                        @click="selectedSign"
                >
                    <i class="fa fa-signature"></i>
                    <span class="d-none d-lg-inline">@lang('Firmar')</span>
                </button>
                {{-- /Firmar múltiples archivos --}}

            </div>

            <div class="btn-group mt-1" role="group" aria-label="">
                <button @click.prevent="sharingExtraDataMultiple" :disabled="files.length <= 1"
                        class="btn btn-action-copy-url" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="@lang('Copiar URL')">
                    <i class="fa fa-copy"></i>
                    <span class="d-none d-lg-inline">@lang('Copiar URL')</span>
                </button>

                {{-- Compartir múltiples archivos --}}
                <button @click.prevent="shareFiles" :disabled="files.length <= 1" class="btn btn-action-share"
                        data-toggle="tooltip" data-placement="bottom"
                        data-original-title="@lang('Compartir los archivos seleccionados')"
                >
                    <i class="fas fa-share-alt"></i>
                    <span class="d-none d-lg-inline">@lang('Compartir')</span>
                </button>
                {{-- /Compartir múltiples archivos --}}

                <button @click.prevent="confirmRemoveFiles" :disabled="files.length <= 1"
                        class="btn btn-action-remove" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="@lang('Eliminar los archivos seleccionados')">
                    <i class="fa fa-trash"></i>
                    <span class="d-none d-lg-inline">@lang('Eliminar')</span>
                </button>
            </div>
        </div>
    </div>
    {{--/Compartir Archivos --}}

</div>
{{--/Control de la Tabla --}}
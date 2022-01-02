{{-- Muestra el modal para la vista previa del fichero indicado --}}
<template>
    <b-modal size="xl" scrollable id="shared-file-preview">
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-list fa-2x"></i>
            <span class="bold">@lang('Vista previa del archivo que se comparte')</span>
        </template>

        {{-- El contenido de la modal --}}
        <div class="mt-2">
            {{-- Lista de archivos que se van a compartir --}}
            <div class="col-md-12">
                <div class="main-card mb-3 card">
                    <h5 class="card-header"><span v-html="file.name"></span></h5>
                    <div class="card-body">
                        <div v-if="file.preview_as === 'audio'">
                            <audio controls :src="file.path" :type="file.type" style="width: 100%;">
                                @lang('Su navegador no soporta reproducir archivos de audio.')
                            </audio>
                        </div>
                        <div v-else-if="file.preview_as === 'video'">
                            <video controls :src="file.path" :type="file.type" style="width: 100%;">
                                @lang('Su navegador no soporta reproducir archivos de video.')
                            </video>
                        </div>
                        <div v-else-if="file.preview_as === 'image'">
                            <img :src="file.path" :alt="file.name" style="width: 100%;">
                        </div>
                        <div v-else>
                            @lang('No es posible mostrar una vista previa de este archivo.')
                        </div>
                    </div>
                </div>
            </div>
            {{--/ Lista de archivos que se van a compartir --}}
        </div>

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click.prevent="clean" variant="success">@lang('Cerrar')</b-button>
        </template>

    </b-modal>
</template>
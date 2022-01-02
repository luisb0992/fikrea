<template>
    <b-modal id="delete-capture">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-exclamation-triangle fa-2x"></i>
            <span class="bold">@lang('Está seguro de querer eliminar este archivo') ?</span>
        </template>
        {{-- / El encabezado de la modal --}}

        {{-- El contenido de la modal --}}
        <div class="text-justify">
            @lang('Una vez eliminado el archivo de video, este no puede ser recuperado, por lo que le pedimos que revise si este archivo ya no le será útil en lo adelante').
        </div>
        {{-- /El contenido de la modal --}}

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click.prevent="cancel()" variant="secondary">
                @lang('Cancelar')
            </b-button>
            <b-button @click.prevent="destroyCapture" variant="danger">
                <i class="fas fa-trash"></i>
                @lang('Si, eliminar')
            </b-button>
        </template>
        {{-- /El pié de la modal --}}

    </b-modal>
</template>

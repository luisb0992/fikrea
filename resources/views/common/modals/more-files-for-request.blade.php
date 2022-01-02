<template>
    <b-modal id="more-files">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-exclamation-triangle fa-2x"></i>
            <span class="bold">@lang('Desea adicionar más archivos a este documento') ?</span>
        </template>
        {{-- / El encabezado de la modal --}}

        
        {{-- El contenido de la modal --}}
        <div>
            @lang('Puede continuar adicionando archivos que se correspondan con el documento solicitado que está aportando').
        </div>
        {{-- /El contenido de la modal --}}

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click.prevent="noMoreFilesSelected" variant="warning">
                @lang('No más. Finalizar')
            </b-button>
            <b-button @click.prevent="moreFilesSelected" variant="success">
                @lang('Si, continuar')
            </b-button>
        </template>
        {{-- /El pié de la modal --}}

    </b-modal>
</template>

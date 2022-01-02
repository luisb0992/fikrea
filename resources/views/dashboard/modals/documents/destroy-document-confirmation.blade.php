<template>
    <b-modal id="destroy-document-confirmation">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-exclamation-triangle fa-2x"></i>
            <span class="bold">@lang('Eliminación de Documento')</span>
        </template>
        
        {{-- El contenido de la modal --}}
        
        <div class="mt-2">
            @lang('El documento y los archivos generados para su procesamiento serán eliminados definitivamente')
            @lang('¿Desea continuar con la eliminación del documento?')
        </div>
        
        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click="confirmDestroyDocument()" variant="danger">@lang('Aceptar')</b-button>
            <b-button @click="cancel()" variant="success">@lang('Cancelar')</b-button>
        </template>

    </b-modal>
</template>
<template>
    <b-modal id="remove-document-confirmation">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-exclamation-triangle fa-2x"></i>
            <span class="bold">@lang('Eliminación de Documento')</span>
        </template>
        
        {{-- El contenido de la modal --}}
        
        <div class="mt-2">
            @lang('¿Desea enviar el documento a la papelera de reciclaje?')
        </div>
        
        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click="confirmRemoveDocument()" variant="danger">@lang('Aceptar')</b-button>
            <b-button @click="cancel()" variant="success">@lang('Cancelar')</b-button>
        </template>

    </b-modal>
</template>
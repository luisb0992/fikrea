<template>
    <b-modal id="remove-files-confirmation">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-exclamation-triangle fa-2x"></i>
            <span class="bold">@lang('Eliminación de Archivo')</span>
        </template>
        
        {{-- El contenido de la modal --}}
        
        <div class="mt-2">
            @lang('¿Desea eliminar los archivos seleccionados definitivamente?')
        </div>
        
        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click.prevent="removeFiles()" variant="danger">@lang('Aceptar')</b-button>
            <b-button @click.prevent="cancel()" variant="success">@lang('Cancelar')</b-button>
        </template>

    </b-modal>
</template>
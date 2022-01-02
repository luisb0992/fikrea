<template>
    <b-modal id="document-signed">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-check-circle fa-2x"></i>
            <span class="bold">@lang('Documento Guardado')</span>
        </template>
        
        {{-- El contenido de la modal --}}
        <div class="text-center">
            @lang('El documento ha sido guardado con éxito')
        </div>
    
        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click.prevent="exit" variant="success">@lang('Aceptar')</b-button>
        </template>

    </b-modal>
</template>
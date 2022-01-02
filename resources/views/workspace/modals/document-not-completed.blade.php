<template>
    <b-modal id="document-not-completed">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-exclamation-triangle fa-2x"></i>
            <span class="bold">@lang('Faltan firmas necesarias')</span>
        </template>
        
        {{-- El contenido de la modal --}}
        <div class="text-center">
            @lang('No se han completado todas las firmas del documento')
        </div>
    
        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click.prevent="exit" variant="success">@lang('Aceptar')</b-button>
        </template>

    </b-modal>
</template>
<template>
    <b-modal id="document-sended-success">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-check-circle fa-2x"></i>
            <span class="bold">@lang('Solicitud de Firma')</span>
        </template>
        
        {{-- El contenido de la modal --}}
        <div class="text-center">
            @lang('La solicitud de firma ha sido enviada con éxito')
        </div>
    
        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click="cancel()" variant="success">@lang('Aceptar')</b-button>
        </template>

    </b-modal>
</template>
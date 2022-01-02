<template>
    <b-modal id="not-save-billing-data">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-exclamation-triangle fa-2x"></i>
            <span class="bold">@lang('Compartir Datos')</span>
        </template>
        
        {{-- El contenido de la modal --}}
        <div class="text bold">
            @lang('Para compartir los datos de facturacion debe guardar los cambios, por favor guarde primero los cambios para poder compartir sus datos de facturacion')
        </div>

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click.prevent="cancel" variant="success">
                @lang('Aceptar')
            </b-button>
        </template>

    </b-modal>
</template>

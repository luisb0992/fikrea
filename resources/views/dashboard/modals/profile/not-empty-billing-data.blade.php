<template>
    <b-modal id="not-empty-billing-data">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-exclamation-triangle fa-2x"></i>
            <span class="bold">@lang('No Puede Compartir Datos')</span>
        </template>
        
        {{-- El contenido de la modal --}}
        <div class="text bold">
            @lang('Para compartir los datos de facturacion no pueden estar campos vacio, por favor rellene primero los campos que esten vacios')
        </div>

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click.prevent="cancel" variant="success">
                @lang('Aceptar')
            </b-button>
        </template>

    </b-modal>
</template>

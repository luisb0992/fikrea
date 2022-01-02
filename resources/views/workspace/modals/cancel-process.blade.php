<template>
    <b-modal id="cancel-process">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-exclamation-triangle fa-2x"></i>
            <span class="bold">@lang('Cancelación del proceso de firma')</span>
        </template>
        
        {{-- El contenido de la modal --}}
        <div class="text-center bold">
            @lang('Va a cancelar este proceso de firma y validación de documentos')
        </div>

        <div class="text-justify text-secondary mt-4">
            <div class="form-group">
                <label for="subject" class="bold">@lang('Motivo')</label>
                <input type="text" id="subject" v-model="subject" class="form-control" />
            </div>
        </div>

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click.prevent="cancel" variant="success">
                @lang('Volver')
            </b-button>
            <b-button @click.prevent="cancelProcess" variant="danger">
                @lang('Quiero cancelar este proceso')
            </b-button>
        </template>

    </b-modal>
</template>

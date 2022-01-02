<template>
    <b-modal id="document-sign-config-success">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-check-double fa-2x text-success"></i>
            <span class="bold">@lang('Documento Preparado')</span>
        </template>
        
        {{-- El contenido de la modal --}}
        <div>
            <div class="bold text-center">
                @lang('El documento se ha configurado con éxito')
            </div>
            <div v-show="document" class="text-right text-secondary mt-4">
                <div>
                    <strong>@lang('GUID')</strong> : @{{ document.guid }}
                </div> 
                <div>
                    @{{ document.name }}
                </div> 
            </div>
        </div>
        
        {{-- El pié de la modal --}}
        <template #modal-footer="{accept}">
            <b-button @click.prevent="redirectAfterSave" variant="success">@lang('Aceptar')</b-button>
        </template>

    </b-modal>
</template>
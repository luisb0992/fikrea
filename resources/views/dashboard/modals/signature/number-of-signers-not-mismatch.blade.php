<template>
    <b-modal id="number-of-signers-not-mismatch" :signers="signers">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
            <span class="bold">@lang('El número de firmantes no coincide')</span>
        </template>
        
        {{-- El contenido de la modal --}}
        <div>
            <div class="bold text-justify">
                @if (request()->is('dashboard/document/textboxs/*'))
                @lang('Debe asignar al menos, una caja de texto a cada uno de los siguientes firmantes del documento'):
                @else
                @lang('Debe asignar al menos, una firma a cada uno de los siguientes firmantes del documento'):
                @endif
            </div>
            <ul class="list-group mt-4">
                <li v-for="signer in signers" class="list-group-item" :key="signer.id">
                    
                    <span v-if="signer.selected" class="mr-2">
                        <i class="fas fa-user-check text-success"></i>
                    </span>
                    <span v-else>
                        <i class="fas fa-user-times text-danger"></i>
                    </span>

                    <span>
                        @{{signer.name}} @{{signer.lastname}}
                    </span>

                    <span v-if="signer.email">
                        <@{{signer.email}}>
                    </span>
                    
                    <span v-else="signer.phone">
                        @{{signer.phone}}
                    </span>

                </li>
            </ul>
        </div>
        
        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click.prevent="cancel" variant="primary">@lang('Aceptar')</b-button>
        </template>

    </b-modal>
</template>
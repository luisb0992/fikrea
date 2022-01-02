<template>
    <b-modal id="help-textboxs-document">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="far fa-2x fa-question-circle"></i>
            <span class="bold">@lang('Configure su documento')</span>
        </template>
        
        {{-- El contenido de la modal --}}
        <div>
            @lang('Haga click en aquellas partes del documento que requieran una caja de texto')
        </div>

        <div class="mt-2">
            @lang('Luego seleccione el firmante de la lista'). 
            @lang('Para cada caja de texto se generará una clave criptográfica única')
        </div>
        
        {{-- El pié de la modal --}}
        <template #modal-footer>
            <b-button @click.prevent="closeHelp" variant="primary">@lang('Aceptar')</b-button>
        </template>

    </b-modal>
</template>
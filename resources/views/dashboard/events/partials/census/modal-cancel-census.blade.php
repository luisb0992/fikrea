<template>
    <b-modal id="cancel-census">

        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-exclamation-triangle fa-2x"></i>
            <span class="bold">@lang('Cambio de Página')</span>
        </template>

        {{-- El contenido de la modal --}}
        <div class="mt-2">
            <p>
                @lang('Al salirse de este proceso se perderá toda la información')
            </p>

            <p class="mt-2 p-2">
                @lang('¿Está seguro que desea salir de este proceso?')
            </p>
        </div>

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click="cancel()" variant="success">@lang('Cancelar')</b-button>
            <a href="{{ $route }}" class="btn btn-danger">@lang('Aceptar')</a>
        </template>

    </b-modal>
</template>

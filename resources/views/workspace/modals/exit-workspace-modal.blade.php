<template>
    <b-modal id="exit-workspace-modal">

        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <h5 class="modal-title text-danger">
                <i class="fas fa-exclamation-triangle"></i> @lang('Saliendo del espacio de trabajo')
            </h5>
        </template>

        {{-- El contenido de la modal --}}
        <div class="text-justify">
            <div class="form-group">
                <p>@lang('¿Estas seguro que desea salir del espacio de trabajo?')</p>
                <p class="text-danger">@lang('Esta opción no puede deshacerse')</p>
            </div>
        </div>

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click.prevent="cancel" variant="secondary">
                @lang('Volver')
            </b-button>
            <b-button @click.prevent="exitWorkspace" variant="danger">
                @lang('Salir del espacio de trabajo')
            </b-button>
        </template>

    </b-modal>
</template>

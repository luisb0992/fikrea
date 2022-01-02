<template>
    <b-modal id="retrieve-row-modal">

        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> @lang('AVISO')</h5>
        </template>

        {{-- El contenido de la modal --}}
        <div class="mt-2">
            <div class="alert alert-warning" role="alert">
                <h5 class="text-justify">
                    <p class="font-weight-bold h4">
                        @lang('Ha eliminado un concepto y sus validaciones')
                    </p>
                    <p>
                        @lang('Si cree que es correcto pulse <b>Volver y Continuar</b>.')
                        @lang('Si fue un error puede recuperar sus datos pulsando <b>Recuperar Concepto</b>.')
                    </p>
                </h5>
            </div>
        </div>

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <button type="button" class="btn btn-secondary" @click="$bvModal.hide('retrieve-row-modal')">
                @lang('Volver y continuar')
            </button>
            <button type="button" class="btn btn-warning" @click="retrieveRowAndAddIntoForm">
                @lang('Recuperar concepto')
            </button>
        </template>

    </b-modal>
</template>

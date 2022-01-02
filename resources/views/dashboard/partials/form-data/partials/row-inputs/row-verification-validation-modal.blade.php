<template>
    <b-modal id="verification-modal">

        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <h5 class="modal-title">@lang('ADVERTENCIA')</h5>
        </template>

        {{-- El contenido de la modal --}}
        <div class="mt-2">
            <div class="alert alert-warning" role="alert">
                <h5 class="text-justify">
                    <p>
                        @lang('Le recomendamos que revise los campos <b>mínimo aceptado, máximo permitido y tipo de carácter</b>.')
                        @lang('Vemos que hay campos vacíos y es importante antes de darlo por finalizado.')
                        <hr>
                        <span class="text-center">@lang('Si cree que es correcto pulse <b>Continuar</b>')</span>
                    </p>
                </h5>
            </div>
        </div>

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <button type="button" class="btn btn-secondary" @click="$bvModal.hide('verification-modal')">
                @lang('Volver y seguir configurando')
            </button>
            <button type="button" class="btn btn-warning" @click="closeModalAndContinue">
                @lang('Continuar')
            </button>
        </template>

    </b-modal>
</template>

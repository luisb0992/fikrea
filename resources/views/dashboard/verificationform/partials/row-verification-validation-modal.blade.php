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
                    <p class="mb-2">
                        @lang('Le recomendamos que revise los campos mínimo aceptado, máximo permitido y tipo de carácter.')
                        @lang('Vemos que hay campos vacíos y es importante antes de darlo por finalizado.')
                    </p>
                    <br>
                    <strong class="text-center">@lang('Si cree que es correcto, pulse CONTINUAR')</strong>
                </h5>
            </div>
        </div>

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">

            {{--  cancelar y volver a seguir configurando el formulario  --}}
            <button type="button" class="btn btn-secondary" @click="$bvModal.hide('verification-modal')">
                @lang('Volver y seguir configurando')</button>

            {{--  guardar la plantilla y continuar el proceso  --}}
            <button type="button" class="btn btn-primary" @click.prevent="closeModalAndContinue"
                data-save-continue="true"
                data-save-form-verification="@route('dashboard.verificationform.save', ['id' => $verificationForm->id ?? null])"
                data-redirect-to-signers="@route('dashboard.verificationform.selectSigners', ['id' => $verificationForm->id ?? null])"
                data-toggle="tooltip" title="@lang('Guardar y continuar')">
                @lang('Guadar y continuar')
            </button>

            {{--  solo continuar sin guardar  --}}
            <button type="button" class="btn btn-success" @click.prevent="closeModalAndContinue"
                data-save-continue="false"
                data-save-form-verification="@route('dashboard.verificationform.save', ['id' => $verificationForm->id ?? null])"
                data-redirect-to-signers="@route('dashboard.verificationform.selectSigners', ['id' => $verificationForm->id ?? null])"
                data-toggle="tooltip" title="@lang('Continue con el proceso')">
                @lang('Continuar')
            </button>
        </template>

    </b-modal>
</template>

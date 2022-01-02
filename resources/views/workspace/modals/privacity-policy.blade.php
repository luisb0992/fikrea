<template>
    <b-modal id="privacity-policy">

        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-user-shield fa-2x"></i>
            <span class="bold">@lang('Advertencia de :app', ['app' => config('app.name')])</span>
        </template>

        {{-- El contenido de la modal --}}
        <div class="text-justify text-success bold">
            @lang('El siguiente procedimiento garantiza la seguridad, privacidad e integridad de la información
            proporcionada por las personas intervinientes')
        </div>

        <div class="text-justify text-secondary mt-4">
            @lang('A continuación deberá realizar las acciones que le han sido solicitadas')

            @lang('En el proceso se le pedirá el acceso a su ubicación y el acceso a otros dispositivos
            como su micrófono o su cámara')
        </div>

        <div class="text-justify bold mt-4">
            @lang('En el proceso se le pedirá el acceso a su ubicación y el acceso a otros dispositivos
            como su micrófono o su cámara')
        </div>

        <div class="text-justify text-secondary mt-4">
            @lang('Si está de acuerdo con las condiciones de privacidad, puede continuar')
        </div>

        {{-- Leída la política de privacidad --}}
        <div class="form-group text-right mt-4">
            <input @click="checkPrivacityPolicy()" type="checkbox" v-model="privacityPolicy"
                class="form-check-input check-1-8" id="privacityPolicyCheck">
            <label class="ml-2 bold" for="privacityPolicyCheck">
                <a href="#" data-toggle="modal" data-target="#modal-privacity-policy">
                    @lang('He leído la Política de Privacidad')
                </a>
            </label>
        </div>
        {{-- /Leída la política de privacidad --}}

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click="cancel()" :disabled="!privacityPolicyRead" variant="success">
                @lang('Aceptar')
            </b-button>
        </template>

    </b-modal>
</template>

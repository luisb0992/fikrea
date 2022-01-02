<template>
    <b-modal id="cancel-verificationform">

        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-exclamation-triangle fa-2x"></i>
            <span class="bold">@lang('Cancelación del proceso <b>verificación de datos</b>')</span>
        </template>

        {{-- El contenido de la modal --}}
        <div class="text-justify text-secondary mt-4">
            <div class="form-group">
                <label for="reason-cancel" class="bold">@lang('Motivo')</label>
                <select type="text" class="form-control" name="reason_cancel_verificationform_id" id="reason-cancel" form="cancel-verificationform-formdata" required="">
                    <option value="">@lang('Selecciona Una Opción...')</option>
                    @foreach ($reasons as $reason)
                        <option value="{{ $reason->id }}">{{ $reason->reason }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click.prevent="cancel" variant="success">
                @lang('Volver')
            </b-button>

            <form action="@route('workspace.cancel.verificationform', ['token' => $token])" method="POST"
                id="cancel-verificationform-formdata">
                @csrf
            </form>
            <button form="cancel-verificationform-formdata" class="btn btn-danger">
                @lang('Rechazar')
            </button>
        </template>

    </b-modal>
</template>

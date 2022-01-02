<template>
    <b-modal id="cancel-request-{{$validation->id}}">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-exclamation-triangle fa-2x"></i>
            <span class="bold">@lang('Cancelación de proceso de') @validation($validation)</span>
        </template>
        
        {{-- El contenido de la modal --}}
        <div class="text-justify text-secondary mt-4">
            <div class="form-group">
                <label for="reason-cancel" class="bold">@lang('Motivo')</label>
                <select type="text" class="form-control" name="reason_cancel_request_id" id="reason-cancel" form="cancel_request-{{$validation->id}}" required="">
                    <option value="">@lang('Seleccione una opción...')</option>
                    @foreach($reasons as $reason)
                        <option value="{{$reason->id}}">@lang($reason->reason)</option>   
                    @endforeach
                </select>     
            </div>
        </div>

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click.prevent="cancel" variant="success">
                @lang('Volver')
            </b-button>

            <form action="@switch ($validation->validation)
                @case(\App\Enums\ValidationType::HAND_WRITTEN_SIGNATURE)
                    @route('workspace.cancel.signature', ['token' => $token])
                @break
                @case(\App\Enums\ValidationType::AUDIO_FILE_VERIFICATION)
                    @route('workspace.cancel.audio', ['token' => $token])
                @break
                @case(\App\Enums\ValidationType::VIDEO_FILE_VERIFICATION)
                    @route('workspace.cancel.video', ['token' => $token])
                @break
                @case(\App\Enums\ValidationType::PASSPORT_VERIFICATION)
                    @route('workspace.cancel.passport', ['token' => $token])
                @break
                @case(\App\Enums\ValidationType::FORM_DATA_VERIFICATION)
                    @route('workspace.cancel.formdata', ['token' => $token])
                @break
                @case(\App\Enums\ValidationType::DOCUMENT_REQUEST_VERIFICATION)
                    @route('workspace.cancel.request', ['token' => $token])
                @break @endswitch" method="POST" id="cancel_request-{{$validation->id}}" name="cancel_request-{{$validation->id}}">
                @csrf
                <input type="hidden" name="validation" value="{{$validation->id ?? null}}">
            </form>
            <button form="cancel_request-{{$validation->id}}" class="btn btn-danger">
                @lang('Rechazar')
            </button>
        </template>

    </b-modal>
</template>

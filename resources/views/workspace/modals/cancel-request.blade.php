<template>
    <b-modal id="cancel-request-document">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-exclamation-triangle fa-2x"></i>
            <span class="bold">@lang('Cancelación de proceso de solicitud de documentos')</span>
        </template>
        
        {{-- El contenido de la modal --}}
        <div class="text-justify text-secondary mt-4">
            <div class="form-group">
                <label for="reason-cancel" class="bold">@lang('Motivo')</label>
                <select type="text" class="form-control" name="reason_cancel_request_id" id="reason-cancel" form="cancel_request-document" required="">
                    <option value="">@lang('Seleccione una opción...')</option>
                    @foreach($reasons as $reason)
                        <option value="{{$reason->id}}">{{ $reason->reason }}</option>   
                    @endforeach
                </select>     
            </div>
        </div>

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click.prevent="cancel" variant="success">
                @lang('Volver')
            </b-button>

            <form action="@route('workspace.cancel.request', ['token' => $token])" method="POST" id="cancel_request-document" name="cancel_request">
                @csrf
                <input type="hidden" name="file_request" value="{{$signer->request()->id ?? ''}}">
                <input type="hidden" name="doc_req" value="1">
            </form>
            <button form="cancel_request-document" class="btn btn-danger">
                @lang('Rechazar')
            </button>
        </template>

    </b-modal>
</template>

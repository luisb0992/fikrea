{{-- 
    Los botones de Acción en la vista donde se crean las solicitudes de documentos
--}}

{{-- Los botones de Acción --}}
<div class="col-md-12 mb-4">
    <div class="btn-group" role="group">
        <button
            data-save-document-request="@route('dashboard.document.request.save')"
            data-after-save-redirect-to="@route('dashboard.document.request.signers')"  
            @click.prevent="saveRequest"
            :disabled="saveButtonDisabled"
            class="btn btn-lg btn-success mr-1">
            @lang('Continuar')
        </button>

        <a href="" class="btn btn-lg btn-danger">
            @lang('Atrás')
        </a>
    </div>
</div>
{{--/Los botones de Acción --}}
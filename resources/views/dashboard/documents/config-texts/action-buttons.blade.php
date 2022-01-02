{{-- Los botones de acción --}}
<div class="row col-md-12 mt-2 ml mb-2">
    <div class="btn-group" role="group">

        <a href="#" @click.prevent="saveDocument"
            class="btn btn-lg btn-success mr-1"
        >
            {{-- 
                Si hay un proceso de firma o de validación de datos o
                un proceso de solicitud de documentos seleccionado
            --}}
            @if($document->mustBeValidateByHandWrittenSignature()
                || $document->mustBeValidateByDocumentRequest()
                || $document->mustBeValidateByFormData()
            )
                @lang('Continuar')
            @else
                @lang('Finalizar')
            @endif
        </a>
        
        <a href="@route('dashboard.document.list')" class="btn btn-lg btn-danger">
            @lang('Cancelar')
        </a>
        
    </div>
</div>
{{--/Los botones de acción --}}
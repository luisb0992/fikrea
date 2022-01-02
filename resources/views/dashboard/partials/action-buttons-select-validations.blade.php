{{-- Los botones de Acción en la vista donde se seleccionan las validaciones
     para cada firmante --}}

{{-- Los botones de Acción --}}
<div class="row">
    <div class="col-md-12 my-4">
        <div class="btn-group" role="group">
            <a href="#!" 
                data-request-save-validations="@route('dashboard.document.save.validations', ['id' => $document->id])"
                {{-- Direccionamiento --}}
                data-redirect-to-textboxs="@route('dashboard.document.textboxs', ['id' => $document->id])"
                data-redirect-to-sign="@route('dashboard.document.prepare', ['id' => $document->id])"
                data-redirect-to-formdata="@route('dashboard.document.formdata', ['id' => $document->id])"
                data-redirect-to-list="@route('dashboard.document.list')"
                data-redirect-to-config-documents-request="@route('dashboard.document.request.validations', ['id' => $document->id])"
                
                @click.prevent="saveValidations" class="btn btn-lg btn-success mr-1"
            >
                @lang('Continuar')
            </a>

            <a href="@route('dashboard.document.signers', ['id' => $document->id])" class="btn btn-lg btn-danger">
                @lang('Atrás')
            </a>
        </div>
    </div>
</div>
{{-- /Los botones de Acción --}}

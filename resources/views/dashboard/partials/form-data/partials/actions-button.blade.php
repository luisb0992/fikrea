<div class="col-md-12 mb-3">
    <div class="row">
        <dic class="col-12 col-md-auto mb-2 mr-n4">
            <button type="button" class="btn btn-primary btn-lg btn-block" data-save-continue="true"
                data-save-formdata-validation="@route('dashboard.document.saveFormDataValidation', ['id' => $document->id])"
                data-redirect-to-sign="@route('dashboard.document.prepare', ['id' => $document->id])"
                data-redirect-to-document-request="@route('dashboard.document.request.validations', ['id' => $document->id])"
                data-redirect-to-list="@route('dashboard.document.list')" data-toggle="tooltip"
                title="@lang('Guarde la plantilla para usarla cuado desee y continúe con el proceso')"
                @click.prevent="saveFormDataValidation" :disabled="isDisabled">
                @lang('Guardar y continuar')
            </button>
        </dic>

        <div class="col-12 col-md-auto mb-2 mr-n4">
            <button type="button" class="btn btn-success btn-lg btn-block" data-save-continue="false"
                data-save-formdata-validation="@route('dashboard.document.saveFormDataValidation', ['id' => $document->id])"
                data-redirect-to-sign="@route('dashboard.document.prepare', ['id' => $document->id])"
                data-redirect-to-document-request="@route('dashboard.document.request.validations', ['id' => $document->id])"
                data-redirect-to-list="@route('dashboard.document.list')" data-toggle="tooltip"
                title="@lang('Continúe con el proceso')" @click.prevent="saveFormDataValidation" :disabled="isDisabled">
                @lang('Continuar')
            </button>
        </div>

        <div class="col-12 col-md-auto">
            <a href="@route('dashboard.document.list')"
                class="btn btn-danger btn-lg d-block d-md-inline-block">@lang('Cancelar')</a>
        </div>
    </div>
</div>

{{-- mensaje informativo de la app --}}
<div class="col-md-12">
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <div class="text-justify row">
            <div class="col-md-auto d-none d-md-block border-right"><i class="far fa-lightbulb fa-4x"></i></div>
            <div class="col">
                @lang('El botón <b>Guardar y continuar</b> guardará la <b>Verificación de Datos</b> creada para
                ser usada nuevamente y aparecerá en la sección <b>Plantillas personales</b> como parte de sus propias
                plantillas.')
                @lang('Mientras que el botón <b>Continuar</b> solo continuará con el proceso una
                vez termine de validar o asignar la <b>Verificación de Datos</b>')
            </div>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>

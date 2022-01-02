<div class="col-md-12 mb-3">
    <div class="row">
        <div class="col-12 col-md-auto mb-2 mr-n4">
            <button type="button" class="btn btn-primary btn-lg btn-block"
                data-save-continue="true"
                data-save-form-verification="@route('dashboard.verificationform.save', ['id' => $verificationForm->id ?? null])"
                data-redirect-to-signers="@route('dashboard.verificationform.selectSigners', ['id' => $verificationForm->id ?? null])"
                data-toggle="tooltip" title="@lang('Guarde la plantilla para usarla cuado desee y continúe con el proceso')"
                @click.prevent="addFormToUSerforValidate" :disabled="isDisabled">
                @lang('Guardar y continuar')
            </button>
        </div>

        <div class="col-12 col-md-auto mb-2 mr-n4">
            <button type="button" class="btn btn-success btn-lg btn-block"
                data-save-continue="false"
                data-save-form-verification="@route('dashboard.verificationform.save', ['id' => $verificationForm->id ?? null])"
                data-redirect-to-signers="@route('dashboard.verificationform.selectSigners', ['id' => $verificationForm->id ?? null])"
                data-toggle="tooltip" title="@lang('Continúe con el proceso')"
                @click.prevent="addFormToUSerforValidate" :disabled="isDisabled">
                @lang('Continuar')
            </button>
        </div>

        <div class="col-12 col-md-auto">
            <a href="@route('dashboard.verificationform.list')"
                class="btn btn-danger btn-lg d-block d-md-inline-block">@lang('Cancelar')</a>
        </div>
    </div>
</div>

<div class="col-md-12">
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <div class="text-justify row">
            <div class="col-md-auto d-none d-md-block border-right"><i class="far fa-lightbulb fa-4x"></i></div>
            <div class="col">
                @lang('El botón') <strong>@lang('Guardar y continuar')</strong> @lang('guardará la VERIFICACIÓN DE DATOS creada para
                ser usada nuevamente y aparecerá en la sección')
                <strong>@lang('PLANTILLAS PERSONALES')</strong> @lang('como parte de sus propias plantillas').
                @lang('Mientras que el botón') <strong>@lang('Continuar')</strong> @lang('solo continuará con el proceso una
                vez terminé de validar o asignar la <b>VERIFICACIÓN DE DATOS</b>')
            </div>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>

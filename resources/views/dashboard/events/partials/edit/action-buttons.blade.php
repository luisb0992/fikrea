<div class="col-md-12 mb-4">
    <div class="row">
        <div class="col-12 col-md-auto mb-2 mr-n4">
            <button type="button" class="btn btn-primary btn-lg btn-block" data-draft=true
                data-save-event="@route('dashboard.event.save', ['id' => $event->id ?? null])"
                data-event-list="@route('dashboard.event.list')"
                data-toggle="tooltip" title="@lang('Guarde a modo de borrador para ser usada cuado desee')"
                :disabled="(allStatesCorrect && requiredFieldsCompleted) ? false : true" @click="saveEvent">
                @lang('Guardar borrador')
            </button>
        </div>

        <div class="col-12 col-md-auto mb-2 mr-n4">
            <button type="button" class="btn btn-success btn-lg btn-block" data-draft=false
                data-save-event="@route('dashboard.event.save', ['id' => $event->id ?? null])"
                data-event-list="@route('dashboard.event.list')"
                data-toggle="tooltip" title="@lang('Continúe con el proceso')"
                :disabled="(allStatesCorrect && requiredFieldsCompleted) ? false : true" @click="saveEvent">
                @lang('Continuar')
            </button>
        </div>

        <div class="col-12 col-md-auto">
            <a href="@route('dashboard.event.list')"
                class="btn btn-danger btn-lg d-block d-md-inline-block">@lang('Cancelar')</a>
        </div>
    </div>
</div>

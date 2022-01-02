<div class="col-md-12 mb-4">
    <div class="input-group" role="group">
        <button :disabled="ifNotUsers" class="btn btn-lg btn-success mr-1" data-draft=false
            data-save-census="@route('dashboard.event.save.census', ['id' => $event->id])" @click.prevent="saveCensus">
            @lang('Continuar')
        </button>

        <button type="button" class="btn btn-primary btn-lg mr-1" data-draft=true
            data-save-census="@route('dashboard.event.save.census', ['id' => $event->id])" @click.prevent="saveCensus"
            data-toggle="tooltip" title="@lang('Guarde a modo de borrador para ser usada cuado desee')"
            :disabled="ifNotUsers">
            @lang('Guardar borrador')
        </button>

        <a href="#" @click.prevent="cancelCensusModal" class="btn btn-lg btn-danger">@lang('Cancelar')</a>
    </div>
</div>

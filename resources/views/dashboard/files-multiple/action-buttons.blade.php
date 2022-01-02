{{-- Los botones de Acción --}}
<div class="col-md-12 mb-4">
    <div class="btn-group" role="group">
        <button v-if="show"
            @click.prevent="gotoSigners"
            class="btn btn-lg btn-success mr-1">
            @lang('Continuar')
        </button>
        
        <button v-else
            :disabled="!canSign"
            @click.prevent="gotoSignMultiple"
            class="btn btn-lg btn-success mr-1">
            @lang('Continuar')
        </button>

        <a href="@route('dashboard.file.list')" class="btn btn-lg btn-danger">
            @lang('Cancelar')
        </a>
    </div>
</div>
{{--/Los botones de Acción --}}
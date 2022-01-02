{{-- Los botones con las acciones --}}
<div class="col-md-12 mb-4">
    <div class="input-group" role="group">

        <button :disabled="ifNotSigners && !shareUrl"
        	@click.prevent="saveSigners"
        	class="btn btn-lg btn-success mr-1"
        >
        	@lang('Finalizar')
        </button>

        <a href=""
        	@click.prevent="cancelSignerRequestModal"
        	class="btn btn-lg btn-danger"
        >
        	@lang('Cancelar')
        </a>
    </div>
</div>
{{-- /Los botones con las acciones --}}
<div class="col-md-12 mb-4">
    <div class="input-group" role="group">
        <button :disabled="ifNotSigners" @click.prevent="saveSigners" class="btn btn-lg btn-success mr-1">@lang('Finalizar')</button>
        <a href="@route('dashboard.verificationform.edit', ['id' => $verificationForm->id])"  class="btn btn-lg btn-primary mr-1">@lang('Volver')</a>
        <a href="" @click.prevent="cancelSelectSignerModal" class="btn btn-lg btn-danger">@lang('Cancelar')</a>
    </div>
</div>
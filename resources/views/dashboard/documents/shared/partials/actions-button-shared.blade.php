<div class="col-md-12 mb-4">
    <div class="input-group" role="group">
        <a href="#" @click.prevent="documentShare" class="btn btn-lg btn-success mr-1">
            @lang('Continuar')
        </a>
        <a href="@route('dashboard.document.sent')" class="btn btn-lg btn-danger">
            @lang('Cancelar')
        </a>
    </div>
</div>

<div class="col-md-12">
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <div class="text-justify row">
            <div class="col-md-auto d-none d-md-block border-right"><i class="fas fa-exclamation-triangle fa-4x"></i></div>
            <div class="col">
                @lang('Cada documento posee información estrictamente confidencial y datos de carácter personal sensibles.')
                @lang('Queda a consentimiento del usuario el hecho de compartir datos a terceros teniendo en cuenta el riesgo que puede o no causar.')
            </div>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>
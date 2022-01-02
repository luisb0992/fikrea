<div class="col-md-12 pr-0 mb-4">
    <a href="@route('workspace.billing.download', ['token' => $company->token])" class="btn btn-danger btn-lg btn-block"
        role="button" target="_blank">
        <i class="fas fa-file-pdf fa-2x"></i>
        <br>
        @lang('Descargar todos los datos')
    </a>
</div>

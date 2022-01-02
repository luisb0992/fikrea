{{-- datos de la firma digital si es necesario --}}
<div class="col-md-12 pr-0">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">@lang('Datos Adicionales')</h5>
            <div class="form-group">
                <label>@lang('Título')</label>
                <input type="text" class="form-control" value="{{ $company->sometitle }}" disabled>
            </div>
            <div class="form-group">
                <label>@lang('Comentario')</label>
                <textarea class="form-control" disabled>{{ $company->somecomment }}</textarea>
            </div>
            <hr>
            <div class="form-group">
                <label>
                    @lang('Firma manuscrita digital del usuario <b>:user</b>', [
                    'user' => $company->user ? $company->user->getFullNameUser() : null
                    ])
                </label>
                <div class="d-flex border border-primary justify-content-center">
                    <div class="text-center">
                        <img src="@exists($company->user->config->sign->sign)" alt="signature" class="img-fluid" style="min-height: 200px; min-width: 300px;"/>
                    </div>
                    <div>
                        <span>@exists($company->user->config->sign->code)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
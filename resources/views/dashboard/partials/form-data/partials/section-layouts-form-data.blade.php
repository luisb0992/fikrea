{{-- Si ya se ha realizado la validacion anteriormente, 
    el usuario tiene la posibilidad devolver a cargar los datos previamente guardados --}}
@if (isset($document))
    @if ($document->hasADataVerificationPerformed())
        <div class="col-md-12 mb-3">
            <div class="card border-warning">
                <div class="card-header bg-warning">
                    <i class="fas fa-info-circle"></i>&nbsp; @lang('verificación de datos realizada')
                </div>
                <div class="card-body text-md-left text-center">
                    <p class="card-text text-justify">
                        @lang('Ya ha realizado esta validación anteriormente'),
                        @lang('si desea cargar todos los datos previos presione el botón <b>CARGAR VERIFICACIÓN DE
                            DATOS</b>')
                    </p>
                    <button type="button" class="btn btn-primary btn-lg" @click="loadDataVerification">@lang('Cargar
                        verificación de datos')</button>
                    <div id="formDataValidation"
                        data-form-data-validation="@json($document->formdata->groupBy('signer_id'))" class="d-none">
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif

@if (isset($verificationForm))
    @if ($verificationForm->fieldsRow()->count())
        <div class="col-md-12 mb-3">
            <div class="card border-warning">
                <div class="card-header bg-warning">
                    <i class="fas fa-info-circle"></i>&nbsp; @lang('verificación de datos realizada')
                </div>
                <div class="card-body text-md-left text-center">
                    <p class="card-text text-justify">
                        @lang('Ya ha realizado esta verificación anteriormente'),
                        @lang('si desea cargar todos los datos previos presione el botón <b>CARGAR VERIFICACIÓN DE
                            DATOS</b>')
                    </p>
                    <button type="button" class="btn btn-primary btn-lg" @click="loadDataVerification">@lang('Cargar
                        verificación de datos')</button>
                    <div id="formDataVerification" class="d-none"
                        data-form-data-verification="@json($verificationForm->fieldsRow)"
                        data-name-verification="{{ $verificationForm->name }}"
                        data-comment-verification="{{ $verificationForm->comment }}">
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif

{{-- Plantillas de formulario del sistema --}}
<div class="col-md-6 mb-4">
    <div class="card border-dark">
        <h5 class="card-header bg-dark text-white">
            <i class="fas fa-cogs mr-1"></i> @lang('Plantillas que ofrece :app', ['app' => config('app.name')])
            <span class="text-justify d-inline-block" tabindex="0" data-toggle="tooltip"
                title="@lang('Las plantillas predeterminadas las ofrece :app. Así tiene a su disposición distintos formularios listos para ser usados', ['app' => config('app.name')])">
                <button class="btn btn-link btn-sm" type="button">
                    <i class="fas fa-info-circle text-white"></i>
                </button>
            </span>
        </h5>
        <div class="card-body">
            <div class="card-group">
                @forelse($appFormTemplates as $key => $appTemplate)
                    @if ($appTemplate->filter(fn($template) => $template->type == \App\Enums\FormType::PARTICULAR_FORM)->all())

                        @include('dashboard.partials.form-data.box-form-template', [
                        'icon' => 'far fa-user',
                        'textColor' => 'text-success',
                        'iconName' => 'Particular',
                        'templates' => $appTemplate,
                        'key' => $key+1,
                        'textType' => Lang::get('Particular'),
                        'enumType' => \App\Enums\FormType::PARTICULAR_FORM
                        ])

                    @endif
                    @if ($appTemplate->filter(fn($template) => $template->type == \App\Enums\FormType::BUSINESS_FORM)->all())

                        @include('dashboard.partials.form-data.box-form-template', [
                        'icon' => 'fas fa-user-tie',
                        'textColor' => 'text-primary',
                        'iconName' => 'Empresarial',
                        'templates' => $appTemplate,
                        'key' => $key+2,
                        'textType' => Lang::get('Empresarial'),
                        'enumType' => \App\Enums\FormType::BUSINESS_FORM
                        ])

                    @endif
                @empty
                    <h5 class="card-title"><i class="fas fa-info-circle"></i> @lang('No existen plantillas
                        predeterminadas creadas para
                        :app', ['app' => config('app.name')])</h5>
                @endforelse
            </div>
        </div>
    </div>
</div>
{{-- /Plantillas de formulario del sistema --}}

{{-- Plantillas de formulario del usuario --}}
<div class="col-md-6 mb-4">
    <div class="card border-primary">
        <h5 class="card-header bg-primary text-white">
            <i class="fas fa-user-tag mr-1"></i> @lang('Plantillas personales')
            <span class="text-justify d-inline-block" tabindex="0" data-toggle="tooltip"
                title="@lang('Las plantillas personales son creadas cada vez que presione el botón GUARDAR Y CONTINUAR. La próxima vez que entre a una VERIFICACIÓN DE DATOS las podrá ver disponibles desde esta sección')">
                <button class="btn btn-link btn-sm" type="button">
                    <i class="fas fa-info-circle text-white"></i>
                </button>
            </span>
        </h5>
        <div class="card-body">
            <div class="card-group">
                @forelse($userFormTemplates as $key => $userTemplate)
                    @if ($userTemplate->filter(fn($template) => $template->type == \App\Enums\FormType::PARTICULAR_FORM)->all())

                        @include('dashboard.partials.form-data.box-form-template', [
                        'icon' => 'far fa-user',
                        'textColor' => 'text-success',
                        'iconName' => 'Particular',
                        'templates' => $userTemplate,
                        'key' => $key+3,
                        'textType' => Lang::get('Particular'),
                        'enumType' => \App\Enums\FormType::PARTICULAR_FORM
                        ])

                    @endif
                    @if ($userTemplate->filter(fn($template) => $template->type == \App\Enums\FormType::BUSINESS_FORM)->all())

                        @include('dashboard.partials.form-data.box-form-template', [
                        'icon' => 'fas fa-user-tie',
                        'textColor' => 'text-primary',
                        'iconName' => 'Empresarial',
                        'templates' => $userTemplate,
                        'key' => $key+4,
                        'textType' => Lang::get('Empresarial'),
                        'enumType' => \App\Enums\FormType::BUSINESS_FORM
                        ])

                    @endif
                @empty
                    <h5 class="card-title"><i class="fas fa-info-circle"></i> @lang('No posee plantillas creadas')</h5>
                @endforelse
            </div>
        </div>
    </div>
</div>
{{-- /Plantillas de formulario del usuario --}}

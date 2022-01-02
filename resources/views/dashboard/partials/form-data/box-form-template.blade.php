{{-- Tarjeta con la informacion de cada una de las plantillas de formulario --}}
<div class="col-md-4 mb-2">
    <div class="card text-center">
        <div class="card-body">
            <h5 class="{{ $textColor }} card-title"><i class="{{ $icon }}"></i> @lang($iconName)</h5>
            <div class="d-flex justify-content-around">
                <p class="card-text">
                    @if (isset($document))
                        <button type="button" class="btn btn-success" @click="addFormTemplateOnView"
                            data-form-template-id="{{ $key }}" :disabled="isNoSingerAvailable">
                            <i class="fas fa-hand-point-up"></i> @lang('Usar')
                        </button>
                    @else
                        <button type="button" class="btn btn-success" @click="addFormTemplateOnView"
                            data-form-template-id="{{ $key }}" :disabled="isActiveButton">
                            <i class="fas fa-hand-point-up"></i> @lang('Usar')
                        </button>
                    @endif
                    <b-button v-b-modal.modal-info-form-template-{{ $key }} variant="info"><i
                            class="far fa-list-alt"></i> @lang('Ver')</b-button>
                </p>
            </div>
        </div>
    </div>
</div>
{{-- /Tarjeta con la informacion de cada una de las plantillas de formulario --}}

{{-- Ambos modales contienen las mismas caracteristicas,
    pero cada uno enfocado al tipo de formulario, en este caso particular y empresarial --}}

{{-- Modal del formulario de datos para los particulares --}}
@if ($enumType == \App\Enums\FormType::PARTICULAR_FORM)
    @include('dashboard.partials.form-data.modal-form-fields', [
    'enumType' => $enumType,
    'key' => $key,
    'textType' => $textType,
    'templates' => $templates,
    ])
@endif
{{-- /Modal del formulario de datos para los particulares --}}

{{-- Modal del formulario de datos para los empresariales --}}
@if ($enumType == \App\Enums\FormType::BUSINESS_FORM)
    @include('dashboard.partials.form-data.modal-form-fields', [
    'enumType' => $enumType,
    'key' => $key,
    'textType' => $textType,
    'templates' => $templates,
    ])
@endif
{{-- /Modal del formulario de datos para los empresariales --}}

<div class="form-row border shadow-sm mb-3 p-2" v-for="(row, i) in rowsFormData" :key="i">
    <input type="hidden" name="type[]" class="input-type" v-model="row.type" :id="'type-'+i">
    <input type="hidden" name="template_number[]" class="template-number" v-model="row.templateNumber"
        :id="'template_number-'+i">

    {{-- Input check --}}
    <div class="form-group col-md-1">
        <label for="" class="d-none d-md-block">&nbsp;</label>
        <div class="d-md-flex align-items-md-center justify-content-md-start ml-4 mt-md-4 mt-lg-4 ml-4 mt-2 mt-md-0">
            <input type="checkbox" v-model="allChecked ? allChecked : row.checked" name="check_field[]"
                :data-check-id="i" class="input-check form-check-input" style="transform: scale(2.0)">

            {{-- visible solo en smartphone y tablet --}}
            <label class="d-block d-md-none ml-2 ml-md-0">
                <small class="text-center">
                    <i class="fas fa-info-circle text-dark d-none d-md-block"></i> <span
                        class="text-primary">@lang('Marque el campo si desea solicitar este campo')</span>
                </small>
            </label>
            {{-- /visible solo en smartphone y tablet --}}
        </div>
    </div>

    {{-- input field_name (texto o pregunta) --}}
    <div class="form-group col-md-4">
        <label :for="'field_name-'+i" class="span-field_name">@lang('Concepto que pregunta')</label>
        <span class="text-justify d-inline-block" tabindex="0">
            <b-button v-b-tooltip.hover size="sm" variant="link"
                title="@lang('Utilice el concepto predeterminado, edite o cree uno nuevo')">
                <i class="fas fa-info-circle text-dark"></i>
            </b-button>
        </span>
        <input type="text" name="field_name[]" class="form-control"
            :placeholder="row.fieldName ? row.fieldName : '@lang('Ingrese un concepto descriptivo')'"
            v-model="row.fieldName" :id="'field_name-'+i">
    </div>

    {{-- input field_text (descripcion o respuesta) --}}
    <div class="form-group col-md-4">
        <label :for="'field_text-'+i" class="span-field_text">@lang('Respuesta que debe verificar')</label>
        <span class="text-justify d-inline-block" tabindex="0">
            <b-button v-b-tooltip.hover size="sm" variant="link"
                title="@lang('Si el campo viene complementado, revise y en caso de existir error, modifique la información. En caso de estar vacío, se le solicita que rellene con la información solicitada en el campo CONCEPTO QUE PREGUNTA')">
                <i class="fas fa-info-circle text-dark"></i>
            </b-button>
        </span>
        <input type="text" name="field_text[]" class="form-control"
            :placeholder="row.fieldText ? row.fieldText : '@lang('Ingrese una respuesta detallada')'"
            v-model="row.fieldText" :id="'field_text-'+i">
    </div>

    {{-- Botones de accion --}}
    <div class="form-group col-md-auto text-nowrap">
        @include('dashboard.partials.form-data.partials.row-inputs.row-action-buttons')
    </div>
    {{-- /Botones de accion --}}

    {{-- visible solo en modo escritorio (md) --}}
    <div class="form-group col-md-12 d-none d-md-block">
        <small>
            <i class="fas fa-info-circle text-dark"></i> <span class="text-primary">@lang('Marque el check si desea
                solicitar este campo')</span>
        </small>
    </div>
    {{-- /visible solo en modo escritorio (md) --}}

    {{-- Modal para la validacion de campos --}}
    @include('dashboard.partials.form-data.partials.row-inputs.row-modal-validation')
    {{-- /Modal para la validacion de campos --}}
</div>

<template>
    <b-modal :id="'validationModal-'+i">

        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <h5 class="modal-title">@lang('Validación y configuración de campos')</h5>
        </template>

        {{-- El contenido de la modal --}}
        {{-- mensaje de aviso sobre la validacion --}}
        <div class="alert alert-warning" role="alert" :id="'msjValidateModal-'+i" style="display: none;">
            @lang('El valor máximo no puede ser menor o igual al valor mínimo'), <strong>@lang('debe ser mayor')</strong>
        </div>

        {{-- campo minimo aceptado --}}
        <div class="form-group" data-toggle="tooltip" title="@lang('Mínimo de caracteres aceptados')">
            <label :for="'min-'+i" class="span-min">@lang('Mínimo aceptado')</label>
            <input type="number" name="min[]" class="form-control" pattern="[0-9]" min="0" max="999999999" step="1"
                v-model="row.min" :id="'min-'+i" @keyup="isMinMaxValidationValid(i)">
            <small class="form-text text-muted"><i class="fas fa-info-circle"></i> @lang('Mínimo de caracteres aceptados para la respuesta')</small>
        </div>

        {{-- campo maximo permitido --}}
        <div class="form-group" data-toggle="tooltip" title="@lang('Máximo de caracteres permitido')">
            <label :for="'max-'+i" class="span-max">@lang('Máximo permitido')</label>
            <input type="number" name="max[]" class="form-control" pattern="[0-9]" min="0" max="999999999" step="1"
                v-model="row.max" :id="'max-'+i" @keyup="isMinMaxValidationValid(i)">
            <small class="form-text text-muted"><i class="fas fa-info-circle"></i> @lang('Máximo de caracteres permitido para la respuesta')</small>
        </div>

        {{-- campo tipo de caraceteres aceptado --}}
        <div class="form-group" data-toggle="tooltip" title="@lang('Tipo de caracteres aceptado')">
            <label :for="'character_type-'+i" class="span-character_type">@lang('Tipo de carácter')</label>
            <select name="character_type[]" class="form-control" :id="'character_type-'+i" v-model="row.characterType">
                <option value="">@lang('Cualquiera')</option>
                @foreach ($characterTypes as $type)
                    <option value="{{ $type }}" :selected="row.characterType">
                        @include('dashboard.partials.form-data.partials.character-type', [
                        'type' => $type
                        ])
                    </option>
                @endforeach
            </select>
            <small class="form-text text-muted"><i class="fas fa-info-circle"></i> @lang('Un tipo de carácter específico aceptado para la respuesta')</small>
        </div>


        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <button type="button" class="btn btn-secondary" @click="closeModalValidationField(i)"
                :id="'btnCloseModal-'+i">@lang('Cerrar')</button>
            <button type="button" class="btn btn-primary" @click="validateFieldWithSpecificValidations(i)"
                :id="'btnValidateModal-'+i">@lang('Validar')</button>
        </template>

    </b-modal>
</template>

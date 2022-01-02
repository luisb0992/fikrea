<div class="form-row mb-4 template-form-data">
    <input id="input-type-{{$keyTemplate}}" type="hidden" name="type[]" value="{{ $template->type }}" class="input-type-{{ $keyTemplate }} input-type-all">
    <input id="input-template_number-{{$keyTemplate}}" type="hidden" name="template_number[]" value="{{ $template->template_number }}" class="input-template_number-{{ $keyTemplate }}">

    <div class="col-md-3">
        <div class="input-group" data-toggle="tooltip" data-placement="top" title="@lang('Nombre para el campo')">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <input type="checkbox" checked name="check_field[]" data-check-id="{{ $keyTemplate }}" class="input-check">
                </span>
                <span class="input-group-text span-field_name">@lang('Nombre')</span>
            </div>
            <input id="input-field_name-{{$keyTemplate}}" type="text" name="field_name[]" class="form-control input-field_name-{{ $keyTemplate }}" value="{{ $template->field_name }}">
        </div>
    </div>
    <div class="col-md-3">
        <div class="input-group" data-toggle="tooltip" data-placement="top" title="@lang('Texto o descripción del campo')">
            <div class="input-group-prepend">
                <span class="input-group-text span-field_text">@lang('Texto')</span>
            </div>
            <input id="input-field_text-{{$keyTemplate}}" type="text" name="field_text[]" class="form-control input-field_text-{{ $keyTemplate }}" value="{{ $template->field_text }}">
        </div>
    </div>
    <div class="col-md-2">
        <div class="input-group" data-toggle="tooltip" data-placement="top" title="@lang('Mínimo de caracteres aceptado')">
            <div class="input-group-prepend">
                <span class="input-group-text span-min">@lang('Min')</span>
            </div>
            <input id="input-min-{{$keyTemplate}}" type="number" name="min[]" class="form-control input-min-{{ $keyTemplate }}" value="{{ $template->min }}" pattern="[0-9]" min="0" max="999999999" step="1">
        </div>
    </div>
    <div class="col-md-2">
        <div class="input-group" data-toggle="tooltip" data-placement="top" title="@lang('Máximo de caracteres aceptado')">
            <div class="input-group-prepend">
                <span class="input-group-text span-max">@lang('Max')</span>
            </div>
            <input id="input-max-{{$keyTemplate}}" type="number" name="max[]" class="form-control input-max-{{ $keyTemplate }}" value="{{ $template->max }}" pattern="[0-9]" min="0" max="999999999" step="1">
        </div>
    </div>
    <div class="col-md-2">
        <div class="input-group" data-toggle="tooltip" data-placement="top" title="@lang('Tipo de carácter aceptado')">
            <div class="input-group-prepend">
                <label class="input-group-text span-character_type">@lang('Tipo')</label>
            </div>
            <select name="character_type[]" class="custom-select input-character_type-{{ $keyTemplate }}" id="input-character_type-{{$keyTemplate}}">
                <option value="" selected>@lang('Cualquiera')</option>
                @foreach ($characterTypes as $type)
                    <option value="{{ $type }}">
                        @include('dashboard.partials.form-data.partials.character-type', [
                            'type' => $type
                        ])
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
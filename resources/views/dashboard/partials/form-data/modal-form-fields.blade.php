<template>
    <b-modal id="modal-info-form-template-{{ $key }}" size="lg">

        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <h5 class="modal-title">@lang('Plantilla de formulario :textType', ['textType' => $textType])</h5>
        </template>

        {{-- El contenido de la modal --}}
        <h5 class="text-muted">
            @lang('Está viendo un ejemplo de la plantilla :textType', ['textType' => $textType])
        </h5>

        <hr>

        {{-- Datos a mostrar en el modal referente a la plantilla de formulario de datos
                    Tanto plantillas personales como del sistema se muestran a continuacion ... --}}
        <form>
            <fieldset disabled>
                <div class="form-row">
                    @foreach ($templates as $keyTemplate => $template)
                        @if ($template->type == $enumType)
                            <div class="col-md-6 mb-2">
                                <label>{{ $template->field_name }}</label>
                                <input type="text"
                                    class="form-control"
                                    data-span-id="{{ $keyTemplate }}"

                                    {{-- muestra un pequeño ejemplo si no existe algun valor para mostrar en el campo,
                                                    este ejemplo puede llegar a ser el mismo nombre del campo como valor --}}
                                    @if ($template->field_text)
                                        value="{{ $template->field_text }}"
                                    @else
                                        value="@lang('Indique :text', ['text' => $template->field_name])"
                                    @endif>
                            </div>
                        @endif
                    @endforeach
                </div>
            </fieldset>
        </form>
        {{-- /Datos a mostrar en modal --}}

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <button type="button" class="btn btn-secondary"
                @click="$bvModal.hide('modal-info-form-template-{{ $key }}')">&times; @lang('Cerrar')</button>
        </template>

    </b-modal>
</template>

{{-- Datos a mostrar en el modal referente a la plantilla de formulario de datos
                    Tanto plantillas personales como del sistema se muestran a continuacion ... --}}
<form id="form-template-{{ $key }}" class="d-none">
    <fieldset disabled>
        <div class="form-row">
            @foreach ($templates as $keyTemplate => $template)
                @if ($template->type == $enumType)

                    <span id="span-type-{{ $key }}" class="d-none">{{ $template->type }}</span>
                    <span id="span-template_number-{{ $key }}"
                        class="d-none">{{ $template->template_number }}</span>

                    <div class="col-md-6 mb-2">
                        <label>{{ $template->field_name }}</label>
                        <input type="text"
                            class="form-control"
                            data-span-id="{{ $keyTemplate }}"

                            {{-- muestra un pequeño ejemplo si no existe algun valor para mostrar en el campo,
                                    este ejemplo puede llegar a ser el mismo nombre del campo como valor --}}
                            @if ($template->field_text)
                                value="{{ $template->field_text }}"
                            @else
                                value="@lang('Indique :text', ['text' => $template->field_name])"
                            @endif>

                        <span id="span-field_name-{{ $keyTemplate }}"
                            class="d-none">{{ $template->field_name }}</span>
                        <span id="span-field_text-{{ $keyTemplate }}"
                            class="d-none">{{ $template->field_text }}</span>
                        <span id="span-min-{{ $keyTemplate }}" class="d-none">{{ $template->min }}</span>
                        <span id="span-max-{{ $keyTemplate }}" class="d-none">{{ $template->max }}</span>
                        <span id="span-character_type-{{ $keyTemplate }}"
                            class="d-none">{{ $template->character_type }}</span>
                    </div>
                @endif
            @endforeach
        </div>
    </fieldset>
</form>
{{-- /Datos a mostrar en modal --}}

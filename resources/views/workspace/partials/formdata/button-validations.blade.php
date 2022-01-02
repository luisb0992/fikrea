<div class="row no-gutters">

    {{-- para mostrar una ayuda tipo leyenda --}}
    @if ($input->min || $input->max || $input->character_type)
        <div class="col-12 col-md-auto mb-2 mr-2 d-none d-md-block">
            <b-button id="popover-info-{{ $input->id }}" variant="info">
                <i class="fas fa-info-circle"></i>
            </b-button>
            <b-popover target="popover-info-{{ $input->id }}">
                <template #title>
                    <h5>
                        @lang('Leyenda')
                    </h5>
                </template>
                <div class="row">
                    <div class="col-12">
                        <div class="card w-100 p-2">
                            <p class="mb-1 font-weight-bold">
                                @lang('Mínimo')
                                <small class="text-muted text-right">
                                    <span class="badge badge-info">{{ $input->min }}</span>
                                </small>
                            </p>
                            <p class="mb-1 mt-1">@lang('Mínimo de caracteres
                                aceptado para la validación')</p>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card w-100 p-2">
                            <p class="mb-1 font-weight-bold">
                                @lang('Máximo')
                                <small class="text-muted text-right">
                                    <span class="badge badge-info">{{ $input->max }}</span>
                                </small>
                            </p>
                            <p class="mb-1 mt-1">@lang('Máximo de caracteres
                                permitido para la validación')</p>
                        </div>
                    </div>
                    <div class="col-12 mb-2">
                        <div class="card w-100 p-2">
                            <p class="mb-1 font-weight-bold">@lang('Tipo')</p>
                            <p class="mb-1 mt-1">@lang('El tipo de carácter
                                aceptado para la validación')</p>
                        </div>
                    </div>
                </div>
            </b-popover>
        </div>
    @endif

    {{-- para validar la cantidad minima de caracteres aceoptados --}}
    @if ($input->min)
        <div class="col-12 col-md-auto mb-2 mr-2">
            <button type="button" class="btn btn-outline-secondary btn-block" data-toggle="tooltip"
                title="@lang('Debe ser del mínimo de caracteres aceptado')" id="btn-min-{{ $input->id }}">
                <strong>@lang('Mínimo'):</strong>
                <span class="badge badge-light badge-pill text-dark"
                    id="span-min-{{ $input->id }}">{{ $input->min }}</span>
            </button>
        </div>
    @endif

    {{-- para validar la cantidad maxima de caracteres permitidos --}}
    @if ($input->max)
        <div class="col-12 col-md-auto mb-2 mr-2">
            <button type="button" class="btn btn-outline-secondary btn-block" data-toggle="tooltip"
                title="@lang('Debe ser del máximo de caracteres permitido')" id="btn-max-{{ $input->id }}">
                <strong>@lang('Máximo'):</strong>
                <span class="badge badge-light badge-pill text-dark"
                    id="span-max-{{ $input->id }}">{{ $input->max }}</span>
            </button>
        </div>
    @endif

    {{-- para validar el tipo de caracter --}}
    @if ($input->character_type)
        <div class="col-12 col-md-auto mb-2 mr-2">
            <button type="button" class="btn btn-outline-secondary btn-block" data-toggle="tooltip"
                title="@lang('Solo debe ser del tipo de carácter solicitado')"
                id="btn-character_type-{{ $input->id }}">
                <strong>@lang('Tipo'):</strong>
                <span class="badge badge-light badge-pill text-dark" id="span-character_type-{{ $input->id }}"
                    data-type="{{ $input->character_type }}">
                    @include('dashboard.partials.form-data.partials.character-type',[
                    'type' => $input->character_type
                    ])
                </span>
            </button>
        </div>
    @endif

    {{-- si no hay validacion alguna --}}
    @if (!$input->min && !$input->max && !$input->character_type)
        <div class="col-12 col-md-auto mb-2 mr-2">
            <button type="button" class="btn btn-outline-secondary btn-block">
                @lang('Cualquier carácter es válido')
            </button>
        </div>
    @endif
</div>

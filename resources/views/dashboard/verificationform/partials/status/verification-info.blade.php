<div class="col-md-12 mt-4">
    <h5>
        <i class="fas fa-info text-info"></i>
        @lang('Información de la verificación de datos')
    </h5>
</div>

<div class="col-md-12 mb-4">
    <div class="main-card card">
        <div class="card-body">

            {{-- Datos principales --}}
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="name" class="bold">@lang('Nombre')</label>
                    <input id="name" type="text" value="{{ $verificationForm->formatname }}" class="form-control"
                        disabled />
                </div>

                <div class="form-group col-md-10">
                    <label for="name" class="bold">@lang('Tipo de formulario')</label>
                    @switch ($verificationForm->fieldsRow()->first()->type)
                        @case(\App\Enums\FormType::PARTICULAR_FORM)
                            <input id="name" type="text" class="form-control" disabled
                                value="@lang('Formulario particular')" />
                        @break
                        @case(\App\Enums\FormType::BUSINESS_FORM)
                            <input id="name" type="text" class="form-control" disabled
                                value="@lang('Formulario empresarial')" />
                        @break
                    @endswitch
                </div>

                <div class="form-group col-md-2">
                    <label for="created" class="bold">@lang('Creado')</label>
                    <div class="input-group">
                        <input id="created" type="text" value="@date($verificationForm->created_at)"
                            class="form-control" disabled />
                        <div class="input-group-prepend">
                            <i class="btn btn-secondary fa fa-calendar"></i>
                        </div>
                    </div>
                </div>

                <div class="form-group col-md-12">
                    <label for="comment" class="bold">@lang('Comentarios')</label>
                    <textarea class="form-control" id="comment" rows="4" disabled>{!! $verificationForm->formatcomment !!}</textarea>
                </div>
            </div>

            {{-- Lista de filas de inputs requeridos en la verificación --}}
            <div class="row">
                <div class="col-md-12 mt-4">
                    <label for="documents" class="bold">@lang('Datos requeridos')</label>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th class="text-nowrap">@lang('Concepto que pregunta')</th>
                                    <th class="text-nowrap">@lang('Respuesta a verificar')</th>
                                    <th>@lang('Validaciones a realizar')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($verificationForm->fieldsRow as $row)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td data-label="@lang('Pregunta')">
                                            <span class="text-info">{{ $row->field_name }}</span>
                                        </td>
                                        <td data-label="@lang('Respuesta')">
                                            <span>{{ $row->formatfieldtext }}</span>
                                        </td>
                                        <td data-label="@lang('Validaciones')">

                                            {{-- para validar la cantidad minima de caracteres aceoptados --}}
                                            @if ($row->min)

                                                <button type="button" class="btn btn-outline-secondary">
                                                    <strong>@lang('Mínimo'):</strong>
                                                    <span class="badge badge-light badge-pill text-dark"
                                                        id="span-min-{{ $row->id }}">{{ $row->min }}</span>
                                                </button>

                                            @endif

                                            {{-- para validar la cantidad maxima de caracteres permitidos --}}
                                            @if ($row->max)

                                                <button type="button" class="btn btn-outline-secondary">
                                                    <strong>@lang('Máximo'):</strong>
                                                    <span class="badge badge-light badge-pill text-dark"
                                                        id="span-max-{{ $row->id }}">{{ $row->max }}</span>
                                                </button>

                                            @endif

                                            {{-- para validar el tipo de carácter --}}
                                            @if ($row->character_type)

                                                <button type="button" class="btn btn-outline-secondary">
                                                    <strong>@lang('Tipo'):</strong>
                                                    <span class="badge badge-light badge-pill text-dark">
                                                        @include('dashboard.partials.form-data.partials.character-type',[
                                                        'type' => $row->character_type
                                                        ])
                                                    </span>
                                                </button>

                                            @endif

                                            {{-- si no hay validacion alguna --}}
                                            @if (!$row->min && !$row->max && !$row->character_type)

                                                <button type="button" class="btn btn-outline-secondary">
                                                    @lang('Cualquier carácter es valido')
                                                </button>

                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th class="text-nowrap">@lang('Concepto que pregunta')</th>
                                    <th class="text-nowrap">@lang('Respuesta a verificar')</th>
                                    <th>@lang('Validaciones a realizar')</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            {{-- /Lista de filas de inputs requeridos en la verificación --}}

            {{-- Datos modificados dentro del formulariod e datos --}}
            <div class="row">
                <div class="col-md-12 mt-4">
                    <label class="bold">@lang('Datos modificados')</label>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>@lang('Respuesta a verificar')</th>
                                    <th>@lang('Respuesta verificada')</th>
                                    <th>@lang('Modificado')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($verificationForm->fieldsRow as $keyRow => $row)
                                    @if ($row->formDataBackup)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $row->formDataBackup->old_field_text }}</td>
                                            <td>{{ $row->formDataBackup->new_field_text }}</td>
                                            <td>@datetime($row->formDataBackup->created_at)</td>
                                        </tr>
                                    @else
                                        @if ($keyRow === 0)
                                            <tr>
                                                <td colspan="4">@lang('Ningun campo ha sido modificado')</td>
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('Respuesta a verificar')</th>
                                    <th>@lang('Respuesta verificada')</th>
                                    <th>@lang('Modificado')</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            {{-- /Datos modificados dentro del formulariod e datos --}}

            {{-- Comenatarios --}}
            @if ($verificationForm->feedback)
                <div class="row">
                    <div class="col-md-12 mt-4">
                        <div class="alert alert-primary text-justify" role="alert">
                            <div class="alert-heading mb-2 border-bottom border-primary font-weight-bold">
                                <i class="fas fa-comment"></i> @lang('Comentario del usuario :user', ['user' =>
                                $verificationForm->noCreatorSigner()])
                            </div>
                            <p>
                                <small class="font-italic">"{{ $verificationForm->feedback->comment }}"</small>
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            {{-- /Comenatarios --}}

        </div>
    </div>
</div>

{{-- Validaciones de verificación de datos
     Esta validacion es asignada por el usuario de la app y validada por el usuario externo
     los campos del formulario son agrupados por el firmante para asi mostrar la informacion de manera correcta --}}
@if ($document->hasADataVerificationPerformed())

    <p>
    <table>
        <thead>
            <tr>
                <th colspan="2">@lang('Validaciones mediante verificación de datos')</th>
            </tr>
        </thead>
    </table>
    </p>

    @foreach ($document->signers as $signer)
        @foreach ($signer->formdata->groupBy('signer_id') as $keyGroup => $groupDataForm)
            @foreach ($groupDataForm as $dataForm)
                @php
                    $type = $dataForm->type;
                    $ip = $dataForm->ip;
                    $userAgent = $dataForm->user_agent;
                    $latitude = $dataForm->latitude;
                    $longitude = $dataForm->longitude;
                    $device = $dataForm->device;
                    $created = $dataForm->created_at;
                @endphp
            @endforeach
            <br />
            <table>
                <thead>
                    <tr>
                        <th colspan="2">
                            {{ $signer->name }} {{ $signer->lastname }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>@lang('Validante')</th>
                        <td> {{ $signer->name }} {{ $signer->lastname }} {{ $signer->dni }} {{ $signer->email }}
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Tipo de Formulario')</td>
                        <td>
                            <div>
                                @switch ($type)
                                    @case(\App\Enums\FormType::PARTICULAR_FORM)
                                        @lang('Formulario Particular')
                                    @break
                                    @case(\App\Enums\FormType::BUSINESS_FORM)
                                        @lang('Formulario Empresarial')
                                    @break
                                @endswitch
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Dirección IP')</td>
                        <td>
                            <div>
                                {{ $ip }}
                            </div>
                            <div>
                                @hostname($ip)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Sistema Utilizado')</td>
                        <td>
                            @if ($device)
                                <div>
                                    @userdevice($device)
                                </div>
                            @endif
                            @useragent($userAgent)
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Ubicación')</td>
                        <td>
                            <div>
                                {{ $latitude }} {{ $longitude }}
                            </div>
                            @if ($latitude && $longitude)
                                <div>
                                    <a
                                        href="https://www.google.com/maps/search/?api=1&query={{ $latitude }},{{ $longitude }}">
                                        @lang('Ver en Google Maps')
                                    </a>
                                </div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Fecha de Acreditación')</td>
                        <td>@datetime($created)</td>
                    </tr>
                </tbody>
            </table>
            <br />
            <table>
                <thead>
                    <tr>
                        <th colspan="4">@lang('Datos solicitados')</th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th>@lang('Pregunta')</th>
                        <th>@lang('Respuesta')</th>
                        <th>@lang('Validaciones')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groupDataForm as $keyData => $dataForm)
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $dataForm->field_name }}</td>
                            <td>{{ $dataForm->formatfieldtext }}</td>
                            <td>
                                {{-- para validar la cantidad minima de caracteres aceoptados --}}
                                @if ($dataForm->min)

                                    <strong>@lang('Mínimo'):</strong>
                                    <span>{{ $dataForm->min }}</span>
                                    <hr>

                                @endif

                                {{-- para validar la cantidad maxima de caracteres permitidos --}}
                                @if ($dataForm->max)

                                    <strong>@lang('Máximo'):</strong>
                                    <span>{{ $dataForm->max }}</span>
                                    <hr>

                                @endif

                                {{-- para validar el tipo de carácter --}}
                                @if ($dataForm->character_type)

                                    <strong>@lang('Tipo'):</strong>
                                    <span>
                                        @include('dashboard.partials.form-data.partials.character-type',[
                                        'type' => $dataForm->character_type
                                        ])
                                    </span>

                                @endif

                                {{-- si no hay validacion alguna --}}
                                @if (!$dataForm->min && !$dataForm->max && !$dataForm->character_type)

                                    @lang('Cualquier carácter es valido')

                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <br>
            <table>
                <thead>
                    <tr>
                        <th colspan="4">@lang('Datos modificados')</th>
                    </tr>
                </thead>
            </table>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('Respuesta a verificar')</th>
                        <th>@lang('Respuesta verificada')</th>
                        <th>@lang('Modificado')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groupDataForm as $keyRow => $row)
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
            </table>
        @endforeach

        {{-- comentario de la validacion --}}
        @if ($signer->getIfCommentExists(\App\Enums\ValidationType::FORM_DATA_VERIFICATION))
            <p>
            <table>
                <thead>
                    <tr>
                        <th>@lang('Comentario')</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <p>
                                <small>"{{ $signer->getIfCommentExists(\App\Enums\ValidationType::FORM_DATA_VERIFICATION) }}"</small>
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
            </p>
        @endif
        {{-- /comentario de la validacion --}}
    @endforeach

    {{-- Próxima página --}}
    <div class="break"></div>
    {{-- / Próxima página --}}
@endif
{{-- /Validaciones de formulario d datos --}}

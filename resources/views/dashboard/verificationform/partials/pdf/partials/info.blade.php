{{-- Información de la verificación --}}
<p>
<table>
    <thead>
        <tr>
            <th colspan="2">@lang('Información de la verificación')</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>@lang('Nombre')</td>
            <td>{{ $verificationForm->formatname }}</td>
        </tr>
        <tr>
            <td>@lang('Tipo de formulario')</td>
            <td>
                @switch ($verificationForm->fieldsRow()->first()->type)
                    @case(\App\Enums\FormType::PARTICULAR_FORM)
                        @lang('Formulario particular')
                    @break
                    @case(\App\Enums\FormType::BUSINESS_FORM)
                        @lang('Formulario empresarial')"
                    @break
                @endswitch
            </td>
        </tr>
        <tr>
            <td>@lang('Comentarios')</td>
            <td>{{ $verificationForm->formatcomment }}</td>
        </tr>
        <tr>
            <td>@lang('Creada')</td>
            <td>@datetime($verificationForm->created_at)</td>
        </tr>
    </tbody>
</table>
</p>

{{-- datos del formulario --}}
<p>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>@lang('Concepto que pregunta')</th>
            <th>@lang('Respuesta a verificar')</th>
            <th>@lang('Validaciones a realizar')</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($verificationForm->fieldsRow as $row)
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td>{{ $row->field_name }}</td>
                <td>{{ $row->formatfieldtext }}</td>
                <td>

                    {{-- para validar la cantidad minima de caracteres aceoptados --}}
                    @if ($row->min)

                        <strong>@lang('Mínimo'):</strong>
                        <span class="text-dark">{{ $row->min }}</span>
                        <hr>

                    @endif

                    {{-- para validar la cantidad maxima de caracteres permitidos --}}
                    @if ($row->max)

                        <strong>@lang('Máximo'):</strong>
                        <span class="text-dark">{{ $row->max }}</span>
                        <hr>

                    @endif

                    {{-- para validar el tipo de carácter --}}
                    @if ($row->character_type)

                        <strong>@lang('Tipo'):</strong>
                        <span class="text-dark">
                            @include('dashboard.partials.form-data.partials.character-type',[
                            'type' => $row->character_type
                            ])
                        </span>

                    @endif

                    {{-- si no hay validacion alguna --}}
                    @if (!$row->min && !$row->max && !$row->character_type)

                        @lang('Cualquier carácter es valido')

                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
</p>

{{-- Próxima página --}}
<div class="break"></div>
{{-- / Próxima página --}}

{{-- Datos modificados --}}
<p>
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
</table>
</p>
{{-- / Información de la verificación --}}

{{--  Comenatarios  --}}
@if($verificationForm->feedback)
<p>
    <table>
        <thead>
            <tr><th>@lang('Comentario del usuario :user', ['user' => $verificationForm->noCreatorSigner()])</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <p>
                        <small>"{{ $verificationForm->feedback->comment }}"</small>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</p>
@endif
{{--  /Comenatarios  --}}

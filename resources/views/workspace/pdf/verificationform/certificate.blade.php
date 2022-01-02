@extends('common.layouts.certificate')

@section('page-header')
    <div>
        @lang('Informe acreditativo de un proceso para certificación de datos') #{{ $verificationForm->id }}
        [ <span class="bold">@locale</span> ]
    </div>
@endsection

@section('document-guid')
    {{-- Identificador del proceso que se certifica --}}
    #{{ $verificationForm->id }}-{{ $verificationForm->formatname }}
    {{-- /Identificador del proceso que se certifica --}}
@endsection

@section('document-goals')

    {{-- Objetivo del documento --}}
    @lang('El objeto de este certificado es proporcionar la información necesaria sobre la identidad de los firmantes y los
    datos verificados, durante el proceso de respuesta a la certificación de datos.')

    @lang(':app es un sistema de firma digital avanzada. Se puede acceder a la plataforma a través de la dirección <a
        href=":url">:url</a>', [
    'app' => config('app.name'),
    'url' => config('app.url'),
    ])
    {{-- /Objetivo del documento --}}

@endsection

@section('document-data')

    {{-- Información de la verificación --}}
    <p>
    <table>
        <thead>
        <tr>
            <th colspan="2">@lang('Información de la verificación de datos')</th>
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
                <td>{{ $row->field_text ?? '---' }}</td>
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
    {{-- / Información de la verificación --}}

    {{-- Próxima página --}}
    <div class="break"></div>
    {{-- / Próxima página --}}

    {{-- Listado de Personas que forman parte --}}
    <p>
    <table>
        <thead>
        <tr>
            <th colspan="2">@lang('Participantes en el proceso')</th>
        </tr>
        </thead>
        <tbody>

        {{-- Creador --}}
        <tr>
            <td>
                {{ $verificationForm->user->name }} {{ $verificationForm->user->lastname }}
                <div>
                    @if ($verificationForm->user->email)
                        <a
                                href="mailto:{{ $verificationForm->user->email }}">{{ $verificationForm->user->email }}</a>
                    @elseif ($verificationForm->user->phone)
                        <a
                                href="tel:{{ $verificationForm->user->phone }}">{{ $verificationForm->user->phone }}</a>
                    @endif
                </div>
            </td>
            <td>
                @lang('Solicitante')
            </td>
        </tr>
        {{-- Creador --}}

        {{-- Firmantes --}}
        @foreach ($verificationForm->signers as $signer)
            <tr>
                <td>
                    {{ $signer->name }} {{ $signer->lastname }}
                    <div>
                        @if ($signer->email)
                            <a href="mailto:{{ $signer->email }}">{{ $signer->email }}</a>
                        @elseif ($signer->phone)
                            <a href="tel:{{ $signer->phone }}">{{ $signer->phone }}</a>
                        @endif
                    </div>
                </td>
                <td>
                    @lang('Solicitado')
                </td>
            </tr>
        @endforeach
        {{-- /Firmantes --}}
        </tbody>
    </table>
    </p>
    {{-- /Listado de Personas que forman parte --}}

    {{--  Comenatarios  --}}
    @if($verificationForm->feedback)
        <p>
        <table>
            <thead>
            <tr>
                <th>@lang('Comentario del usuario :user', ['user' => $verificationForm->noCreatorSigner()])</th>
            </tr>
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

    {{-- Próxima página --}}
    <div class="break"></div>
    {{-- / Próxima página --}}

@endsection

@section('first-attachment-customized')
    <li class="mt">
        <x-certificates.certificate-attach-i :user="$verificationForm->user"
                                             :created_at="$verificationForm->created_at"></x-certificates.certificate-attach-i>
    </li>
@endsection

@extends('dashboard.layouts.mail')

@section('content')
    <p>
        Hola, {{ $creator->name }}:
    </p>

    {{-- Datos de la verificación de datos --}}
    <div>

        <p>
            @lang('Has solicitado una nueva certificación de datos con :app', ['app' => config('app.name')]):
        </p>

        <table class="table">
            <thead>
                <tr>
                    <th colspan="2" class="text-center">@lang('certificación de datos')</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-info">@lang('Nombre') :</td>
                    <td class="text-bold">
                        @if ($verificationForm->name)
                            {{ $verificationForm->name }}
                        @else
                            @lang('verificación de datos')
                        @endif
                    </td>
                </tr>
                @if ($verificationForm->comment)
                    <tr>
                        <td class="text-info">@lang('Comentarios') :</td>
                        <td>
                            @if ($verificationForm->comment)
                                {!! $verificationForm->comment !!}
                            @else
                                @lang('Sin comentarios')
                            @endif
                        </td>
                    </tr>
                @endif
                <tr>
                    <td class="text-info">@lang('Creada') :</td>
                    <td class="text-warning text-bold">@datetime($verificationForm->created_at)</td>
                </tr>
            </tbody>
        </table>
    </div>
    {{-- /Datos de la verificación de datos --}}

    {{-- Lista de Firmantes --}}
    <div>

        <p>
            @lang('Estos son los destinatarios a los que se ha notificado'):
        </p>

        <table class="table">
            <thead>
                <tr>
                    <th class="text-left">@lang('Nombre')</th>
                    <th class="text-center">@lang('Email')</th>
                    <th class="text-center">@lang('Teléfono')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($verificationForm->signers->filter(fn($signer) => !$signer->creator) as $signer)
                    <tr>
                        <td class="text-left">{{ $signer->name }} {{ $signer->lastname }}</td>
                        <td class="text-center">
                            @if ($signer->email)
                                <a href="mailto: {{ $signer->email }}">
                                    {{ $signer->email }}
                                </a>
                            @else
                                @lang('No posee email')
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($signer->phone)
                                <a href="tel: {{ $signer->phone }}">
                                    {{ $signer->phone }}
                                </a>
                            @else
                                @lang('No posee telefono')
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- /Lista de Firmantes --}}

    <p>
        @lang('Para realizar un seguimiento de la certificación realizada'):
    </p>

    <p class="text-center">
        <a class="btn btn-primary"
            href="@url(@route('dashboard.verificationform.status', ['id' => $verificationForm->id]))">
            @lang('Seguimiento de la certificación de datos')
        </a>
    </p>
@stop

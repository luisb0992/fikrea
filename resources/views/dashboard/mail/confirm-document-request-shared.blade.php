@extends('dashboard.layouts.mail')

@section('content')
    <p>
        Hola, {{$creator->name}}:
    </p>

    {{-- Datos del documento --}}
    <div>

        <p>
            @lang('Has solicitado documentos con :app', ['app' => config('app.name')]):
        </p>

        <table class="table">
            <thead>
                <tr>
                    <th colspan="2" class="text-center">@lang('Solicitud de Documentos')</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-info">@lang('Nombre') :</td>
                    <td class="text-bold">{{$request->name}}</td>
                </tr>
                @if ($request->comment)
                <tr>
                    <td class="text-info">@lang('Comentarios') :</td>
                    <td>{!!$request->comment!!}</td>
                </tr>
                @endif
                <tr>
                    <td class="text-info">@lang('Creada') :</td>
                    <td class="text-warning text-bold">@datetime($request->created_at)</td>
                </tr>
                </tbody>
        </table>
    </div>
    {{--/Datos del documento --}}

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
                @foreach($request->signers->filter(fn ($signer) => !$signer->creator) as $signer)
                <tr>
                    <td class="text-left">{{$signer->name}} {{$signer->lastname}}</td>
                    <td class="text-center">
                        @if ($signer->email)
                        <a href="mailto: {{$signer->email}}">
                            {{$signer->email}}
                        </a>
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($signer->phone)
                        <a href="tel: {{$signer->phone}}">
                            {{$signer->phone}}
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{--/Lista de Firmantes --}}

    <p>
        @lang('Para realizar un seguimiento de la solicitud realizada'):
    </p>

    <p class="text-center">
        <a class="btn btn-primary" 
            href="@url(@route('dashboard.document.request.status', ['id' => $request->id]))">
            @lang('Seguimiento de ls Solicitud')
        </a>
    </p>
@stop

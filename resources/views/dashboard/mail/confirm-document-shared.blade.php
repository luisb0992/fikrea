@extends('dashboard.layouts.mail')

@section('content')
    <p>
        Hola, {{$creator->name}}:
    </p>

    {{-- Datos del documento --}}
    <div>

        <p>
            @lang('Has compartido un nuevo documento en :app', ['app' => config('app.name')]):
        </p>

        <table class="table">
            <thead>
                <tr>
                    <th colspan="2" class="text-center">@lang('Documento')</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-info">@lang('Nombre') :</td>
                    <td class="text-bold">{{$document->name}}</td>
                </tr>
                @if ($document->comment)
                <tr>
                    <td class="text-info">@lang('Comentarios') :</td>
                    <td>{{$document->comment}}</td>
                </tr>
                @endif
                <tr>
                    <td class="text-info">@lang('Tamaño') :</td>
                    <td>@filesize($document->size)</td>
                </tr>
                <tr>
                    <td class="text-info">@lang('Creado') :</td>
                    <td class="text-warning text-bold">@datetime($document->created_at)</td>
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
                @foreach($document->signers->filter(fn ($signer) => !$signer->creator) as $signer)
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
        @lang('Para realizar un seguimiento de las validaciones realizada por los usuarios para este documento'):
    </p>

    <p class="text-center">
        <a class="btn btn-primary" 
            href="@url(@route('dashboard.document.status', ['id' => $document->id]))">
            @lang('Estado del Documento')
        </a>
    </p>
@stop

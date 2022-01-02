@extends('common.layouts.certificate')

@section('title')
@lang('Certificado de solicitud de documentos')
@endsection

@section('page-header')
    <div>
        @lang('Informe acreditativo de proceso de solicitud de documentos') #{{$request->id}}
        [ <span class="bold">@locale</span> ]
    </div>
@endsection

@section('document-guid')
    {{-- Identificador del proceso que se certifica --}}
    #{{$request->id}}-{{$request->name}}-{{$request->comment}}
    {{-- /Identificador del proceso que se certifica --}}
@endsection

@section('document-goals')
    {{-- Objetivo del documento--}}
    @lang('El objeto de este certificado es proporcionar la información necesaria sobre la identidad de los firmantes y los documentos aportados, durante el proceso de respuesta a la solicitud de documentos.')

    @lang(':app es un sistema de firma digital avanzada. Se puede acceder a la plataforma a través de la dirección <a href=":url">:url</a>',
        [
            'app' => config('app.name'),
            'url' => config('app.url'),
        ]
    )
    {{-- /Objetivo del documento--}}
@endsection

@section('document-data')
    {{-- Los datos y el histórico del proceso --}}
    @include('dashboard.requests.pdf.certificate-data')
    {{-- /Los datos y el histórico del proceso --}}
@endsection

@section('document-attachs')
    {{-- Anexos --}}
    <div class="page">
        <h3>5) @lang('Anexos')</h3>
        <ul>
            <li class="mt">
                <x-certificates.certificate-attach-i :user="$request->user"
                                                     :created_at="$request->created_at">
                    <p>
                        @lang('La descarga de todos los documentos que se aportaron durante este proceso pueden ser descargados suministrando las credenciales de acceso a la aplicación en la dirección')
                        :
                    </p>
                    <p>
                        <a class="btn btn-success square"
                           href="@route('dashboard.document.request.download.files', ['id' => $request->id])">
                            @url(@route('dashboard.document.request.download.files', ['id' => $request->id]))
                        </a>
                    </p>
                </x-certificates.certificate-attach-i>
            </li>
            <li class="mt">
                <x-certificates.certificate-attach-ii></x-certificates.certificate-attach-ii>
            </li>
            <li class="mt">
                <x-certificates.certificate-attach-iii></x-certificates.certificate-attach-iii>
            </li>
            <li class="mt">
                <x-certificates.certificate-attach-iv></x-certificates.certificate-attach-iv>
            </li>
        </ul>
    </div>
    {{-- /Anexos --}}
@endsection

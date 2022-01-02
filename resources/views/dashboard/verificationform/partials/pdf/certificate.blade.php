@extends('common.layouts.certificate')

@section('title')
@lang('Certificado de proceso de certificación de datos')
@endsection

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

    {{-- Los datos y el histórico del proceso --}}
    @include('dashboard.verificationform.partials.pdf.certificate-data')
    {{-- /Los datos y el histórico del proceso --}}

@endsection

@section('first-attachment-customized')
    <x-certificates.certificate-attach-i :user="$verificationForm->user"
                                         :created_at="$verificationForm->created_at"></x-certificates.certificate-attach-i>
@endsection

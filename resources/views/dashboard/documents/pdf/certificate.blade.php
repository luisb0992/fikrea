@extends('common.layouts.certificate')

@section('title')
@lang('Certificado de proceso de validación')
@endsection

@section('page-header')
    <div>
        @lang('Informe acreditativo de proceso de firma digital') #{{$document->guid}}
        [ <span class="bold">@locale</span> ]
    </div>
@endsection

@section('document-guid')
    {{-- Identificador del documento que se certifica --}}
    #{{$document->guid}}
    {{-- /Identificador del documento que se certifica --}}
@endsection

@section('document-goals')
    {{-- Objetivo del documento--}}
    @lang('El objeto de este certificado es proporcionar la información necesaria sobre la identidad de los firmantes y acreditar que el proceso de firma digital se realizó con las debidas garantías que reconoce la ley.')

    @lang(':app es un sistema de firma digital avanzada. Se puede acceder a la plataforma  a través de la dirección <a href=":url">:url</a>',
        [
            'app' => config('app.name'),
            'url' => config('app.url'),
        ]
    )
    {{-- /Objetivo del documento--}}
@endsection

@section('document-data')
    {{-- Los datos y el histórico del proceso --}}
    @include('dashboard.documents.pdf.certificate-data')
    {{-- /Los datos y el histórico del proceso --}}
@endsection

@section('first-attachment-customized')
    <x-certificates.certificate-attach-i :user="$document->user" :created_at="$document->created_at">
        <p>
            @lang('Su descarga puede ser realizada suministrando las credenciales de acceso a la aplicación en la dirección')
            :
        </p>
        <p>
            <a href="@url(@route('dashboard.document.download', ['id' => $document->id]))" target="_blank">
                @url(@route('dashboard.document.download', ['id' => $document->id]))
            </a>
        </p>
    </x-certificates.certificate-attach-i>
@endsection

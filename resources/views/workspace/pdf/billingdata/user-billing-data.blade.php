@extends('common.layouts.certificate')

@section('page-header')
    <div>
        @lang('Descarga de datos de facturacion del usuario <b>:user</b>', ['user' => $company->user->getFullNameUser()])
        [ <span class="bold">@locale</span> ]
    </div>
@endsection

@section('document-guid')
    {{-- Identificador del proceso que se certifica --}}
    {{ $company->id }}-{{ $company->user->id }}-{{ date('dmYhis') }}
    {{-- /Identificador del proceso que se certifica --}}
@endsection

@section('document-goals')
    {{-- Objetivo del documento --}}
    @lang('El objeto de este certificado es proporcionar la información necesaria sobre la identidad del usuario.')

    @lang(':app es un sistema de firma digital avanzada. Se puede acceder a la plataforma a través de la dirección <a
        href=":url">:url</a>',[
    'app' => config('app.name'),
    'url' => config('app.url'),
    ])
    {{-- /Objetivo del documento --}}
@endsection

{{-- --------------------- --}}
{{-- Los datos del usuario --}}
{{-- --------------------- --}}
@section('document-data')
    @include('workspace.pdf.billingdata.data.user-data')
@endsection
{{-- --------------------- --}}
{{--/Los datos del usuario --}}
{{-- --------------------- --}}

{{--  anexos finales  --}}
@section('first-attachment-customized')
    {{-- FIXME: ¿Este certificado no lleva el Anexo I --}}
@endsection

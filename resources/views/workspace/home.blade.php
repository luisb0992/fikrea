@extends('workspace.layouts.main')

{{-- Título de la Página --}}
@section('title', 'WorkSpace')

    {{-- Css Personalizado --}}
@section('css')
    <link rel="stylesheet" href="@mix('assets/css/workspace/home.css')" />
@stop

@section('content')

    {{-- El encabezado con la ayuda para la página --}}
@section('help')
    @include('workspace.home.help')
@stop

<div id="app" class="col-md-12">

    {{-- Modales necesarias --}}
    @include('workspace.modals.privacity-policy')
    {{-- /Modales necesarias --}}

    {{-- Identificación del usuario creator/autor del documento --}}
    <x-user-info :creator="$creator"></x-user-info>
    {{-- /Identificación del usuario creator/autor del documento --}}

    {{-- Progreso de la tarea cuando se trata de la validación de un documento --}}
    @include('workspace.home.process-progress')
    {{-- /Progreso de la tarea --}}


    <ul class="block list-group row col-md-12">

        {{-- Si se está validando un documento,
            para cada tipo de validación solicitada para el firmante, se muestra una opción --}}

        @if ($signer->validations())

            {{-- Listado de las validaciones del firmante --}}
            @include('workspace.home.validations')
            {{-- /Listado de las validaciones del firmante --}}

        @elseif ($signer->request())

            {{-- La solicitud de documentos --}}
            @include('workspace.home.document-request')
            {{-- /La solicitud de documentos --}}

        @elseif ($signer->verificationForm())

            {{-- La solicitud de documentos --}}
            @include('workspace.home.verification-form')
            {{-- /La solicitud de documentos --}}

        @endif
    </ul>

</div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
{{-- Manipulación de Cookies
    @link https://github.com/js-cookie/js-cookie --}}
<script src="https://cdn.jsdelivr.net/npm/js-cookie@rc/dist/js.cookie.min.js"></script>
<script src="@mix('assets/js/workspace/menu.js')"></script>
<script src="@mix('assets/js/workspace/home.js')"></script>
@stop

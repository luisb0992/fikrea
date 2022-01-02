@extends('workspace.layouts.main')

{{-- Título de la Página --}}
@section('title', 'WorkSpace')

{{-- Css Personalizado --}}
@section('css')
<link rel="stylesheet" href="@mix('assets/css/common/passport.css')" />
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @lang('Debe adjuntar los documentos que acrediten su identidad')
    <div class="page-title-subheading">

        {{-- Sólo si se va a usar reconocimiento facial se necesita una foto frontal del usuario --}}
        @if ($useFacialRecognition)
        <div>
            @lang('Obtenga una foto frontal suya utilizando la cámara de su dispositivo')
        </div>
        @endif
        
        <div>
            @lang('Además tome una foto del documento por su anverso y reverso y pulse añadir para incorporarlo a la lista')
        </div>
    </div>
</div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')
@include('common.passport.passport')
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
{{-- Moment.js --}}
<script src="@asset('assets/js/libs/moment-with-locales.min.js')"></script>

{{-- Face api --}}
<script src="@asset('assets/js/workspace/face-api.min.js')"></script>

{{-- Tesseract.js --}}
@production
<script src='https://unpkg.com/tesseract.js@v2.1.0/dist/tesseract.min.js'></script>
@else
<script src="@asset('assets/js/libs/tesseract/tesseract.min.js')"></script>
@endproduction

{{-- Personalizado --}}
<script src="@mix('assets/js/common/passport.js')"></script>
@stop
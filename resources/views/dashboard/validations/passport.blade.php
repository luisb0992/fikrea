@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

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
            @lang('Obtenga una foto frontal suya')
        </div>
        @endif
        
        <div>
            @lang('Suba o tome una foto del documento por su anverso y reverso y pulse añadir para incorporarlo a la lista')
        </div>
    </div>
</div>
@stop

{{-- 
    Aquí incluímos el contenido de la página
--}}
@section('content')
@include('common.passport.passport')
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
{{-- Moment.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js"></script>

{{-- Face api --}}
<script src="@asset('assets/js/workspace/face-api.min.js')"></script>

{{-- Tesseract.js --}}
<script src='https://unpkg.com/tesseract.js@v2.1.0/dist/tesseract.min.js'></script>

<script src="@mix('assets/js/common/passport.js')"></script>
@stop
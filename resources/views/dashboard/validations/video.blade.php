@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@section('css')
<link rel="stylesheet" href="/assets/css/common/video.css" />
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @lang('Pulse el botón grabar para iniciar la grabación de video')
    <div class="page-title-subheading">
          @lang('Se le indica la locución de referencia que debe decir')
    </div>
</div>
@stop

{{-- 
    Aquí incluímos el contenido de la página
--}}
@section('content')
@include('common.video')
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
{{-- Moment.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js"></script>
<script src="@mix('assets/js/common/video.js')"></script>
@stop
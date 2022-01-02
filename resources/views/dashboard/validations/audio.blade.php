@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@section('css')
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @lang('Pulse el botón grabar para iniciar la grabación de audio')
    <div class="page-title-subheading">
          @lang('Se le indica la locución de referencia que debe decir')
    </div>
</div>
@stop

{{-- 
    Aquí incluímos el contenido de la página
--}}
@section('content')
@include('common.audio')
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
{{-- Moment.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js"></script>
<script src="@mix('assets/js/common/audio.js')"></script>
@stop
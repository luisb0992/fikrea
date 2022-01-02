@extends('backend.layouts.main')

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
    El título de la ayuda
    <div class="page-title-subheading">
        El texto de ayuda
    </div>
</div>
@stop

{{-- 
    Aquí incluímos el contenido de la página
--}}
@section('content')
<h1>Hello World</h1>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
@stop
@extends('landing.layouts.main')

{{-- Título de la Página --}}
@section('title', __('Página de Inicio'))


{{-- Etiquetas meta personalizadas --}}
@section('meta')
<meta property="og:title" content="{{$fileSharing->name}}" />
<meta property="og:description" content="@lang('Tu archivo está disponible para su descarga en :app', ['app' => config('app.name')])" />
<meta property="og:type" content="zip" />
<meta property="og:url" content="@current" />
<meta property="og:image" content="@asset('/assets/images/dashboard/logos/fikrea-medium-logo.png')" />
@stop

{{-- Css Personalizado --}}
@section('css')

{{-- Fontawesome 5
     
    @link https://fontawesome.com/icons?d=gallery    
--}}
<link href="@asset('assets/css/vendor/fontawesome/css/all.min.css')" rel="stylesheet" />

{{-- Css Personalizado --}}
<link rel="stylesheet" href="@mix('/assets/css/landing/share.css')" />

@stop

@section('body')

{{-- Menú de la aplicación --}}
@include('landing.sections.body.menu')

{{-- Carousel - Descarga de Archivo --}}
@include('landing.sections.body.carousel-download-file-set')

{{-- Pié de página --}}
@include('landing.sections.body.footer')

{{-- Loader --}}
@include('landing.sections.body.loader')

@stop

{{-- Scripts Personalizados --}}
@section('scripts')
@stop

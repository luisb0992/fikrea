@extends('workspace.layouts.main')

{{-- Título de la Página --}}
@section('title', 'WorkSpace')

{{-- Css Personalizado --}}
@section('css')
<link rel="stylesheet" href="@mix('assets/css/workspace/document-request.css')" />
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @lang('Debe renovar el documento que se le indica a continuación')
    <div class="page-title-subheading">
        @lang('Asegúrese de aportar un documento que no esté cerca de expirar').
    </div>
</div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')
<div v-cloak id="app" class="col-md-12">
    
{{-- Vista commun en caso de que el creador deba renovar un documento  --}}
@include('common.renew-file.renew-document-request-file')
{{-- /Vista commun en caso de que el creador deba renovar un documento  --}}

</div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
{{-- Moment.js --}}
<script src="@asset('assets/js/libs/moment-with-locales.min.js')"></script>
<script src="@mix('assets/js/workspace/renew-file.js')"></script>
@stop

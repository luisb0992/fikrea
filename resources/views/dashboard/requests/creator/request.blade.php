@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', Illuminate\Support\Facades\Lang::get('Aportando documentos'))

{{-- Css Personalizado --}}
@section('css')
<link rel="stylesheet" href="@asset('assets/css/vendor/vue/vue-treeselect.min.css')" />
<link rel="stylesheet" href="@mix('assets/css/workspace/document-request.css')" />
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @lang('Debe adjuntar los documentos que se le indican a continuación')
    <div class="page-title-subheading">
        @lang('Asegúrese de adjuntar al menos un archivo por cada documento solicitado')
    </div>
</div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')
<div v-cloak id="app" class="col-md-12">

    {{-- Se reutiliza la vista donde se aportan los documentos requeridos --}}
    @include('common.request.document-request')
    {{-- /Se reutiliza la vista donde se aportan los documentos requeridos --}}

</div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
{{-- Moment.js --}}
<script src="@asset('assets/js/libs/moment-with-locales.min.js')"></script>

{{-- Vue Treeselect --}}
<script src="@asset('assets/js/vue/vue-treeselect.umd.min.js')"></script>

<script src="@mix('assets/js/workspace/request.js')"></script>
@stop


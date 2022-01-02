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

    {{-- Se incluye vista commún en proceso de atención a solicitud de documentos --}}
    @include('common.request.document-request')
    {{-- / Se incluye vista commún en proceso de atención a solicitud de documentos --}}

</div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
<!-- Load Vue followed by BootstrapVue -->
<script src="@asset('assets/js/vue/bootstrap-vue.js')"></script>

{{-- Moment.js --}}
<script src="@asset('assets/js/libs/moment-with-locales.min.js')"></script>

{{-- Vue Treeselect --}}
<script src="@asset('assets/js/vue/vue-treeselect.umd.min.js')"></script>

<script src="@mix('assets/js/workspace/request.js')"></script>
@stop
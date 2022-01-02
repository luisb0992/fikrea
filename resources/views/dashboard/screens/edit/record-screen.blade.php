{{--
    Vista para hacer una grabación de pantalla
--}}
@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title')
    @lang('Workspace → Haciendo grabación de pantalla')
@endsection

{{-- Css Personalizado --}}
@section('css')
<link rel="stylesheet" href="@asset('assets/css/vendor/vue/vue-treeselect.min.css')" />
<link rel="stylesheet" href="@mix('assets/css/dashboard/screen.css')" />
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @lang('Creando archivo de video mediante grabación de su pantalla')
    <div class="page-title-subheading">
        <p>
            @lang('Mediante esta herramienta usted puede realizar una grabación de su pantalla para procesos externos').
            @lang('Por ejemplo para compartir determinada información, o mostrar el funcionamiento de alguna herramienta').
        </p>
        @lang('Al finalizar su grabación, asegúrese de editar el nombre y la ubicación de las grabaciones que ha realizado.')
    </div>
</div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')
<div v-cloak id="app" class="col-md-12">

    {{-- Las modals necesarias --}}
    @include('dashboard.modals.screens.edit-capture')
    {{-- /Las modals necesarias --}}

    {{-- Data que se le pasa a Vue --}}
    @include('dashboard.screens.edit.data')
    {{-- /Data que se le pasa a Vue --}}

    {{-- Botones de acción --}}
    @include('dashboard.screens.edit.action-buttons')
    {{-- /Botones de acción --}}

    {{-- Control de la captura de pantalla --}}
    @include('dashboard.screens.edit.capture-controls')
    {{-- / Control de la captura de pantalla --}}

    {{-- Listado de capturas que se han grabado --}}
    @include('dashboard.screens.edit.captures-list')
    {{-- / Listado de capturas que se han grabado --}}

    {{-- Botones de acción --}}
    @include('dashboard.screens.edit.action-buttons')
    {{-- /Botones de acción --}}

</div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
    {{-- Moment.js --}}
    <script src="@asset('assets/js/libs/moment-with-locales.min.js')"></script>

    {{--filesize plugin--}}
    <script src="@asset('assets/js/libs/filesize.min.js')"></script>

    {{-- Vuelidate plugin for vue --}}
    <script src="@asset('assets/js/libs/vuelidate.min.js')"></script>
    {{-- The builtin validators is added by adding the following line. --}}
    <script src="@asset('assets/js/libs/validators.min.js')"></script>

    <!-- Load Vue followed by BootstrapVue -->
    <script src="@asset('assets/js/vue/bootstrap-vue.js')"></script>

    {{-- Vue Treeselect --}}
    <script src="@asset('assets/js/vue/vue-treeselect.umd.min.js')"></script>

    {{-- Screen View --}}
    <script src="@mix('assets/js/dashboard/screen/screen.js')"></script>
@stop


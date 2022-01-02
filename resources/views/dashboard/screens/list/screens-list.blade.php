{{--
    Vista para mostrar las grabaciones que he realizado
--}}

@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title')
    @lang('Workspace → Listado de grabaciones de pantalla')
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
    @lang('Listado de sus grabaciones de pantalla')
    <div class="page-title-subheading">
        <p>
            @lang('Aquí puede revisar y editar sus grabaciones').
        </p>
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
    @include('dashboard.modals.screens.delete-capture')
    {{-- /Las modals necesarias --}}
  
    {{-- Data que se le pasa a Vue --}}
    @include('dashboard.screens.list.data')
    {{-- /Data que se le pasa a Vue --}}

    {{-- Botones de acción --}}
    @include('dashboard.screens.list.action-buttons')
    {{-- /Botones de acción --}}

    {{-- Listado de capturas que se han grabado --}}
    @include('dashboard.screens.list.captures-list')
    {{-- / Listado de capturas que se han grabado --}}

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

    {{-- Screens List View --}}
    <script src="@mix('assets/js/dashboard/screen/screen-list.js')"></script>
    
@stop


@extends('workspace.layouts.no-signer')

{{-- Etiquetas meta personalizadas --}}
@section('meta')
    <meta property="og:title" content="{{ $sharing->name }}"/>
    <meta property="og:description"
          content="@lang('Tu archivo está disponible para su descarga en :app', ['app' => config('app.name')])"/>
    <meta property="og:type" content="{{ $sharing->type }}"/>
    <meta property="og:url" content="@current"/>
    <meta property="og:image" content="@asset('/assets/images/dashboard/logos/fikrea-medium-logo.png')"/>
@stop

{{-- Título de la Página --}}
@section('title', @config('app.name'))

@section('help')
    <div>
        <div class="text-info small">
            @lang('Compartición: :title', [ 'title' => $sharing->title ])
        </div>
        @if( $contact )
            @lang('Hola, :name <a href="mailto::email">:email</a>', [
                'name'  => $contact->name ?? $contact->email,
                'email' => $contact->email,
            ])
        @else
            @lang('Hola. ¡Bienvenido a Fikrea!')
        @endif
        <div class="page-title-subheading">
            {{ $sharing->description }}
        </div>
    </div>
@endsection

{{-- Css Personalizado --}}
@push('page-styles')
    <link href="@asset('assets/css/dashboard/vendor/main.css')" rel="stylesheet"/>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet"/>

    <link href="@asset('assets/css/vendor/holdon/HoldOn.min.css')" rel="stylesheet"/>

    <link href="@asset('assets/css/vendor/fontawesome/css/all.min.css')" rel="stylesheet"/>

    <link href="@asset('assets/css/bootstrap.min.css')" rel="stylesheet"/>
    <link href="@asset('assets/css/vendor/vue/bootstrap-vue.css')" rel="stylesheet"/>

    <link href="@asset('assets/css/vendor/toastr/toastr.min.css')" rel="stylesheet"/>

    {{-- Css personalizado del Dashboard --}}
    <link href="@mix('assets/css/dashboard/style.css')" rel="stylesheet"/>

    {{-- Css para botón scroll-up --}}
    <link rel="stylesheet" type="text/css" href="@mix('assets/css/common/scroll-to-top.css')">

    {{-- Css Personalizado--}}
    {{-- Estos tienen que estar 100%--}}
    <link rel="stylesheet" href="@mix('/assets/css/landing/share.css')"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatable.css') }}">
@endpush

{{--
    El contenido de la página
--}}
@section('content')
    <div class="row">
        <div class="col-12 col-lg-6">
            <x-user-info :creator="$sharing->user" :message="Lang::get('Archivo compartido por')"></x-user-info>
        </div>
        <div class="col-12 col-lg-6">
            <a href="{{ is_a($file, \App\Models\File::class) ? route('file.download', ['id' => $sharing->token]) : route('file.set.download', ['id' => $sharing->token]) }}"
               class="btn btn-primary btn-primary-2 m-1 p-3 px-xl-5 py-xl-3 pull-right">
                <span class="btn-icon btn-block"><i class="fas fa-cloud-download-alt fa-4x"></i></span>
                <div class="btn-block">
                    @lang('Descarga tu archivo')
                    [<span class="text-warning bold">@filesize( $sharing->size )</span>]
                </div>
            </a>
        </div>
    </div>
    {{--@section('content')--}}
    <div v-cloak id="app">
        <div class="row">
            <div class="col-12">
                {{-- Las modales necesarias --}}
                @include('dashboard.modals.files.shared-file-preview')
                {{-- /Las modales necesarias --}}

                {{-- Descarga de Archivo --}}
                {{-- Botón que ofrece la descarga del archivo --}}
                <div class="col-sm-12 text-center mt-5">
                </div>
                {{--/Botón que ofrece la descarga del archivo --}}
                <div class="mt-3">
                    @includeWhen(!empty($files), 'includes.file_list_datatable', ['files' => $files])
                </div>
            </div>
        </div>
    </div>
@stop

{{-- Los scripts personalizados --}}
@push('page-scripts')
    {{-- Consentimiento de Cookies --}}
    <script src="@asset('assets/js/landing/vendor/cookieconsent.js')"></script>
    <script src="@mix('assets/js/landing/cookies.js')"></script>
    <script src="@mix('assets/js/dashboard/files/shared-files.js')"></script>
    <script src="@mix('assets/js/datatables.js')"></script>
    <script src="@mix('assets/js/common/static-data-datatable.js')"></script>
@endpush
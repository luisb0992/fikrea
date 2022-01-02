@extends('workspace.layouts.no-signer')

{{-- Etiquetas meta personalizadas --}}
@section('meta')
    <meta property="og:title" content="{{ $sharing->name }}" />
    <meta property="og:description"
        content="@lang('Tus documentos están disponibles para su descarga en :app', ['app' => config('app.name')])" />
    <meta property="og:type" content="{{ $sharing->type }}" />
    <meta property="og:url" content="@current" />
    <meta property="og:image" content="@asset('/assets/images/dashboard/logos/fikrea-medium-logo.png')" />
@stop

{{-- Título de la Página --}}
@section('title', @config('app.name'))

@section('help')
    <div>
        <div class="text-info small">
            @lang('Compartición: :title', [ 'title' => $sharing->title ])
        </div>
        @if ($contact)
            @lang('Hola, :name <a href="mailto::email">:email</a>', [
                'name' => $contact->name ?? $contact->email,
                'email' => $contact->email,
            ])
        @else
            @lang('Hola. ¡Bienvenido a :app!', ['app' => config('app.name')])
        @endif
        <div class="page-title-subheading">
            {{ $sharing->description }}
        </div>
    </div>
@endsection

{{-- Css Personalizado --}}
@push('page-styles')
    <link href="@asset('assets/css/dashboard/vendor/main.css')" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" />

    <link href="@asset('assets/css/vendor/holdon/HoldOn.min.css')" rel="stylesheet" />

    <link href="@asset('assets/css/vendor/fontawesome/css/all.min.css')" rel="stylesheet" />

    <link href="@asset('assets/css/bootstrap.min.css')" rel="stylesheet" />
    <link href="@asset('assets/css/vendor/vue/bootstrap-vue.css')" rel="stylesheet" />

    <link href="@asset('assets/css/vendor/toastr/toastr.min.css')" rel="stylesheet" />

    {{-- Css personalizado del Dashboard --}}
    <link href="@mix('assets/css/dashboard/style.css')" rel="stylesheet" />

    {{-- Css para botón scroll-up --}}
    <link rel="stylesheet" type="text/css" href="@mix('assets/css/common/scroll-to-top.css')">

    {{-- Css Personalizado --}}
    {{-- Estos tienen que estar 100% --}}
    <link rel="stylesheet" href="@mix('/assets/css/landing/share.css')" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatable.css') }}">
@endpush

{{-- El contenido de la página --}}
@section('content')
    <div class="row">
        <div class="col-12 col-md-6">
            <x-user-info :creator="$sharing->user" :message="Lang::get('Archivo compartido por')"></x-user-info>
        </div>
        <div class="col-12 col-md-6">
            <a href="{{ route('workspace.document.shared.download', ['token' => $documentSharing->token]) }}"
                class="btn btn-primary btn-primary-2 m-1 p-3 px-xl-5 py-xl-3 pull-right">
                <span class="btn-icon btn-block"><i class="fas fa-cloud-download-alt fa-4x"></i></span>
                <div class="btn-block">
                    @lang('Descarga tus documentos')
                    [<span class="text-warning bold">@filesize( $sharing->size )</span>]
                </div>
            </a>
        </div>
        <div class="col-12 col-md-12 my-4">
            <div class="main-card card">
                <h5 class="px-4 pt-4">@lang('Documentos compartidos')</h5>
                <div class="card-body">
                    <div class="col-12">
                        <div>
                            <label class="font-weight-bold">@lang('Título'): </label>
                            {{ $documentSharing->title ?? \Lang::get('Sin titulo') }}
                        </div>
                        <hr>
                        <div class="mb-4">
                            <label class="font-weight-bold">@lang('Descripción'): </label>
                            {{ $documentSharing->description ?? \Lang::get('Sin descripción') }}
                        </div>
                        <div>
                            <h6 class="text-muted">
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    <i class="fas fa-info-circle"></i>
                                    @lang('Este documento puede venir acompañado por múltiples archivos, como un certificado
                                    o archivos de audio y video.')
                                </div>
                            </h6>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="mb-0 table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('Documento')</th>
                                    <th>@lang('Tipo')</th>
                                    <th>@lang('Páginas')</th>
                                    <th>@lang('Tamaño') (kB)</th>
                                    <th>@lang('Creado') (kB)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td data-label="#">1</td>
                                    <td data-label="@lang('Documento')" class="text-info">
                                        {{ $documentSharing->document->name }}</td>
                                    <td data-label="@lang('Tipo')">
                                        @include('dashboard.partials.file-icon', ['type' =>
                                        $documentSharing->document->type])
                                    </td>
                                    <td data-label="@lang('Páginas')">{{ $documentSharing->document->pages }}</td>
                                    <td data-label="@lang('Tamaño')">@filesize($documentSharing->document->size)</td>
                                    <td data-label="@lang('Creado')">@datetime($documentSharing->created_at)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
    <script src="{{ mix('assets/js/datatables.js') }}"></script>
    <script src="{{ mix('assets/js/common/static-data-datatable.js') }}"></script>
@endpush

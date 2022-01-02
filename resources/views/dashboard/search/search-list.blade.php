@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
    <div>
        @if ($documents->count() + $files->count() > 0)
            @lang('Se proporcionan :results resultados para la búsqueda <em>:query</em>', [
                'results'   => $results,
                'query'     => $query,
            ])
        @else
            @lang('No hay ningún resultado que satisfaga la búsqueda')
            <div class="page-title-subheading">
                @lang('Cambiando el texto de búsqueda podría obtener resultados')
            </div>
        @endif
    </div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')

    <div id="app" class="col-md-12">

        @if ($documents->count() > 0)
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Documentos para Firma')</h5>
                </div>

                {{-- Tabla de documentos --}}
                @include('dashboard.partials.documents-table')
                {{--/Tabla de documentos --}}
            </div>
        @endif

        @if ($files->count() > 0)
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Archivos Subidos')</h5>
                </div>

                {{-- Tabla de archivos --}}
                @include('dashboard.files.files-table', ['selection' => false])
                {{--/Tabla de archivos --}}
            </div>
        @endif

    </div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
@stop
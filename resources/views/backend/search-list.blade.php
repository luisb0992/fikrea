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
    @if ($results > 0)
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

    @if ($results > 0)
    <div class="main-card mb-3 card">
        <div class="card-body">
            <h5 class="card-title">@lang('Usuarios encontrados')</h5>
            {{-- Tabla de usurios --}}
            @include('backend.partials.table-users')
            {{--/Tabla de usuario --}}
        </div>
    </div>
    @endif

</div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
@stop
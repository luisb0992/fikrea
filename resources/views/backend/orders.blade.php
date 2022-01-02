@extends('backend.layouts.main')

{{-- Título de la Página --}}
@section('title')
@lang('Administración → Listado de facturas')
@endsection

{{-- Css Personalizado --}}
@section('css')
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @lang('Lista de facturas emitidas por :app', ['app' => config('app.name')])
    <div class="page-title-subheading">
        @lang('Se muestran todos los pagos recibidos')
    </div>
</div>
@stop

{{-- 
    Aquí incluímos el contenido de la página
--}}
@section('content')
<div class="col-md-12">

    {{-- Muestra la lista de facturas --}}
    <div class="main-card mb-3 card">
        <div class="card-body">
            <h5>@lang('Lista de Facturas')</h5> 
            @include('backend.partials.table-orders')
        </div>
    </div>
    {{--/Muestra la lista de facturas --}}

</div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
@stop
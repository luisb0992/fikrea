@extends('dashboard.layouts.main')

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
    @lang('Se muestra la lista de visitas realizadas para satisfacer la solicitud de documentos')
    <div class="page-title-subheading">
        @lang('Aquí se listan las veces que las personas han entrado a su área de trabajo para atender a la solicitud')
    </div>
</div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')
<div id="app" class="col-md-12">

 {{-- Información de la solicitud de documentos --}}
 <div class="col-md-12 mt-4">
    <h5>
        <i class="fas fa-thermometer-half text-info"></i>
        @lang('Estado de la Solicitud')
    </h5>
</div>

    {{-- Estado de la solicitud --}}
    <div class="main-card card">

        <div class="card-body table-responsive">

            <div class="progress mb-2">
                <div class="progress-bar progress-bar-animated bg-primary progress-bar-striped" role="progressbar" 
                    aria-valuenow="{{$request->progress}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$request->progress}}%;">
                    {{$request->progress}} %
                </div>
            </div>

            {{--Tabla que muestra el estado de la solicitud--}}
            @include('dashboard.requests.history.status-table')
            {{--/Tabla que muestra el estado de la solicitud--}}

        </div>
    </div>
    {{--/Estado de la solicitud --}}


    {{-- Listado de visitas de los firmantes --}}
    <div class="col-md-12 mt-4">
        <h5>
            <i class="fas fa-history text-info"></i>
            @lang('Listado de visitas de los firmantes')
        </h5>
    </div>

    <div class="col-md-12 mb-2">
        <div class="main-card card">

            {{--Tabla con histórico de la solicitud de documento--}}
            @include('dashboard.requests.history.history-table')
            {{--/Tabla con histórico de la solicitud de documento--}}

        </div>
    </div>
    {{--/ Listado de visitas de los firmantes --}}

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
@stop
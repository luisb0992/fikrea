{{--
    Listado de visitas realizadas por los firmantes del documento
    en su área de trabajo donde firmarán y validarán su identidad
--}}

@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@section('css')
<link rel="stylesheet" href="@mix('assets/css/dashboard/document/history.css')" />
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @lang('Puede ver el histórico de visitas de los firmantes del documento')
    <div class="page-title-subheading">
        @lang('Aquí se listan las veces que los firmantes han entrado a su área de trabajo para realizar las firmas y validaciones correspondientes a su solicitud de firma.')
    </div>
</div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')

<div  id="app" class="col-md-12">
    
    {{-- Información del documento --}}
    <div class="col-md-12 mt-4">
        <h5>
            <i class="fas fa-info text-info"></i>
            @lang('Información del documento')
        </h5>
    </div>

    <div class="col-md-12">
        <div class="main-card card">

            <div class="card-body table-responsive">
                @include('dashboard.partials.documents-table',
                    [
                        'documents' => [$document],
                    ] 
                )
            </div>
        </div>
    </div>
    {{--/Información del documento --}}

    {{-- Listado de visitas de los firmantes --}}
    <div class="col-md-12 mt-4">
        <h5>
            <i class="fas fa-history text-info"></i>
            @lang('Listado de visitas de los firmantes')
        </h5>
    </div>

    <div class="col-md-12 mb-4">
        <div class="main-card card">

            <div class="card-body table-responsive">
                <div class="table-responsive col-md-12">    
                    <table class="mb-0 table table-striped">
                        <thead>
                            @include('dashboard.documents.history.header-history-table')
                        </thead>
                        <tbody>
                            @forelse($document->visits as $visit)
                                <tr>
                                    <th scope="row" data-label="@lang('Visita') #">{{$loop->iteration}}</th>

                                    <td data-label="@lang('Firmante')">
                                        {{$visit->signer->name}} {{$visit->signer->lastname}}
                                        <div>
                                            @if ($visit->signer->email)
                                                <a href="mailto:{{$visit->signer->email}}">{{$visit->signer->email}}</a>
                                            @else
                                                <a href="tel:{{$visit->signer->phone}}">{{$visit->signer->phone}}</a>
                                            @endif
                                        </div>
                                    </td>
                
                                    <td class="text-nowrap" data-label="@lang('Hora Inicio')">
                                        @if ($visit->starts_at)
                                            @date($visit->starts_at)
                                            <div class="text-info">
                                                @time($visit->starts_at)
                                            </div>
                                        @endif
                                    </td>

                                    <td class="text-nowrap" data-label="@lang('Hora Fin')">
                                        @if ($visit->ends_at)
                                            @date($visit->ends_at)
                                            <div class="text-info">
                                                @time($visit->ends_at)
                                            </div>
                                        @else
                                            <span class="text-danger nowrap bold">@lang('No Completado')</span>
                                        @endif
                                    </td>

                                    <td data-label="@lang('Duración')" class="text-center">
                                        <div class="text-secondary">{{gmdate('i:s', $visit->duration)}}</div>
                                    </td>

                                    <td data-label="@lang('Proceso')" class="text-secondary">
                                        @switch ($visit->request)
                                            @case('workspace.validate.signature.page')
                                                @lang('Firma de documento')
                                                @break
                                            @case('workspace.validate.audio')
                                            @case('dashboard.audio')
                                                @lang('Validación por Audio')
                                                @break
                                            @case('workspace.validate.video')
                                            @case('dashboard.video')
                                                @lang('Validación por Video')
                                                @break
                                            @case('workspace.validate.screen')
                                            @case('dashboard.screen')
                                                @lang('Validación por Captura de Pantalla')
                                                @break
                                            @case('workspace.validate.passport')
                                            @case('dashboard.passport')
                                                @lang('Validación por Documento Identificativo')
                                                @break
                                            @case('workspace.document.request')
                                                @lang('Solicitud de documentos')
                                                @break
                                            @case('workspace.validate.formdata')
                                                @lang('Certificación de datos')
                                                @break
                                            @case('workspace.validate.textboxs')
                                                @lang('Editor de documento')
                                                @break
                                            @default
                                                {{$visit->request}}
                                        @endswitch
                                    
                                    </td>
            
                                    <td data-label="@lang('Ip')">
                                        <div class="text-info">
                                            @ip($visit->ip)
                                        </div>
                                    </td>

                                    <td data-label="@lang('Sistema')">
                                        @if ($visit->device)
                                    	<div class="text-info bold">
                                    		@userdevice($visit->device)
                                    	</div>
                                        @endif
                                    	<div>
                                    		{{--  @useragent($visit->user_agent)  --}}
                                    	</div>
                                    </td>

                                    <td data-label="@lang('Ubicación')" class="text-center">
                                        <div>
                                            @if ($visit->latitude && $visit->longitude)
                                            <a target="_blank" class="btn btn-primary square" href="https://www.google.com/maps/search/?api=1&query={{$visit->latitude}},{{$visit->longitude}}">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </a>
                                            @else
                                                <div class="text-center">
                                                    <div class="text-danger bold p-2">@lang('No se ha obtenido')</div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-danger bold">
                                        @lang('Ninguna visita registrada')
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                        <tfoot>
                            @include('dashboard.documents.history.header-history-table')
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{--/ Listado de visitas de los firmantes --}}
    
</div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')

{{--  NOTA: no se utliza este archivo en lo abosluto ni sus metodos,
    solo se incluye para generar una instancia de Vue necesaria para ciertos procesos  --}}
<script src="@mix('assets/js/dashboard/documents/status.js')"></script>

@stop
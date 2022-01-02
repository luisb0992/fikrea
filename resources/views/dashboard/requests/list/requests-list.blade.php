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
        @lang('Estas son las solicitudes de documentos que ha enviado')
        <div class="page-title-subheading">
            <div>
                @lang('Para verificar el estado actual de una solicitud puede usar el botón')
                <i class="fas fa-thermometer-half"></i>
            </div>
        </div>
    </div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')

{{-- El mensaje flash que se muestra cuando la operación ha tenido éxito o error --}}
<div class="offset-md-3 col-md-6">
    @include('dashboard.sections.body.message-success')
    @include('dashboard.sections.body.message-error')
</div>
{{--/El mensaje flash que se muestra cuando la operacion ha tenido éxito o error --}}

<div id="app" class="col-md-12">

    {{-- Uso del espacio disponible --}}
    @include('dashboard.partials.disk-space')
    {{--/Uso del espacio disponible --}}

    {{-- El listado de solicitudes de documentos--}}
    <div class="main-card mb-3 card">
        <div class="card-body">
            <h5 class="card-title">@lang('Solicitudes de Documentos')</h5>
            
            {{-- link para ir a una nuva solictud de documentos  --}}
            <div class="col-md-12 mb-2">
                <a href="@route('dashboard.document.request.edit')" class="btn btn-lg btn-primary square mt-1" title="@lang('Crear Nueva Solicitud De Documentos')">
                    <i class="far fa-newspaper"></i>
                    @lang('Nueva Solicitud')
                </a>
            </div>

            <div class="table-responsive col-md-12">
                <table class="mb-0 table table-striped">
                    <thead>
                        @include('dashboard.requests.list.header-requests-list')
                    </thead>
                    <tbody>
                        @forelse ($documents as $document)
                        <tr>
                            <td data-label="@lang('Solicitud') #">{{$loop->iteration}}</td>
                            <td data-label="@lang('Nombre')">{{$document->name}}</td>
                            <td data-label="@lang('Comentarios')">{!!$document->comment!!}</td>
                            <td data-label="@lang('Usuarios')">
                                @foreach ($document->signers as $signer)
                                <div>
                                    {{--  {{$signer->token}}  --}}
                                    {{$signer->lastname}} {{$signer->name}}
                                    @if ($signer->email)
                                        <a href="mailto:{{$signer->email}}">  {{$signer->email}}</a>
                                    @else
                                        <a href="tel:{{$signer->phone}}">  {{$signer->phone}}</a>
                                    @endif
                                </div>
                                @endforeach
                            </td>
                            <td data-label="@lang('Documentos')">
                                @foreach ($document->documents as $userDocument)
                                <div>
                                    {{$userDocument->name}}
                                </div>
                                @endforeach        
                            </td>
                            <td data-label="@lang('Progreso')">
                                {{-- Progreso de la solicitud, animado cuando se está atendiendo por un firmante --}}
		                        @if ($document->isActive())
		                        <div class="progress" style="height: 10px;"
		                            data-toggle="tooltip"
                                    data-html="true"
		                            data-placement="top"
		                            data-original-title="{{$document->getActivity()}}"
		                        >
		                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info w-100" role="progressbar"></div>
		                        </div>
                                <div class="text-secundary animated infinite pulse text-center">
                                    {!! $document->getActivity() !!}
                                </div>
		                        @else
	                        	<progress min="0" max="100" value="{{$document->progress}}"></progress>
	                            <div class="text-center bold">{{$document->progress}} %</div>
		                        @endif
		                        {{-- /Progreso de la solicitud, animado cuando se está atendiendo por un firmante --}}      
                            </td>
                            
                            <td class="align-middle text-nowrap" data-label="@lang('Creado')">
                                @date($document->created_at)
                                <div class="text-info">
                                    @time($document->created_at)
                                </div>
                            </td>

                            <td class="text-center">

                                {{-- Estado --}}
                                <a href="@route('dashboard.document.request.status', ['id' => $document->id])" class="btn btn-primary square" data-toggle="tooltip" data-placement="top" data-original-title="@lang('Consultar estado de la Solicitud')">
                                    <i class="fas fa-thermometer-half"></i>
                                </a>

                                {{-- Histórico en solicitud de documentos--}}
                                @if ($document->visits->isEmpty())
                                    <a href="#!"  class="btn btn-warning square"
                                            data-toggle="tooltip" data-placement="top" data-original-title="@lang('Ninguna visita registrada')">
                                        <i class="fas fa-eye-slash"></i>
                                    </a>
                                @else
                                    <a href="@route('dashboard.document.request.history', ['id' => $document->id])" class="btn btn-warning square" data-toggle="tooltip" data-placement="top" data-original-title="@lang('Histórico de la Solicitud')">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endif
                                {{-- / Histórico en solicitud de documentos--}}

                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-danger bold">
                                @lang('No tiene solicitudes')
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        @include('dashboard.requests.list.header-requests-list')
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{--/El listado de solicitudes de documentos--}}

    {{-- Control de la Tabla --}}
    <div class="control-wrapper">

        {{--Paginador --}}
        @if ($documents->total() > config('documents.pagination'))
        <div class="paginator-wrapper">
            {{$documents->links()}}

            @lang('Se muestran :files de un total de :total archivos', [
                'files' => $documents->count(),
                'total' => $documents->total(),
            ])

        </div>
        @endif
        {{--/Paginador --}}
 
    </div>
    {{--/Control de la Tabla --}}

</div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
@stop
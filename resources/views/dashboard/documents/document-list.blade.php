@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@section('css')
@stop

{{--  --}}
@section('help')
    @if (request()->is('dashboard/document/list'))
        {{-- Ayuda cuando se muestra la lista de mis documentos --}}
        <div>
            @lang('Estos son sus documentos que puede compartir o ya ha compartido para firmar')
            <div class="page-title-subheading">
                <div>
                    @lang('Puede proceder a enviar un documento para su validación y firma utilizando el botón')
                    <i class="fas fa-signature"></i>
                </div>
                <div>
                    @lang('Para verificar el estado actual de un documento enviado use el botón')
                    <i class="fas fa-thermometer-half"></i>
                </div>
            </div>
        </div>
    @elseif (request()->is('dashboard/document/sent'))
        {{-- Ayuda cuando se muestra la lista de documentos enviados --}}
        <div>
            @lang('Esta es la lista de documentos que ha enviado para firmar')
            <div class="page-title-subheading">
                @lang('Puede consultar el estado de validación de los mismos haciendo click')
                <i class="fas fa-thermometer-half"></i>
            </div>
        </div>
    @elseif (request()->is('dashboard/document/trash'))
        {{-- Ayuda cuando se muestra la lista de documentos eliminados --}}
        <div>
            @lang('Estos son sus documentos eliminados')
            <div class="page-title-subheading">
                @lang('Si lo desea puede recuperarlos en cualquier momento')
            </div>
        </div>
    @endif
@stop

{{-- El contenido de la página --}}
@section('content')

    {{-- El mensaje flash que se muestra cuando la operación ha tenido éxito o error --}}
    <div class="offset-md-3 col-md-6">
        @include('dashboard.sections.body.message-success')
        @include('dashboard.sections.body.message-error')
    </div>
    {{-- /El mensaje flash que se muestra cuando la operacion ha tenido éxito o error --}}

    <div v-cloak id="app" class="col-md-12">

        {{-- Modales --}}

        {{-- Modal de confirmación de eliminación del documento --}}
        @include('dashboard.modals.documents.remove-document-confirmation')
        @include('dashboard.modals.documents.destroy-document-confirmation')
        {{-- /Modales --}}

        {{-- Uso del espacio disponible --}}
        @include('dashboard.partials.disk-space')
        {{-- /Uso del espacio disponible --}}

        {{-- El listado de documentos --}}
        <div class="main-card mb-3 card">
            <div class="card-body">

                @if (request()->is('dashboard/document/list'))
                    <h5 class="card-title">@lang('Documentos para Firmar')</h5>
                @elseif (request()->is('dashboard/document/sent'))
                    <h5 class="card-title">@lang('Documentos Enviados')</h5>
                @elseif (request()->is('dashboard/document/trash'))
                    <h5 class="card-title">@lang('Documentos Eliminados')</h5>
                @endif

                {{-- Botones de seleccion de archivo y crear una nota --}}
                @if (request()->is('dashboard/document/list'))
                    <div class="col-md-12 input-group actions">
                        <a href="@route('dashboard.file.list')" class="btn btn-lg btn-primary square mr-1 mt-1 mb-2"
                            data-toggle="tooltip" data-original-title="@lang('Seleccionar un archivo subido')">
                            <i class="fas fa-file-import"></i>
                            @lang('Seleccionar')
                        </a>
                        <a href="@route('dashboard.document.edit')" class="btn btn-lg btn-primary square mt-1 mb-2"
                            data-toggle="tooltip" data-original-title="@lang('Crea una nota para firmar')">
                            <i class="fas fa-edit"></i>
                            @lang('Crea Nota')
                        </a>
                    </div>
                @endif
                {{-- /Botones de seleccion de archivo y crear una nota --}}

                {{-- Rutas de la solicitudes --}}
                <div id="requests" data-remove-document-request="@route('dashboard.document.delete')"
                    data-destroy-document-request="@route('dashboard.document.destroy')">
                </div>
                {{-- /Rutas de las solicitudes --}}

                {{-- Tabla de documentos --}}
                @include('dashboard.partials.documents-table')
                {{-- /Tabla de documentos --}}

            </div>
        </div>
        {{-- /El listado de documentos --}}

        {{-- Control de la Tabla --}}
        <div class="control-wrapper">

            {{-- Paginador --}}
            @if ($documents->total() > config('documents.pagination'))
                <div class="paginator-wrapper">
                    {{ $documents->links() }}

                    @lang('Se muestran :files de un total de :total archivos', [
                    'files' => $documents->count(),
                    'total' => $documents->total(),
                    ])

                    <span class="bold">@{{ documents . length }}</span>
                    @lang('archivos seleccionados')

                </div>
            @endif
            {{-- /Paginador --}}

        </div>
        {{-- /Control de la Tabla --}}

    </div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
    <script src="@mix('assets/js/dashboard/documents/list.js')"></script>
@stop


    {{-- El registro de la visita del usuario --}}
    <div id="visit" data-visit="@json($visit)"></div>
    {{--/El registro de la visita del usuario --}}

    {{-- Las modales necesarias --}}
    @include('common.modals.more-files-for-request')
    {{--/Las modales necesarias --}}

    {{-- Data pasada a Vue --}}
    <div id="data"
        data-documents="@json($request->documents)"
    ></div>
    {{--/Data pasada a Vue --}}

    {{-- Mensajes de error --}}
    <div id="messages"
        data-file-type-error="@lang('Este archivo no tiene extensión. Revise que sea el archivo correcto, y que sea del tipo de archivo esperado')"
        data-file-too-big="@lang('El archivo es demasiado grande')"
        data-file-aported="@lang('Se ha adicionado el archivo para ser aportado correctamente')"
        data-file-not-valid="@lang('El tipo de archivo no es válido para esta solicitud')"
        data-file-to-non-aported="@lang('Debe volver a seleccionar el o los archivos para cumplir con la solicitud del documento')"
    ></div>
    {{--/Mensajes de error --}}

    {{-- Los botones de Acción --}}
    @include('common.request.action-buttons')
    {{--/Los botones de Acción --}}

    {{-- La lista de documentos solicitados --}}
    @include('common.request.requireds-documents-list')
    {{--/La lista de documentos solicitados --}}

    {{-- Formulario para aportar un documento --}}
    {{-- Si es el creador quien está aportando debo cargar otra vista --}}
    @if (request()->is('dashboard/document/request/*'))
        @include('dashboard.requests.creator.creator-contribute-file')
    @else
        @include('common.request.contribute-file')
    @endif
    {{-- / Formulario para aportar un documento --}}

    {{-- Una tabla con el listado de archivos cargados --}}
    @include('common.request.loaded-files')
    {{--/Una tabla con el listado de archivos cargados --}}

    {{-- Los botones de Acción --}}
    @include('common.request.action-buttons')
    {{--/Los botones de Acción --}}

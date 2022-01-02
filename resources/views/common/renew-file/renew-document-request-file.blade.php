	
	{{-- El registro de la visita del usuario --}}
    <div id="visit" data-visit="@json($visit)"></div>
    {{--/El registro de la visita del usuario --}}

    {{-- El registro de la data para vue js --}}
    <div id="data"
        data-documents="{{ $request->expiringDocuments()->toJson() }}"
    ></div>
    {{-- / El registro de la data para vue js --}}

    {{-- Mensajes de error --}}
    <div id="errors"
        data-file-too-big="@lang('El archivo es demasiado grande')"
        data-file-not-valid="@lang('El tipo de archivo no es válido para esta solicitud')">
    </div>
    {{--/Mensajes de error --}}

    {{-- Los botones de Acción --}}
    @include('common.request.action-buttons')
    {{--/Los botones de Acción --}}

    {{-- La lista de documentos solicitados --}}
    @include('common.renew-file.requireds-documents-list')
    {{--/La lista de documentos solicitados --}}

    {{-- Formulario para aportar un documento --}}
    @include('common.renew-file.contribute-file')
    {{-- / Formulario para aportar un documento --}}

    {{-- Una tabla con el listado de archivos cargados --}}
    @include('common.request.loaded-files')
    {{--/Una tabla con el listado de archivos cargados --}}
  
    {{-- Los botones de Acción --}}
    @include('common.request.action-buttons')
    {{--/Los botones de Acción --}}
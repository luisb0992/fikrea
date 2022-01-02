{{--
	Lista de rutas implicadas

    dashboard.document.request.save.signers : Guardar los firmantes  
    dashboard.document.request.list         : Lista de solicitudes de documentos         
--}}
<div id="requests"
    data-save-signers="@route('dashboard.document.request.save.signers', ['id' => $document->id])"
    data-list-of-requests="@route('dashboard.document.request.list')"
    data-generate-url-request="@route('dashboard.document.request.generate.url', ['id'=>$document->id])"
    data-save-generated-url-request="@route('dashboard.document.request.generate.url.save', ['id'=>$document->id])"   
></div>

<div id="messages"
	data-copied-to-clipboard="@lang('Se ha copiado la url al portapapeles')"
></div>
{{-- El registro de la visita del usuario --}}
<div id="visit" data-visit="@json($visit)"></div>
{{--/El registro de la visita del usuario --}}

{{-- Los mensajes de la aplicación --}}
<div id="messages" 
data-capture-record-success="@lang('La captura de pantalla ha finalizado con éxito')"
data-recording="@lang('Detenga la grabación antes de finalizar')"
data-cannot-finish="@lang('Usted no puede finalizar, aún no ha completado todas sus cajas de texto')"
data-must-init-screen-capture-recording="@lang('Debe comenzar la grabación de su pantalla antes de poder completar los textos')"
data-file-is-not-image="@lang('El archivo seleccionado no es una imagen válida')"
data-scale-not-allowed="@lang('Para configurar el documento, debe estar escalado a su tamaño normal')"
data-scale-for-sign="@lang('Para garantizar la integridad del proceso de firma, este documento debe estar escalado a su tamaño normal para poder completar las cajas de textos')"
data-process-document-title="@lang('Procesando el documento')"
data-process-document-text="@lang('Obteniendo las páginas...')"
data-creator-must-finished="@lang('Usted como creador debe completar todas sus cajas de texto para poder finalizar este proceso')"
data-max-length-achieved="@lang('Lo sentimos, pero ya se ha alcanzado la longitud máxima permitida para esta caja de texto')"
></div>
{{--/Los mensajes de la aplicación --}}

{{-- Data a Vue si estoy en desktop o no--}}
<div id="data"
@desktop
data-desktop="@json(true)"
@else
data-desktop="@json(false)"
@enddesktop
data-redirect-to-workspace-home="@route('workspace.home', ['token' => $signer->token])"
></div>
{{-- /Data a Vue si estoy en desktop o no--}}

{{-- Traducciones utilizadas en Vue js --}}
<div id="langs"
    data-langs="@json($langs)"
></div>
{{-- /Traducciones utilizadas en Vue js --}}
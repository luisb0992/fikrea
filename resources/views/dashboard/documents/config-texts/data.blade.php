<div class="row col-md-12 mt-2 ml mb-2">

    {{-- Los mensajes de la aplicación --}}
        <div id="messages" class="d-none"
            data-file-is-not-image="@lang('El archivo seleccionado no es una imagen válida')"
            data-scale-not-allowed="@lang('Para configurar el documento, debe estar escalado a su tamaño normal')"
            data-scale-for-sign="@lang('Para garantizar la integridad del proceso de firma, este documento debe estar escalado a su tamaño normal para poder completar las cajas de textos')"
            data-process-document-title="@lang('Procesando el documento')"
            data-process-document-text="@lang('Obteniendo las páginas...')"
            data-creator-must-finished="@lang('Usted como creador debe completar todas sus cajas de texto para poder finalizar este proceso')"
            data-max-length-achieved="@lang('Lo sentimos, pero ya se ha alcanzado la longitud máxima permitida para esta caja de texto')"
            data-complete-select-box="@lang('Debe completar las opciones de esta caja de texto. Seleccione la caja y adicione las opciones que crea conveniente.')"
            data-incomplete-select-boxes="@lang('Se han encontrado cajas de texto de tipo `Opciones` sin completar. Gestione las opciones que podrá elegir el firmante en esta caja.')"
        ></div>
    {{--/Los mensajes de la aplicación --}}

    {{-- La lista de firmantes del documento --}}
    <div id="signers" data-signers="@json($signers)"></div>
    {{--/La lista de firmantes del documento --}}

    {{-- Traducciones utilizadas en Vue js --}}
    <div id="langs"
        data-langs="@json($langs)"
    ></div>
    {{-- /Traducciones utilizadas en Vue js --}}

    {{-- Data a Vue si estoy en desktop o no--}}
    <div id="data"
        @desktop
        data-desktop="@json(true)"
        @else
        data-desktop="@json(false)"
        @enddesktop
    ></div>
    {{-- /Data a Vue si estoy en desktop o no--}}
    
</div>
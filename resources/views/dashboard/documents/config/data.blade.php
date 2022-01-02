<div class="row col-md-12 mt-2 ml mb-2">

    {{-- Los mensajes de la aplicación --}}
        <div id="messages" class="d-none"
            data-file-is-not-image="@lang('El archivo seleccionado no es una imagen válida')"
            data-scale-not-allowed="@lang('Para configurar el documento, debe estar escalado a su tamaño normal')"
            data-scale-for-sign="@lang('Para garantizar la integridad del proceso de firma, este documento debe estar escalado a su tamaño normal para poder firmarse')"
            data-process-document-title="@lang('Procesando el documento')"
            data-process-document-text="@lang('Obteniendo las páginas...')"
            data-creator-must-finished="@lang('Usted como creador debe firmar sobre todas sus firmas para poder finalizar este proceso')"
        ></div>
    {{--/Los mensajes de la aplicación --}}

    {{-- Las rutas de solicitudes --}}
    <div id="request"
        data-save-stamp="@route('dashboard.document.config.stamp.save')"
        data-remove-stamp="@route('dashboard.document.config.stamp.delete', ['id' => ':id'])">
    </div>
    {{--/Las rutas de solicitudes --}}

    {{-- La lista de firmantes del documento --}}
    <div id="signers" data-signers="@json($signers)"></div>
    {{--/La lista de firmantes del documento --}}

    {{-- Los sellos de los que dispone el usuario --}}
    <div id="stamps" 
        data-stamps="@json($user->stamps->makeHidden(['stamp']))"
        data-mimes="{{ $stampMimetypes }}"
        data-max-width="@config('stamps.size.width')"  
    ></div>
    {{--/Los sellos de los que dispone el usuario --}}
</div>
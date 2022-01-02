{{-- Data que se le pasa a Vue --}}

    {{-- Los mensajes de la aplicación --}}
    <div id="messages" 
        data-capture-not-found="@lang('No se ha encontrado la captura que desea modificar')"
        data-editing-ok="@lang('Se ha acualizado la grabación correctamente')"
        data-deleting-ok="@lang('Se ha eliminado la grabación correctamente')"
    ></div>
    {{--/Los mensajes de la aplicación --}}

    {{-- Data que se utiliza para seleccionar destino de archivo --}}
    <div id="data"
        data-captures="@json($screens)"
        data-file-system-tree="@json($fileSystemTreeselect)"
    ></div>
    {{-- /Data que se utiliza para seleccionar destino de archivo  --}}

    {{-- Data de peticiones --}}
    <div id="requests"
        data-update-request="@route('dashboard.screen.update', ['screen' => 'X'])"
        data-destroy-request="@route('dashboard.screen.destroy', ['screen' => 'X'])"
    ></div>
    {{-- /Data de peticiones  --}}

{{-- /Data que se le pasa a Vue --}}
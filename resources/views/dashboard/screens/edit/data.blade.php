{{-- Data que se le pasa a Vue --}}
    
    {{-- Los mensajes de la aplicación --}}
    <div id="messages" 
        data-capture-record-success="@lang('La captura de pantalla ha finalizado con éxito')"
        data-recording="@lang('Ha iniciado el proceso de grabación de su pantalla correctamente')"
        data-editing-ok="@lang('Se ha acualizado la grabación correctamente')"
        data-capture-not-found="@lang('No se ha encontrado la captura que desea modificar')"
    ></div>
    {{--/Los mensajes de la aplicación --}}

    {{-- Data que se utiliza para seleccionar destino de archivo --}}
    <div id="data"
        data-file-system-tree="@json($fileSystemTreeselect)"
    ></div>
    {{-- /Data que se utiliza para seleccionar destino de archivo  --}}

    {{-- Data de peticiones --}}
    <div id="requests"
        data-save-screen="@route('dashboard.screen.save')"
    ></div>
    {{-- /Data de peticiones  --}}

{{-- /Data que se le pasa a Vue --}}
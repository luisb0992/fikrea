{{-- Los datos de la app para vue js --}}
    {{-- El registro de la visita del usuario --}}
    <div id="visit" data-visit="@json($visit)"></div>
    {{--/El registro de la visita del usuario --}}

    {{-- Data para vue js --}}
    <div id="data"
        data-use-face-recognition="@json($useFacialRecognition)"
        data-url="@config('app.url')"
    ></div>
    {{--/Data para vue js --}}

    {{-- Las rutas de la aplicación --}}
    @isset($token)
    <div id="requests"
        data-save="@route('workspace.save.passport', ['token' => $token])"
        data-redirect-after-save="@route('workspace.home', ['token' => $token])"
    ></div>
    @else
    <div id="requests"
        data-save="@route('dashboard.passport.save', ['id' => $signer->document->id])"
        data-redirect-after-save="@route('dashboard.document.status', ['id' => $signer->document->id])"
    ></div>
    @endisset
    {{--/Las rutas de la aplicación --}}

    {{-- Los mensajes de la aplicación --}}
    <div id="messages" class="d-none" 
        data-remember-include-passport-number="@lang('Recuerde introducir el número del documento')"
        data-file-is-not-valid-image="@lang('El archivo suministrado no es una imagen válida')"
        data-document-loaded="@lang('Se ha cargado el documento satisfactoriamente')"
        data-permission-denied="@lang('Se ha denegado el acceso a su dispositivo. Por favor revise la configuración de su navegador e inténtelo nuevamente.')"
        data-finding-webcam-ok="@lang('Dispositivo de video encontrado')"
        data-finding-webcam-ko="@lang('No hemos detectado ningún dispositivo de video para hacer las fotos necesarias en este proceso')"
        data-finding-webcam-title="@lang('Buscando dispositivo')"
        data-finding-webcam-text="@lang('Estamos detectando algún dispositivo de video en su equipo')"
    ></div>
    {{--/Los mensajes de la aplicación --}}
{{-- / Los datos de la app para vue js --}}
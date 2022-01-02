@extends('dashboard.layouts.mail')

@section('content')
    {{--En caso de existir el contacto--}}
    @if (isset($signer))
        <p>
            @lang('Hola :usuario que tal va todo??', ['usuario' => "{$signer->name}"])
        </p>
    @endif
    {{--/En caso de existir el contacto--}}

    <p>
        @lang('Te hacemos llegar este email a petición de :usuario.', ['usuario' => "{$creator->name} {$creator->lastname} {$creator->company} {$creator->position}"])
    </p>

    <p>
        @lang('A continuación encontrará un botón que al pulsar le redirige a nuestro "workspace-zona de trabajo",
              donde visualizará la información de los procesos que usted tiene pendiente o que ya ha realizado.')
    </p>

    <p class="text-center">
       <a class="btn btn-primary" href="@url(@route('workspace.home', [
            'token'   => $signer->token,
            'sharing' => $sharing ? $sharing->id : null,
          ]))"
        >
          @lang('Acceder a su espacio de trabajo en :app', ['app' => @config('app.name')])
       </a>
    </p>

    <p>
        @lang('No olvide que con FIKREA, puede almacenar en la nube, compartir múltiples ficheros,
              solicitar documentación y tener control sobre su caducidad o fecha de emisión, además
              de nuestro sistema único de firma de documentos con proceso de validación de datos,
              grabación de audio o video, reconocimiento facial mediante documento identificable y mucho más.')
    </p>

    <p>
        @lang('Si estás interesado en saber más, visítanos en <a href="/">www.fikrea.com</a>, ! <b>La aplicación de gestión de documentos y archivos número 1</b>!')
    </p>

@stop

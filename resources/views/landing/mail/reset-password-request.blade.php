@extends('landing.layouts.mail')

@section('content')

    {{--
        Mensaje para recordar (resetear) la contraseña olvidada
        de un usuario
    --}}

    <p>
        Hola, <span class="text-bold text-warning">{{$forgetfulUser->name}}</span>:
    </p>

    <p>
        @lang('Un usuario ha solicitado la recuperación de la contraseña de acceso').
    </p>

    <p>
        @lang('A continuación, puede pulsar en el siguiente botón para acceder a una página que te permitirá cambiar la contraseña de acceso'):
    </p>
    <p class="text-center">
        <a class="btn btn-primary" 
            href="@url(@route('dashboard.password.change', ['rememberToken' => $token]))">
            @lang('Recuperar Contraseña')
        </a>
    </p>
    <p class="text-bold text-danger">
        @lang('Si no ha efectuado esta petición, simplemente ignore este mensaje').
    </p>
    <p>
        @lang('Un Saludo').
    </p>
@stop

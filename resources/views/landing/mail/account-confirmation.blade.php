@extends('landing.layouts.mail')

@section('content')

    {{--
        Mensaje de bienvenida para el usuario registrado
        que debe validar su cuenta
    --}}

    <p>
        Hola, <span class="text-bold text-warning">{{$account->name}}</span>:
    </p>

    <p>
        @lang('Le damos las gracias y la bienvenida por registrarse en :app', ['app' => @config('app.name')]).
    </p>

    <p>
        @lang('Para poder utilizar todas las prestaciones de la aplicación, tiene que acceder con los datos que ha proporcionado en el registro'):
    </p>
    
    <p>
        <span class="text-bold text-info">@lang('Usuario')</span> : {{$account->email}}
    </p>
    <p>
        <span class="text-bold text-info">@lang('Contraseña')</span> : *****************
    </p>

    <p>
        @lang('Para ello, primero, debe proceder a a activar tu cuenta de usuario').
        @lang('Simplemente debe pulsar en el siguiente botón'):
    </p>
    
    <p class="text-center">
        <a class="btn btn-primary" href="@url(@route('dashboard.verify.user', ['validationCode' => $code]))">
            @lang('Activar Cuenta')
        </a>
    </p>

    <p class="mt-50">
        @lang('Si no recuerda la contraseña de acceso a tu cuenta puedes hacer click en el siguiente botón'):
    </p>

    <p class="text-center">
        <a class="btn btn-primary" href="@url(@route('dashboard.rememberme'))">
            @lang('Recordar Contraseña')
        </a>
    </p>

    <p>
        @lang('Introduzca su dirección de correo :email y recibirá un mensaje para cambiar sus credenciales de acceso',
            ['email' => $account->email]
        )
    </p>

    <p>
        @lang('Un Saludo').
    </p>
@stop

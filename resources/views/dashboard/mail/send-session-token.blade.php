@extends('dashboard.layouts.mail')

@section('content')
    <p>
        @lang('Hola, :user:', ['user' => $oldUser->name])
    </p>

    <p>
        @lang('Ha solicitado recuperar el acceso a la información de una sesión anterior').
        @lang('Puede hacer click en el siguiente enlace para acceder a los archivos, contactos, firmas, etc.')
    </p>

    <p class="text-center">
        <a class="btn btn-primary" href="@url(@route('dashboard.profile.session.recovery', ['token' => $oldUser->guest_token]))">
            @lang('Acceder a su espacio de trabajo en :app', ['app' => @config('app.name')])
        </a>
    </p>

    <p class="text-bold text-danger">
        @lang('Si no espera este mensaje, simplemente ignórelo').
    </p>
@stop

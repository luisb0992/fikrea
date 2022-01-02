@extends('dashboard.layouts.mail')

@section('content')
    <p>
        @lang('Hola, :user :lastname <a href="mailto::email">:email</a>:', [
            'user'      => $contact->name,
            'lastname'  => $contact->lastname,
            'email'     => $contact->email,
        ]):
    </p>

    <p>
        @lang('El usuario :user quiere compartir un archivo con usted utilizando la aplicación de firma digital :app.',
            [
                'user'  => "{$user->name} {$user->lastname}",
                'app'   => config('app.name'), 
            ]
        )
        @lang('A continuación, puede pulsar en el siguiente botón para ir a la página de descarga de tu archivo').
    </p>

    <p class="text-center">
        <a class="btn btn-primary" 
            href="@url(@route('workspace.set.share', ['token' => $contact->token]))">
            @lang('Descargar el Archivo')
        </a>
    </p>
    <p class="text-bold text-danger">
        @lang('Si no esperas este archivo, simplemente ignora este mensaje').
    </p>
@stop

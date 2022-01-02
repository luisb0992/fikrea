@extends('dashboard.layouts.mail')

@section('content')
    <p>
        @lang('Hola')!
    </p>

    <p>
        @lang('El usuario :user quiere compartir sus datos de facturacion con usted utilizando la aplicación de firma digital
        :app.', [
        'user' => "{$user->name} {$user->lastname}",
        'app' => config('app.name'),
        ])
        @lang('A continuación, puede pulsar en el siguiente botón para ir a la página donde podrá ver los datos de
        facturación').
    </p>

    <p class="text-center">
        <a class="btn btn-primary" href="@route('billing.viewBillingData', ['token' => $company->token])">
            @lang('Acceder ha datos de facturacion')
        </a>
    </p>
    <p class="text-bold text-danger">
        @lang('Si no esperas este archivo, simplemente ignora este mensaje').
    </p>
@stop

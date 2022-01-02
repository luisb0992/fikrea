@extends('dashboard.layouts.mail')

@section('content')

    <p>
        @lang('Hola, :usuario', ['usuario' => $user->name])
    </p>

    <p>
        @lang('Le damos las gracias, una vez más, por confiar en nosotros').
        @lang('Su suscripción a :app ha sido renovada y ahora podrá disfrutar de nuestra aplicación durante más tiempo',
            ['app' => config('app.name')]
        ).
    </p>

    <table class="table">
        <tr>
            <td>@lang('Pedido') :</td>
            <td>{{$order->order}}</td>
        </tr>
        <tr>
            <td>@lang('Importe') :</td>
            <td>{{$order->amount}} @config('app.currency')</td>
        </tr>
        <tr>
            <td>@lang('Fecha Pago') :</td>
            <td>@date($order->payed_at)</td>
        </tr>
        <tr>
            <td>@lang('Subscripción Hasta') :</td>
            <td>@date($order->ends_at)</td>
        </tr>
    </table>

    <p>
        @lang('Puede acceder su zona de usuario en :app', ['app' => config('app.name')]):
    </p>

    <p class="text-center">
        <a class="btn btn-primary" href="@url(@route('dashboard.home'))">
            @lang('Acceder a su Zona de Usuario en :app', ['app' => config('app.name')])
        </a>
    </p>

    <p>
        @lang('Un Saludo').
    </p>
@stop
